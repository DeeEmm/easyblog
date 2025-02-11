<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/base.php');

class EasyBlogMigratorWordpress_xml extends EasyBlogMigratorBase
{
	public $namespaces = array();
	public $file = null;
	public $session;

	public function __construct()
	{
		$this->session = JFactory::getSession();

		parent::__construct();
	}

	/**
	 * Debug xml file
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function debug()
	{
		libxml_use_internal_errors(true);
		var_dump(simplexml_load_file($file));
		$errors = libxml_get_errors();
		var_dump($errors);
		foreach ($errors as $error) {
			var_dump($error);
		}
		libxml_clear_errors();
		exit;

	}

	/**
	 * Main method to process wordpress migrator
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function migrate($fileName, $authorId, $options = false)
	{
		$ajax = EB::ajax();
		$session = JFactory::getSession();

		$file = JPATH_ROOT . '/administrator/components/com_easyblog/xmlfiles/' . $fileName;
		$exists = JFile::exists($file);

		if (!$exists) {
			return $ajax->resolve('fileNotExist');
		}

		// Set the file
		$this->file = $file;

		// For debugging purposes only.
		// $this->debug();

		// Check if the xml file is valid
		$parser = simplexml_load_file($file);

		if (!$parser) {
			// one of the known issue is somewhere in your XML item title tag content contains &
			// need to manually change it to &amp;
			return $ajax->resolve('parseFailed');
		}

		// Get the base url
		$baseUrl = $parser->xpath('/rss/channel/wp:base_site_url');
		$baseUrl = (string) trim($baseUrl[0]);

		// Get namespaces
		$namespaces = $parser->getDocNamespaces();

		if (!isset($namespaces['wp'])) {
			$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
		}

		if (!isset($namespaces['excerpt'])) {
			$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
		}

		// Get the list of namespaces used in this feed
		$this->namespaces = $namespaces;

		$posts = array();
		$attachments = array();

		// Get the list of posts from the xml file
		$items = $parser->channel->item;

		// Pagination as we don't want to migrate everything at once
		$max = $parser->channel->item->count();
		$limit = 20;
		$current = $this->input->get('current', 0, 'int');
		$totalIteration = $current + $limit;

		$hasMore = true;

		if ($totalIteration > $max) {
			$totalIteration = $max;
			$hasMore = false;
		}

		$messages = array();

		for ($i = $current; $i < $totalIteration; $i++) {

			// Get the item to be migrated
			$item = $parser->channel->item[$i];

			// Migrate the item
			$result = $this->migrateItem($item, $authorId, $options);

			if ($result === "migrated") {
				$messages[] = JText::_('Item already migrated <br />');
			}

			if ($result === "invalid") {
				$messages[] = JText::_('Invalid item. Skiping... <br />');
			}

			if ($result === false) {
				$messages[] = JText::_('Error processing item <br />');
			}

			if (is_object($result)) {
				$messages[] = JText::sprintf('Migrated from WP: %1$s to EB: %2$s <br />', $result->wp_id, $result->eb_id);
			}
		}

		$ajax->append('[data-progress-status]', implode('', $messages));

		return $ajax->resolve('continueProcessing', $totalIteration, $hasMore);
	}

	/**
	 * Retrieves the list of xml file dump from Wordpress
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFiles()
	{
		jimport('joomla.filesystem.folder');

		$path = JPATH_ROOT . '/administrator/components/com_easyblog/xmlfiles';

		if (!JFolder::exists($path)) {
			return false;
		}

		$files = JFolder::files($path, '.xml');

		return $files;
	}

	/**
	 * Migrates a single post item from manifest file
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function migrateItem($item, $authorId, $wpOptions = false)
	{
		// Extract the data from the manifest
		$wp = $this->extractData($item, $wpOptions);

		if ($wp === false) {
			return "invalid";
		}

		// Ensure that there is a proper post id from wordpress
		if (!$wp->post_id) {
			return false;
		}

		// Check if this item is already migrated
		$migrated = $this->isMigrated($this->file, $wp->post_id);

		if ($migrated) {
			return "migrated";
		}

		// Map the post
		$data = $this->mapData($wp, $authorId);

		// lets create blank post which are legacy type.
		$post = EB::post();
		$post->create(array('overrideDoctType' => 'legacy', 'triggerPlugins' => false, 'overrideAuthorId' => $data->created_by));

		// now let get the uid
		$data->uid = $post->uid;
		$data->revision_id = $post->revision->id;

		// Bind the data now
		$post->bind($data, array());

		if ($wpOptions && $wpOptions->firstimagecover) {
			// save first image as cover photo if there is any
			$this->mapCoverImage($post, $wpOptions);
		}

		$options = array(
						'applyDateOffset' => false,
						'validateData' => false,
						'useAuthorAsRevisionOwner' => true,
						'triggerPlugins' => false,
						'skipNotifications' => true
					);

		$post->save($options);

		// Map tags
		$this->createTags($wp, $post);

		// Map comments
		$this->createComments($wp, $post);

		// Add log
		$this->addMigrationLog($wp->post_id, $post->id);

		$result = new stdClass();
		$result->wp_id = $wp->post_id;
		$result->eb_id = $post->id;

		return $result;
	}

	/**
	 * Import first image and save into local server as cover photo
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function mapCoverImage(EasyBlogPost &$post, $wpOptions)
	{
		$content = $post->intro . $post->content;

		$replacerFrom = $wpOptions->wpimagepathfrom;
		$replacerTo = $wpOptions->wpimagepathto;


		// we first check if there are any image or not.
		$pattern = '#<img[^>]*>#i';
		preg_match_all($pattern, $content, $matches);

		if (!$matches) {
			return false;
		}

		// okay there are images. lets determine if the image in intro or content.
		$sources = array('intro', 'content');

		$found = false;

		foreach ($sources as $source) {

			if ($found) {
				break;
			}

			$content = $post->{$source};

			// first occurance
			preg_match($pattern, $content, $matches);

			if ($matches) {

				$imgTag = $matches[0];

				preg_match('/src="([^"]+)"/', $imgTag, $img);

				$imgSrc = isset($img[1]) && $img[1] ? $img[1] : '';

				if ($imgSrc) {

					$actualSrc = $imgSrc;

					if ($replacerFrom && $replacerTo) {
						$actualSrc = str_replace($replacerFrom, $replacerTo, $imgSrc);
					}



					$cover = $this->downloadImage($actualSrc, $post);
					if ($cover !== false) {

						$post->image = $cover->uri;
						$content = str_replace($imgTag, '', $content);
						$post->{$source} = $content;

						$found = true;
					}
				}
			}

		}

	}

	/**
	 * Performs image download from the external wordpress site
	 *
	 * @since   5.2
	 * @access  public
	 */
	private function downloadImage($url, $post)
	{
		// Store the image on a temporary location
		$temporaryPath = JPATH_ROOT . '/tmp/' . md5($url);

		// Read the external image file
		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// Save the image to a temporary location
		JFile::write($temporaryPath, $contents);

		// Prepare the image data
		$file = @getimagesize($temporaryPath);
		if ($file === false || !isset($file['mime']) || !$file['mime']) {
			// invalid image file
			return false;
		}

		$file['name'] = basename($url);
		$file['tmp_name'] = $temporaryPath;
		$file['type'] = $file['mime'];

		$media = EB::mediamanager();
		$uri = 'user:' . $post->created_by;

		$adapter = $media->getAdapter($uri);
		$result = $adapter->upload($file);

		// Delete the temporary file
		JFile::delete($temporaryPath);

		if (!isset($result->type)) {
			return false;
		}

		$path = $this->config->get('main_image_path');
		$path = rtrim($path, '/');

		$relative = $path . '/' . $post->created_by;

		$relativeImagePath = $relative . '/' . $file['name'];
		$result->html = '<img src="'.$relativeImagePath.'" />';

		return $result;
	}


