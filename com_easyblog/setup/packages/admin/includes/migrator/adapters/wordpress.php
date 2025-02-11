<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/base.php');

class EasyBlogMigratorWordpress extends EasyBlogMigratorBase
{
	/**
	 * Determines if wordpress for joomla is installed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isInstalled()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_wordpress/admin.wordpress.php';

		if (!JFile::exists($file)) {
			return false;
		}

		return true;
	}

	public function migrate($wpBlogId)
	{
		$ajax = EB::ajax();
		$session = JFactory::getSession();

		$migrateStat = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if (empty($migrateStat)) {
			$migrateStat = new stdClass();
			$migrateStat->blog = 0;
			$migrateStat->category = 0;
			$migrateStat->user = array();
		}

		// Get item based on $wpBlogId
		$items = $this->getItem($wpBlogId);

		// If no item to migrate, just return
		if (!$items) {
			return $ajax->resolve('noitem');
		}

		foreach ($items as $item) {
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date = EB::date();
			$blogObj = new stdClass();

			// Create category if this item's category does not exist on the site
			//$wpTableNamePrex = ($wpBlogId == '1') ? '' : $wpBlogId . '_';

			// Get the easyblog category Id
			$categoryId = $this->getEasyblogCategory();

			$blogObj->category_id = $categoryId;

			// this is needed because posy lib actually use this to create the post - category relations
			$blogObj->categories = array($categoryId);

			//load user profile
			$profile = EB::user($item->post_author);

			//assigning blog data
			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($item->post_date)? $item->post_date : $date->toSql();
			$blogObj->modified = $date->toSql();
			$blogObj->title = $item->post_title;
			$blogObj->permalink = $item->post_title; // post lib will take care of the normalization of permalink

			/* replacing [caption] and [gallery] */

			// Migrate caption
			$item = $this->migrateCaption($item);

			$blogObj->intro = $item->post_excerpt;
			$blogObj->content = $item->post_content;

			//translating the article state into easyblog publish status.
			$blogState = '0';
			$isPrivate = '0';

			if ($item->post_status == 'private') {
				$isPrivate = '1';
				$blogState = '1';
			} else if ($item->post_status == 'publish') {
				$isPrivate = '0';
				$blogState = '1';
			}

			$blogObj->blogpassword = $item->post_password;
			$blogObj->access = $isPrivate;
			$blogObj->published = $blogState;
			$blogObj->publish_up = !empty($item->post_date)? $item->post_date : $date->toSql();
			$blogObj->publish_down = '0000-00-00 00:00:00';
			$blogObj->ordering = 0;
			$blogObj->hits = 0;
			$blogObj->frontpage = 1;
			$blogObj->allowcomment = ($item->comment_status == 'open')? 1 : 0;

			// lets create blank post which are legacy type.
			$post = EB::post();
			$post->create(array('overrideDoctType' => 'legacy'));

			// now let get the uid
			$blogObj->uid = $post->uid;
			$blogObj->revision_id = $post->revision->id;

			// binding
			$post->bind($blogObj, array());

			$saveOptions = array(
							'applyDateOffset' => false,
							'validateData' => false,
							'useAuthorAsRevisionOwner' => true,
							'triggerPlugins' => false,
							'skipNotifications' => true
						);

			$post->save($saveOptions);

			// add tags.
			$wpPostTag = $this->getWPTerms($item->id, 'post_tag');

			// Migrate tag
			if (count($wpPostTag) > 0) {
				foreach ($wpPostTag as $tag) {
					$this->migrateTag($tag, $post);
				}
			}

			// Migrate comments
			$this->migrateComment($post, $item);

			//update session value
			$migrateStat->blog++;
			$statUser = $migrateStat->user;
			$statUserObj = null;

			if (!isset($statUser[$profile->id])) {
				$statUserObj = new stdClass();
				$statUserObj->name = $profile->nickname;
				$statUserObj->blogcount = 0;
			} else {
				$statUserObj = $statUser[$profile->id];
			}

			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user = $statUser;

			$session->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

			//log the entry into migrate table.
			$migratorTable = EB::table('Migrate');
			$migratorTable->content_id = $item->id;
			$migratorTable->post_id = $post->id;
			$migratorTable->session_id = $session->getToken();
			$migratorTable->component = 'com_wordpress';
			$migratorTable->store();

