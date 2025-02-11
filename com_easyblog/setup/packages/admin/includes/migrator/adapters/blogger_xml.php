<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/base.php');

class EasyBlogMigratorBlogger_xml extends EasyBlogMigratorBase
{
	public function migrate($fileName, $authorId, $categoryId)
	{
		$session = JFactory::getSession();
		$ajax = EB::ajax();

		$file = JPATH_ROOT . '/administrator/components/com_easyblog/xmlfiles/blogger/' . $fileName;

		if (JFile::exists($file)) {
// 			/* use for debugging - dun remove */
// 			libxml_use_internal_errors(true);
// 			echo '<pre>';
// 			print_r( simplexml_load_file( $file ) );
// 			echo '</pre>';
// 		    $errors = libxml_get_errors();
// 			var_dump($errors);
// 		    foreach ($errors as $error) {
// 				var_dump($error);
// 		    }
// 		    libxml_clear_errors();
// 			exit;

			$exitsPosts = $this->checkXMLPostData($fileName, 'post-blogger');

			if ($exitsPosts) {
				$this->migrateBloggerXML($fileName, $authorId, $categoryId);
			} else {
				$parser = simplexml_load_file($file);

				if ($parser) {
					$post = array();
					$attachments = array();

					$entries = $parser->entry;

					// process each items
					foreach ($entries as $item) {

						if (strpos($item->id,'.post-') === false) {
							continue;
						}

						$post = array(
							'post_title' => (string) $item->title,
							'guid' => (string) $item->id,
						);

						$post_id = stristr($item->id, '.post-');
						$post['post_author'] = $authorId;
						$post['post_content'] = (string) $item->content;
						$post['post_id']= str_replace('.post-', '', $post_id);
						$post['post_date_gmt'] = (string) $item->published;
						$post['post_lastupdate_gmt'] = (string) $item->updated;

						$i = 0;

						foreach ($item->category as $tag) {
							if ($i == 0) {
								$i++;
								continue;
							}
							$att = $tag->attributes();
							$post['tags'][] = (string) $att['term'];
						}

						$commentLink = '';
						$commentCountTitle = '';
						$commentCount = 0;

						$attCategory = $item->category->attributes();

						// Only want to migrate posts.
						if (strstr($attCategory['term'] , '#') != '#post') {
							continue;
						}

						foreach ($item->link as $link) {
							$att = $link->attributes();
							if ($att['rel'] == 'replies') {
								if ($att['type'] == 'application/atom+xml') {
									$commentLink = (string) $att['href'];
								} else if ($att['type'] == 'text/html') {
									$commentCountTitle  = (string) $att['title'];
								}
							}
						}

						if ($commentCountTitle) {
							$tmp = explode(' ', $commentCountTitle);
							if (isset($tmp[0]) && is_numeric($tmp[0])) {
								$commentCount = $tmp[0];
							}
						}

						$post['comment_link'] = $commentLink;
						$post['images'] = '';

						//process images from blogger in content.
						$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
						preg_match_all($pattern, $post['post_content'], $matches);

						if (count($matches[1]) > 0) {
							$postImages = array();
							foreach ($matches[1] as $img) {
								if (strpos($img, '.bp.blogspot.com') !== false) {
									$postImages[] = $img;
								}
							}

							if (count($postImages) > 0) {
								$post['images'] = $postImages;
							}
						}

						$postComments = array();
						$this->logXMLData($fileName, $post['post_id'], 'post-blogger', $post, $postComments);

					} //end foreach

					$this->migrateBloggerXML($fileName, $authorId, $categoryId);
				}
				else {// if parse xml file error.
					return $ajax->resolve('parseFailed');
				}
			}
		} else {
			return $ajax->resolve('fileNotExist');
		}
	}