	/**
	 * Migrates wordpress category over
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function migrateCategory($category)
	{
		$title = '';
		$alias = '';

		if (isset($category->title)) {
			$title = EBString::strtolower($category->title);
			$alias = EBString::strtolower($category->alias);
		} else if ($category->name) {
			$title = EBString::strtolower($category->name);
			$alias = EBString::strtolower($category->slug);

			$category->title = $category->name;
			$category->alias = $category->slug;
		}

		if (! $title) {
			// someting went wrong with the category. js return the default one.
			return '1';
		}


		$query = 'select `id` from `#__easyblog_category`';
		$query .= ' where lower(`title`) = ' . $this->db->Quote($title);
		$query .= ' OR lower(`alias`) = ' . $this->db->Quote($alias);
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		// If easyblog category doesn't exist, create a new category using K2's category data
		if (!$result) {
			$result = $this->createEasyBlogCategory($category);
		}

		return $result;
	}

	/**
	 * Maps the wordpress data into EasyBlog's post format
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function mapData($wp, $authorId)
	{
		$post = new stdClass();

		// Get the author
		$author = EB::user($authorId);

		// Get the current date
		$date = EB::date();

		// Map the categories
		// Assign category
		$categories = array();

		if (!$wp->categories && (isset($wp->category) && $wp->category)) {
			$wp->categories = $wp->category;
		}

		if ($wp->categories) {
			foreach ($wp->categories as $category) {
				$categories[] = $this->migrateCategory($category);
			}
		}

		// lets check if categories is empty or not. if yes, lets assign default cat of 1
		if (! $categories) {
			$categories[] = '1';
		}

		// Map the categories
		$wp->categories = $categories;

		$post->category_id = $wp->categories[0];
		$post->categories = $wp->categories;

		$post->created_by = $author->id;
		$post->created = !empty($wp->post_date_gmt) ? $wp->post_date_gmt : $date->toSql();
		$post->modified = $date->toSql();
		$post->title = $wp->post_title;

		// post lib will take care of the normalization of permalink
		$post->permalink = $wp->post_title;

		$post->intro = $wp->post_excerpt;
		$post->content = $wp->post_content;

		$post->blogpassword = $wp->post_password;

		// Translate the wordpress article status into EasyBlog's publishing state
		$post->published = '0';
		$post->access = '0';

		if ($wp->status == 'private') {
			$post->access = '1';
			$post->published = '1';
		}

		if ($wp->status == 'publish') {
			$post->access = '0';
			$post->published = '1';
		}

		$post->publish_up = !empty($wp->post_date_gmt)? $wp->post_date_gmt : $date->toSql();
		$post->publish_down = '0000-00-00 00:00:00';
		$post->ordering = 0;
		$post->hits = 0;
		$post->frontpage = 1;
		$post->allowcomment = ($wp->comment_status == 'open') ? 1 : 0;

		$post->posttype = '';
		$post->source_id = '0';
		$post->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

		$post->keywords = isset($wp->keywords) ? $wp->keywords : '';
		$post->description = isset($wp->description) ? $wp->description : '';

		return $post;
	}

	/**
	 * Get's the migration statistics
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getMigrationStats()
	{
		$stats = $this->session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if (!$stats) {
			$stats = new stdClass();
			$stats->blog = 0;
			$stats->category = 0;
			$stats->user = array();
		}
	}

	/**
	 * Binds the post and tags
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createTags($wp, $post)
	{
		if (!$wp->tags) {
			return false;
		}

		$date = EB::date();

		foreach ($wp->tags as $item) {

			$tag = EB::table('Tag');
			$exists = $tag->exists($item->name);

			if ($exists) {
				$tag->load($item->name, true);
			} else {

				$tag->created_by = $post->created_by;
				$tag->title = $item->name;
				$tag->alias = $item->slug;
				$tag->published = 1;
				$tag->created = $date->toSql();
				$tag->store();
			}

			$relation = EB::table('PostTag');
			$relation->tag_id = $tag->id;
			$relation->post_id = $post->id;
			$relation->created = $date->toSql();
			$relation->store();
		}
	}

	/**
	 * Extracts the necessary data from the manifest
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function extractData($item, $wpOptions = false)
	{
		$post = new stdClass();

		// Extract post attributes
		$post->post_title = (string) $item->title;
		$post->guid = (string) $item->guid;
		$post->link = (string) $item->link;

		// Extract author data
		$dc = $item->children('http://purl.org/dc/elements/1.1/');
		$post->author = (string) $dc->creator;

		// We need to format the content
		$pattern = '/\[caption.*caption="(.*)"\]/iU';

		// Extract and Format post content
		$content = $item->children('http://purl.org/rss/1.0/modules/content/');
		$post->post_content = (string) $content->encoded;
		$post->post_content = preg_replace($pattern, '<div class="caption">$1</div>', $post->post_content);
		$post->post_content = str_ireplace('[/caption]', '<br />', $post->post_content);

		// remove the linefeed at the end of tags.
		$pattern = '/\<(\/)?(\w+)\>([\n\r])/i';
		$post->post_content = preg_replace($pattern, '<$1$2>', $post->post_content);

		// remove the linefeed at the end of nbsp;
		$pattern = '/nbsp;([\n\r])/i';
		$post->post_content = preg_replace($pattern, 'nbsp;', $post->post_content);

		// now lets nl2br so that the content formatting remain.
		$content = nl2br($post->post_content);

		if ($wpOptions->wraptext) {
			// we need to wrap the text with P tag if possible
			// let detect if the content already has P / div tags or not so that it will not break the content.
			$wrap = true;

			$pattern = '#<p|div[^>]*>#i';
			preg_match($pattern, $content, $matches);
			if ($matches) {
				$wrap = false;
			}

			if ($wrap) {
				// ini_set("xdebug.var_display_max_children", -1);
				// ini_set("xdebug.var_display_max_data", -1);
				// ini_set("xdebug.var_display_max_depth", -1);

				$segments = explode("\n\n", $post->post_content);

				if ($segments) {

					// need to check if the segments contain any heading tag or not. if yes, do not wrap with p tag.
					$processed = array();

					$pattern = '/<h[^>]*>(.*)<\/h[^>]*>/im';

					// pattern used to detect blocks
					$blockPattern = '#<ul|blockquote[^>]*>#i';

					foreach ($segments as $part) {

						$partContent = $part;

						preg_match_all($pattern, $part, $headings, PREG_SET_ORDER);
						if (!$headings) {
							// now we need to test if the part has any block tags or not.
							preg_match($blockPattern, $part, $blocks);
							if (! $blocks) {
								$partContent = '<p>' . $part . '</p>';
							}
						} else {
							$heading = $headings[0][0];
							$pos = strpos($part, $heading);

							$replaceSearch = '/(.*)(' . EBString::str_ireplace('/', '\/', $heading) . ')(.*)/si';
							$replaceWith = '<p>$1</p>$2<p>$3</p>';

							if ($pos == 0) {
								$replaceSearch = '/(' . EBString::str_ireplace('/', '\/', $heading) . ')(.*)/si';
								$replaceWith = '$1<p>$2</p>';
							}
							$partContent = preg_replace($replaceSearch, $replaceWith, $part);
						}
						$processed[] = $partContent;
					}
					$content = implode(' ', $processed);
				}
			}
		}

		$post->post_content = $content;

		// Format excerpt
		$excerpt = $item->children($this->namespaces['excerpt']);
		$post->post_excerpt = (string) $excerpt->encoded;
		$post->post_excerpt = nl2br($post->post_excerpt);

		// Extract wordpress data
		$wp = $item->children($this->namespaces['wp']);

		$post->post_id = (int) $wp->post_id;
		$post->post_date_gmt = (string) $wp->post_date_gmt;
		$post->comment_status = (string) $wp->comment_status;
		$post->post_name = (string) $wp->post_name;
		$post->status = (string) $wp->status;
		$post->post_type = (string) $wp->post_type;
		$post->post_parent = (string) $wp->post_parent;
		$post->post_password = (string) $wp->post_password;
		$post->attachment_url = (string) $wp->attachment_url;

		// If this is not a post, we should skip this
		// it seems like we no longer process attachement. 20180226
		if ($post->post_type != 'post') {
			return false;
		}

		// If the post is a draft, we should also skip this
		if ($post->status == 'draft') {
			return false;
		}

		// Extract terms
		$post->categories = array();
		$post->tags = array();

		foreach ($item->category as $category) {
			$attributes = $category->attributes();

			if (isset($attributes['nicename'])) {
				$term = new stdClass();
				$term->name = (string) $category;
				$term->slug = (string) $attributes['nicename'];
				$term->domain = (string) $attributes['domain'];

				if ($term->domain == 'category') {
					$post->category[] = $term;
				}

				if ($term->domain == 'post_tag') {
					$post->tags[] = $term;
				}
			}
		}

		// Extract post's metadata
		$post->postmeta = array();

		foreach ($wp->postmeta as $meta) {

			if ($meta->meta_key == 'keywords') {
				$post->keywords = (string) $meta->meta_value;
			}

			if ($meta->meta_key == 'description') {
				$post->description = (string) $meta->meta_value;
			}

			$post->postmeta[] = array('key' => (string) $meta->meta_key, 'value' => (string) $meta->meta_value);
		}

		// Extract comments
		$post->comments = array();

		foreach ($wp->comment as $wpComment) {

			// To prevent unable to get these data <![CDATA>
			$content = (string) $wpComment->comment_content;
			$commentAuthor = (string) $wpComment->comment_author;

			if (!$commentAuthor) {
				continue;
			}

			if (!$content) {
				continue;
			}

			$comment = new stdClass();
			$comment->id = (int) $wpComment->comment_id;
			$comment->author = (string) $commentAuthor;
			$comment->author_url = (string) $wpComment->comment_author_url;
			$comment->email = (string) $wpComment->comment_author_email;
			$comment->ip = (string) $wpComment->comment_author_IP;
			$comment->date = (string) $wpComment->comment_date;
			$comment->date_gmt = (string) $wpComment->comment_date_gmt;
			$comment->content = (string) $content;
			$comment->approved = (string) $wpComment->comment_approved;
			$comment->type = (string) $wpComment->comment_type;
			$comment->parent = (string) $wpComment->comment_parent;
			$comment->user_id = (int) $wpComment->comment_user_id;

			$post->comments[] = $comment;
		}

		return $post;

		// it seems like we no longer process attachement. 20180226

		// if ($post['post_type'] == 'attachment') {
		// 	$post_parant = $post['post_parent'];
		// 	// $this->logXMLData($fileName, $post_parant, 'attachment',  $post);
		// } else {

		// 	$post_id = $post['post_id'];

		// 	if (count($postComments) > 150) {
		// 		$postComments = array_slice($postComments, 0, 150);
		// 	}

		// 	// $this->logXMLData($fileName, $post_id, 'post', $post, $postComments);
		// }
	}

	/**
	 * Adds migration log
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function addMigrationLog($uid, $postId)
	{
		//log the entry into migrate table.
		$log = EB::table('Migrate');

		$log->content_id = $uid;
		$log->post_id = $postId;
		$log->session_id = $this->session->getToken();
		$log->component = 'xml_wordpress';
		$log->filename = $this->file;
		$log->store();

		return true;
	}

	/**
	 * Determines if a post is migrated
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isMigrated($fileName, $contentId)
	{
		$query = 'SELECT COUNT(1) FROM `#__easyblog_migrate_content` AS b';
		$query .= ' WHERE b.`content_id` = '. $this->db->Quote( $contentId );
		$query .= '  and `component` = ' . $this->db->Quote( 'xml_wordpress' );
		$query .= '  and `filename` = ' . $this->db->Quote( $fileName );

		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		$migrated = $total > 0 ? true : false;

		return $migrated;
	}

	/**
	 * Migrate comments from wordpress over
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createComments($wp, $post)
	{
		if (!$wp->comments) {
			return false;
		}

		// Sort the comments
		usort($wp->comments, array($this, 'sortComments'));

		$parents = array();
		$childs = array();

		// Process parent comments
		foreach ($wp->comments as $item) {

			// Do not migrate child comments yet.
			if ($item->parent) {
				$childs[] = $item;
				continue;
			}

			$parents[$item->id] = $this->migrateComment($item, $post, 0);
		}

		// Process child comments
		foreach ($childs as $item) {

			if (isset($parents[$item->parent])) {
				$this->migrateComment($item, $post, $parents[$item->parent]);
			}
		}

		return true;
	}

	/**
	 * Allows caller to migrate comments recursively
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function migrateComment($item, $post, $parentId = 0)
	{
		$date = EB::date();

		$comment = EB::table('Comment');

		// Bind the comments
		$comment->name = $item->author;
		$comment->email = $item->email;
		$comment->post_id = $post->id;
		$comment->comment = $item->content;
		$comment->title = "";
		$comment->url = $item->author_url;
		$comment->ip = $item->ip;
		$comment->created_by = 0;
		$comment->created = $item->date;
		$comment->modified = $item->date;
		$comment->published = 1;
		$comment->parent_id = $parentId;
		$comment->sent = 1;
		$comment->store();

		return $comment->id;
	}

	/**
	 * Use our own internal method to sort wordpress comments for a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function sortComments($a, $b)
	{
		$date1 = new DateTime($a->date);
		$date2 = new DateTime($b->date);

		return $date1 < $date2 ? -1 : 1;
	}

}