			$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_WORDPRESS_BLOG') . ': ' . $item->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $post->id . '<br />');

		}

		$stat = JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
		$stat .= JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

		$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
		$ajax->append('[data-progress-stat]', $stat);

		// we need to clear the stat variable that stored in session.
		$jSession = JFactory::getSession();
		$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		return $ajax->resolve();
	}

	public function getItem($wordpressBlogId)
	{

		// $wpTableNamePrex = ($wordpressBlogId == '1') ? '' : $wordpressBlogId . '_';
		// $wpComponentName = 'com_wordpress' . $wordpressBlogId;

		$query = 'SELECT a.`ID` as `id`, a.* FROM `#__wp_posts` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote( 'com_wordpress' );
		$query .= ' )';
		$query .= ' AND a.`post_type` = ' . $this->db->Quote('post');
		$query .= ' AND a.`post_status` != ' . $this->db->Quote('auto-draft');

		if ($wordpressBlogId != '0') {
			$query .= ' AND a.`ID` = ' . $this->db->Quote($wordpressBlogId);
		}

		$query .= ' ORDER BY a.`ID`';

		$this->db->setQuery($query);
		$item = $this->db->loadObjectList();

		return $item;
	}

	public function migrateCategory($item, $wpTableNamePrex)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		$wordpressCategory = $this->getWPTerms($wpTableNamePrex, $item->id, 'category');

		// If there's no category assigned in this item
		if (!isset($wordpressCategory->title)) {
			return $default;
		}

		// Determine if this category has already been created in EasyBlog
		$easyblogCategoryId = $this->easyblogCategoryExists($wordpressCategory);

		return $easyblogCategoryId;
	}

	/**
	 * Retrieves posts from Wordpress for Joomla
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getBlogs()
	{
		$db = EB::db();

		$query = 'select * from `#__wp_posts` where `post_type` = ' . $db->Quote('post');
		$db->setQuery($query);

		$result = $db->loadObjectList();

		$htmlList = array();
		if (count($result) > 0) {
			$htmlList[]	= JHTML::_('select.option', '0', 'All', 'value', 'state');
			foreach ($result as $item) {
				$htmlList[]	= JHTML::_('select.option', $item->ID, $item->post_title, 'value', 'state');
			}
		}

		if (count($htmlList) <= 0) {
			//this could be single site wordpress.
			$query  = 'SHOW TABLES LIKE ' . $db->Quote( '%wp_posts%' );
			$db->setQuery( $query );

			$result = $db->loadObjectList();
			if (count( $result ) > 0) {
				$htmlList[]	= JHTML::_('select.option', '1', 'Single site WordPress', 'value', 'state');
			}
		}

		return $htmlList;
	}

	public function getWPTerms($postId, $type)
	{
		$query = 'select distinct a.`name` as `title`, a.`slug` as `alias`, 1 as `published` from `#__wp_terms` as a';
		$query .= '  inner join `#__wp_term_taxonomy` as b on a.`term_id` = b.`term_id`';
		$query .= '  inner join `#__wp_term_relationships` as c on b.`term_taxonomy_id` = c.`term_taxonomy_id`';
		$query .= ' where c.`object_id` = ' . $this->db->Quote($postId);
		$query .= ' and b.`taxonomy` = ' . $this->db->Quote($type);

		$this->db->setQuery($query);

		$result = '';
		if ($type == 'category') {
			// always load one category bcos easyblog only support one category.
			$result = $this->db->loadObject();
		} else {
			//tags
			$result = $this->db->loadObjectList();
		}
		return $result;
	}

	public function migrateCaption($item)
	{
		$pattern2 = '/\[caption.*caption="(.*)"\]/iU';

		$item->post_content	= preg_replace( $pattern2 , '<div class="caption">$1</div>' , $item->post_content );
		$item->post_content	= str_ireplace( '[/caption]' , '<br />' , $item->post_content );

		// Migrate galleries
		$pattern = '/\[gallery(.*)/i';
		preg_match( $pattern , $item->post_content , $matches );

		if (!empty($matches)) {
			$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $item->id;
			if (!JFolder::exists($folder)) {
				JFolder::create( $folder );
			}

			// Now fetch items
			$query = 'SELECT a.guid FROM `#__wp_posts` AS a';
			$query .= ' WHERE `post_type` = ' . $this->db->Quote( 'attachment' );
			$query .= ' AND `post_mime_type` LIKE "%image%"';
			$query .= ' AND `post_parent`=' . $this->db->Quote( $item->id );

			//http://maephim.se/piccolina/wp-content/uploads/2011/04/Thailand-Apr-2010-080-Large.jpg
			//http://easyblog.localhost.com/components/com_wordpress/wp/wp-content/uploads/2011/08/262131_1791775084596_1546222768_31409359_5180181_n.jpg-540×720-pixels.jpg
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			$images = array();
			$siteRoot = JURI::root();

			foreach ($results as $result) {
				$image = $result->guid;
				$image = str_ireplace($siteRoot, '', $image);
				$image = str_ireplace('/', DIRECTORY_SEPARATOR, $image );
				$imageFull  = JPATH_ROOT. DIRECTORY_SEPARATOR. $image;
				$parts= explode(DIRECTORY_SEPARATOR, $imageFull);
				JFile::copy($imageFull , JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $item->id . DIRECTORY_SEPARATOR . $parts[count($parts) - 1]);
			}

			// Replace content with the proper gallery tag
			//{gallery}4745732{/gallery}
			$item->post_content	= EBString::str_ireplace($matches[0], '{gallery}'. $item->id . '{/gallery}', $item->post_content);
		}

		/* end replacing [caption] and [gallery] */
		$item->post_excerpt	= nl2br($item->post_excerpt);
		$item->post_content	= nl2br($item->post_content);

		return $item;
	}


	public function migrateTag($tag, $blog)
	{
		$now		= EB::date();
		$tagTable	= EB::table('Tag');

		if ($tagTable->exists($tag->title)) {
			$tagTable->load( $tag->title, true);
		} else {
			$tagArr = array();
			$tagArr['created_by'] = $this->getDefaultSuperUserId();
			$tagArr['title'] = $tag->title;
			$tagArr['alias'] = $tag->alias;
			$tagArr['published'] = '1';
			$tagArr['created'] = $now->toSql();

			$tagTable->bind($tagArr);
			$tagTable->store();
		}

		$postTag = EB::table('PostTag');
		$postTag->tag_id = $tagTable->id;
		$postTag->post_id = $blog->id;
		$postTag->created = $now->toSql();
		$postTag->store();
	}

	public function migrateComment($blog, $item)
	{
		$query = 'SELECT * FROM `#__wp_comments` AS a';
		$query .= ' where `comment_post_ID` = ' . $this->db->Quote( $item->id );
		$query .= ' and `comment_approved` = ' . $this->db->Quote('1');
		$query .= ' and `comment_parent` = ' . $this->db->Quote('0');
		$query .= ' order by `comment_date` ASC';

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if (count($results) > 0) {
			foreach ($results as $result) {
				$this->migrateWPComments($item->id, $blog->id, '0', $result);
			}
		}
	}

	public function migrateWPComments($postId, $blogId, $parentId, $item, $comments = array())
	{
		$now	= EB::date();
		$commt	= EB::table('Comment');

		//we need to rename the esname and esemail back to name and email.
		$post = array();
		$post['name'] = (isset($item->comment_author))? $item->comment_author : '';
		$post['email'] = (isset($item->comment_author_email))? $item->comment_author_email : '';
		$post['post_id'] = $blogId;
		$post['comment'] = (isset($item->comment_content))? $item->comment_content : '';
		$post['title'] = '';
		$post['url'] = (isset($item->comment_author_url))? $item->comment_author_url : '';
		$post['ip'] = (isset($item->comment_author_IP))? $item->comment_author_IP : '';
		$commt->bindPost($post);

		$commt->created_by = $item->user_id;
		$commt->created = (isset($item->comment_date))? $item->comment_date : '';
		$commt->modified = (isset($item->comment_date))? $item->comment_date : '';
		$commt->published = 1;
		$commt->parent_id = $parentId;
		$commt->sent = 1;

		$commt->store();

		//check to see if there is any child comments or not.
		$query = 'SELECT a.* FROM `#__wp_comments` AS a';
		$query .= ' where `comment_post_ID` = ' . $this->db->Quote($postId);
		$query .= ' and `comment_approved` = ' . $this->db->Quote('1');
		$query .= ' and `comment_parent` = ' . $this->db->Quote($item->comment_ID);
		$query .= ' order by `comment_date` ASC';

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		if (count($result) > 0) {
			foreach ($result as $citem) {
				$this->migrateWPComments($wpTableNamePrex, $postId, $blogId, $commt->id, $citem);
			}
		}

		return true;
	}

}