	/**
	 * Retrieves exported files from blogger
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFiles()
	{
		$path = JPATH_ROOT . '/administrator/components/com_easyblog/xmlfiles/blogger';

		if (!JFolder::exists($path)) {
			return false;
		}

		$files = JFolder::files($path, '.xml');

		return $files;
	}

	public function migrateBloggerXML($fileName, $authorId, $categoryId)
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

		// get posts from table jos_easyblog_xml_wpdata. Any post that already migrated,
		// will be removed from this table to avoid duplicate post
		$posts = $this->getXMLPostData($fileName, 'post-blogger');

		// if posts is empty, exit.
		if (!$posts) {
			// return $ajax->resolve('failed');
			return $ajax->resolve('noitem');
		}

		// Loop thru posts to be migrated
		foreach ($posts as $post) {

			// If no post_id, just continue to the next item
			if (!isset($post->post_id)) {
				$this->clearXMLData($fileName, true);
				continue;
			}

			$data = $post->data;
			$contentId = $data['post_id'];

			if (empty($contentId)) {
				$this->clearXMLData($fileName, true);
				continue;
			}

			$data['comments'] = $post->comments;

			// Check in easyblog_migrate_content if already exist
			$row = $this->checkData($fileName, $contentId);

			// If it returns a value means the post is already exist in easyblog_migrate_content
			if (!is_null($row)) {
				continue;
			}

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.
			$date = EB::date();
			$blogObj = new stdClass();
			$adminId = $authorId;

			$wpTag = array();

			if (!empty($data['tags'])) {
				foreach ($data['tags'] as $tag) {
					$tmpTag = new stdClass();
					$tmpTag->title = $tag;
					$tmpTag->alias = '';
					$wpTag[] = $tmpTag;
				}
			}

			//load user profile
			$profile = EB::user($adminId);

			if (empty($data['post_title'])) {
				$tmpString = strip_tags($data['post_content']);
				$blogObj->title = substr($tmpString, 0, 15) . '...';
			} else {
				$blogObj->title = $data['post_title'];
			}

			// Assign blog data
			$blogObj->category_id = $categoryId;

			// this is needed because post lib actually use this to create the post - category relations.
			$blogObj->categories = array($categoryId);

			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($data['post_date_gmt'])? $data['post_date_gmt']:$date->toSql();
			$blogObj->modified = !empty($data['post_lastupdate_gmt'])? $data['post_lastupdate_gmt']:$date->toSql();

			// process image upload and urll replacement.
			// if ($data['images']) {
			// 	foreach($data['images'] as $image) {
			// 		$data['post_content'] = $this->migrateBloggerImage($image, $authorId, $data['post_content'] );
			// 	}
			// }

			$content = nl2br($data['post_content']);
			$content = preg_replace('#data:image/[^;]+;base64,#', '', $content);


			$blogObj->intro = '';
			$blogObj->content = $content;
			$blogObj->access = '0';
			$blogObj->published = '1';
			$blogObj->publish_up = !empty($data['post_date_gmt'])? $data['post_date_gmt']:$date->toSql();
			$blogObj->publish_down = '0000-00-00 00:00:00';
			$blogObj->ordering = 0;
			$blogObj->hits = 0;
			$blogObj->frontpage = 1;
			$blogObj->allowcomment = 1;
			$blogObj->posttype = '';
			$blogObj->source_id = '0';
			$blogObj->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

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
			if (count($wpTag) > 0) {
				foreach($wpTag as $item) {
					$this->saveTag($item, $post);
				}
			}

			// add comments
			if (isset($data['comment_link'])) {
				// we need to fetch the data from a external feed.
				$this->migrateBloggerComment($data['comment_link'], $post->id);
			}

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

			$migratorTable->content_id = $contentId;
			$migratorTable->post_id = $post->id;
			$migratorTable->session_id = $session->getToken();
			$migratorTable->component = 'xml_blogger';
			$migratorTable->filename = $fileName;
			$migratorTable->store();

			$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_BLOGGER_XML') . ': ' . $data['post_id'] . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $post->id . '<br />');

		} // end foreach

		// here we should check if there is any post left in jos_easyblog_xml_wpdata
		$morePosts = $this->checkXMLPostData($fileName, 'post-blogger');

		$hasmore = false;


		if ($morePosts) {
			// $this->migrateBloggerXML($fileName, $authorId, $categoryId);
			$hasmore = true;

		} else {

			// temporary fix so that the migration state will not get added multiple time.

			$stat  = JText::_('COM_EASYBLOG_MIGRATOR_BLOGGER_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_BLOGGER_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ajax->append('[data-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		}

		return $ajax->resolve($hasmore);
	}

	public function saveTag($item, $blog)
	{
		$now = EB::date();
		$tag = EB::table('Tag');

		if ($tag->exists($item->title)) {
			$tag->load($item->title, true);
		} else {
			$tagArr = array();
			$tagArr['created_by'] = $blog->created_by;
			$tagArr['title'] = $item->title;
			$tagArr['published'] = '1';
			$tagArr['created'] = $now->toSql();

			$tag->bind($tagArr);
			$tag->store();
		}

		$postTag = EB::table('PostTag');
		$postTag->tag_id = $tag->id;
		$postTag->post_id = $blog->id;
		$postTag->created = $now->toSql();
		$postTag->store();
	}

	public function checkData($fileName, $contentId)
	{
		$query = 'SELECT content_id FROM `#__easyblog_migrate_content` AS b';
		$query .= ' WHERE b.`content_id` = '. $this->db->Quote($contentId);
		$query .= '  and `component` = ' . $this->db->Quote('xml_blogger');
		$query .= '  and `filename` = ' . $this->db->Quote($fileName);

		$this->db->setQuery($query);
		$row = $this->db->loadResult();

		return $row;
	}

	function migrateBloggerImage($image, $userid, $content)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$config = EB::getConfig();
		$main_image_path = $config->get('main_image_path');
		$main_image_path = rtrim($main_image_path, '/');

		$rel_upload_path = $main_image_path . '/' . $userid;
		$userUploadPath = JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace('/', DIRECTORY_SEPARATOR, $main_image_path . DIRECTORY_SEPARATOR . $userid);
		$folder = JPath::clean($userUploadPath);

		$dir = $userUploadPath . DIRECTORY_SEPARATOR;
		$tmp_dir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

		if (!JFolder::exists($dir)) {
			JFolder::create($dir);
		}

		//now let get the image from remove url.
		$segments = explode('/', $image);
		$fileName = $segments[count($segments) - 1];

		$fileName = JFile::makesafe($fileName);
		$tmpFileName = $tmp_dir . $fileName;

		$file['name'] = $fileName;
		$file['tmp_name'] = $tmpFileName;

		// write to JOOMLA tmp folder
		file_put_contents($tmpFileName, file_get_contents($image));

		$media = EB::mediamanager();

		$adapter = $media->getAdapter('user:' . $userid);
		$result = $adapter->upload($file, 'user:' . $userid);

		@JFile::delete($file['tmp_name']);

		if (isset($result->type)) {
			$relativeImagePath = $rel_upload_path . '/' . $file['name'];

			// lets replace the image from the content to this uploaded one.
			$content = str_replace( $image , $relativeImagePath , $content);
		}

		return $content;
	}

	function migrateBloggerComment($link, $post_id)
	{
		$connector = FH::connector($link);
		$content = $connector->execute()->getResult();

		$pattern = '/(.*?)<\?xml version/is';
		$replacement = '<?xml version';
		$content = preg_replace($pattern, $replacement, $content, 1);

		$parser = @simplexml_load_string($content);

		if ($parser) {
			$lft = 1;
			$rgt = 2;

			$entries = $parser->entry;
			// process each items
			foreach ($entries as $item) {

				if (strpos($item->id, '.post-') === false) {
					continue;
				}

				$now = EB::date();
				$db = EB::db();
				$comment = EB::table('Comment');

				//we need to rename the esname and esemail back to name and email.
				$post = array();
				$post['name'] = (string) $item->author->name;
				$post['email'] = (string) $item->author->email;
				$post['post_id'] = $post_id;
				$post['comment'] = (string) $item->content;
				$post['title'] = (string) $item->title;
				$post['url'] = (string) $item->author->uri;;
				$post['ip'] = '';

				$comment->bindPost($post);

				$comment->created_by = '0';
				$comment->created = (string) $item->published;
				$comment->modified = (string) $item->published;
				$comment->published = 1;
				$comment->parent_id = '0';
				$comment->sent = 1;
				$comment->lft = $lft;
				$comment->rgt = $rgt;

				if ($comment->store()) {
					$lft++;
					$rgt++;
				}
			}
		}
	}
}
