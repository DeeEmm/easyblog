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

class EasyBlogMigratorK2 extends EasyBlogMigratorBase
{
	public function isInstalled()
	{
		jimport('joomla.filesystem.folder');

		$path = JPATH_ROOT . '/administrator/components/com_k2';
		return JFolder::exists($path);
	}

	public function migrate($migrateComment, $migrateAll, $catId)
	{
		$ajax = EB::ajax();

		// Determine which category to import from
		$catId = $catId == 0 ? null : $catId;

		// Render Joomla's session
		$session = JFactory::getSession();

		// Get the total number of items
		$total = $this->getTotalItems($catId);

		// Get the real items
		$items = $this->getItems($catId);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		// If there's nothing to load just skip this
		if (!$items) {
			return $ajax->resolve('noitem');
		}

		foreach ($items as $item) {

			// Create a new blog object
			$blogObj = new stdClass();
			// Get today's date
			$date = EB::date();

			// Create category if this item's category does not exist on the site
			$categoryId = $this->migrateCategory($item);

			// Statistics
			// after category being created, we need to retrieve the stat again from session.
			$migrateStat = $session->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
			if (empty($migrateStat)) {
				$migrateStat = new stdClass();
				$migrateStat->blog = 0;
				$migrateStat->category = 0;
				$migrateStat->user = array();
			}

			// Assign the new category to the blog object
			$blogObj->category_id = $categoryId;

			// this is needed because post lib actually use this to create the post - category relations.
			$blogObj->categories = array($categoryId);

			// Create user if the user does not exist in `#__easyblog_users`
			$profile = EB::user($item->created_by);

			//assigning blog data
			$blogObj->created_by = $profile->id;
			$blogObj->created = !empty($item->created)? $item->created : $date->toSql();
			$blogObj->modified = $date->toSql();
			$blogObj->title = $item->title;
			$blogObj->permalink = $item->alias; // post lib will take care of the normalization of permalink

			$blogObj->intro = '';
			$blogObj->content = $item->introtext;

			if (!empty($item->fulltext)) {
				// If $item->fulltext exist, means there is system-readmore.
				$blogObj->intro = $item->introtext;
				$blogObj->content = $item->introtext. '<hr id="system-readmore" />' .$item->fulltext;
			}

			$access = ($item->access == 1 || $item->access == 5) ? 0 : 1;
			$blogObj->access = $access;

			$blogState = ($item->published == 2 || $item->published == -2) ? 0 : $item->published;
			$blogObj->published = $blogState;
			$blogObj->publish_up = !empty($item->publish_up)? $item->publish_up : $date->toSql();
			$blogObj->publish_down = !empty($item->publish_down)? $item->publish_down : $date->toSql();
			$blogObj->ordering = $item->ordering;
			$blogObj->hits = $item->hits;
			$blogObj->frontpage = 1;
			$blogObj->eb_language = $item->language;
			$blogObj->posttype = '';
			$blogObj->source_id = '0';
			$blogObj->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

			// lets create blank post which are legacy type.
			$post = EB::post();
			$post->create(array('overrideDoctType' => 'legacy', 'triggerPlugins' => false));

			// now let get the uid
			$blogObj->uid = $post->uid;
			$blogObj->revision_id = $post->revision->id;

			// binding
			$post->bind($blogObj, array('force' => true));

			//Added video to blog content
			$this->migrateK2Videos($item, $post);

			//Migrate over the images
			$this->migrateK2Images($item, $post, $profile);

			$saveOptions = array(
							'applyDateOffset' => false,
							'validateData' => false,
							'useAuthorAsRevisionOwner' => true,
							'triggerPlugins' => false,
							'skipNotifications' => true
						);

			$post->save($saveOptions);

			//Get K2 Tags and map into Easyblog Tags
			$K2Tags = $this->getK2Tag($item->id);

			if ($K2Tags) {
				foreach ($K2Tags as $tag) {
					$this->mapK2Tag($tag, $item, $post);
				}
			}

			// if article was featured, lets mark this blog post as featured as well.
			if ($item->featured) {
				// just call the model file will do as we do not want to create stream on featured action at this migration.
				$modelFeatured = EB::model('Featured');
				$modelFeatured->makeFeatured('post', $post->id);
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
			$migratorTable->content_id = $item->id;
			$migratorTable->post_id = $post->id;
			$migratorTable->session_id = $session->getToken();
			$migratorTable->component = 'com_k2';
			$migratorTable->store();

			$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_K2') . ': ' . $item->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $post->id . '<br />');

			if ($migrateComment) {
				$this->migrateK2Comments($item, $post);
				$return = array();
				$return['migrate_k2_comments'] = 1;
				$return['k2category'] = $catId;
				$return = json_encode($return);
			}
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {

			$stat = JText::_('COM_EASYBLOG_MIGRATOR_K2_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat .= JText::_('COM_EASYBLOG_MIGRATOR_K2_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$ajax->append('[data-progress-status]', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ajax->append('[data-progress-stat]', $stat);

			// we need to clear the stat variable that stored in session.
			$session->set('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		}

		return $ajax->resolve($hasmore);
	}

	public function getTotalItems($categoryId = null)
	{
		$query = 'SELECT COUNT(1) FROM `#__k2_items` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_k2');
		$query .= ' )';

		if (!is_null($categoryId)) {
			$query .= ' AND a.`catid` = ' . $this->db->Quote($categoryId);
		}

		$this->db->setQuery($query);

		$total = $this->db->loadResult();

		return $total;
	}

	public function getItems($categoryId = null, $limit = 10)
	{
		$query = 'SELECT * FROM `#__k2_items` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_k2');
		$query .= ' )';

		if (!is_null($categoryId)) {
			$query .= ' AND a.`catid` = ' . $this->db->Quote($categoryId);
		}

		$query .= ' ORDER BY a.`id`';

		$this->db->setQuery($query.' LIMIT '.$limit);

		$items = $this->db->loadObjectList();

		return $items;
	}

	public function migrateCategory($item)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (!$item->catid) {
			return $default;
		}

		// Get K2's category
		$k2Category = $this->getK2Category($item->catid);

		// Determine if this category has already been created in EasyBlog
		$easyblogCategoryId = $this->easyblogCategoryExists($k2Category);

		return $easyblogCategoryId;
	}

	/**
	 * Retrieves a list of K2 categories
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCategories()
	{
		$db = EB::db();

		if (!$this->isInstalled()) {
			return false;
		}

		$query	= 'SELECT * FROM `#__k2_categories`';
		$db->setQuery( $query );
		$items	= $db->loadObjectList();

		if (!$items) {
			return false;
		}

		return $items;
	}

	public function getK2Category($id)
	{
		$query  = 'SELECT * FROM `#__k2_categories` where `id` = ' . $this->db->Quote($id);
		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		// Mimic Joomla's category behavior
		if ($result) {
			$result->title = $result->name;
		}

		return $result;
	}

	public function getK2Tag($itemId)
	{
		$query = 'SELECT a.* FROM #__k2_tags AS a '
				. 'INNER JOIN #__k2_tags_xref AS b '
				. 'ON a.`id`=b.`tagID` '
				. 'WHERE b.`itemID`=' . $this->db->Quote( $itemId );
		$this->db->setQuery($query);
		$result	= $this->db->loadObjectList();

		return $result;
	}

	public function mapK2Tag($tag, $item, $blog)
	{
		$now = EB::date();
		$tableTag = EB::table('Tag');

		if ($tableTag->exists($tag->name)) {
			$tableTag->load($tag->name, true);
		} else {
			$tagArr = array();
			$tagArr['created_by'] = $this->getDefaultSuperUserId();
			$tagArr['title'] = $tag->name;
			$tagArr['alias'] = $tag->name;
			$tagArr['published'] = '1';
			$tagArr['created'] = $now->toSql();

			$tableTag->bind($tagArr);
			$tableTag->store();
		}

		$postTag = EB::table('PostTag');
		$postTag->tag_id = $tableTag->id;
		$postTag->post_id = $blog->id;
		$postTag->created = $now->toSql();
		$postTag->store();
	}

	public function migrateK2Videos(&$item, &$blog)
	{
		$video = $item->video;
		$blog->content .= $video;
	}

	public function migrateK2Images(&$item, &$blog, $author)
	{
		jimport('joomla.filesystem.file');

		$name = md5('Image' . $item->id);
		$path = JPATH_ROOT  . '/media/k2/items/src/' . $name . '.jpg';
		$config	= EB::getConfig();
		$configStorage = str_ireplace('\\' , '/' , $config->get('main_image_path'));
		$newPath = JPATH_ROOT . '/' . rtrim( $configStorage , '/' ) . '/' . $author->id;

		if (!JFolder::exists($newPath)) {
			JFolder::create($newPath);
		}

		if (JFile::exists($path)) {
			// Copy the full scaled image
			$large = JPATH_ROOT . '/media/k2/items/cache/' . $name . '_XL.jpg';
			$targetLarge = $newPath . '/' . $name . '.jpg';
			$targetURL = rtrim(JURI::root(), '/') . '/' . str_ireplace('\\', '/', $configStorage) . '/' . $author->id;
			$largeSrc = rtrim(JURI::root(), '/') . '/' . str_ireplace('\\', '/', $configStorage) . '/' . $author->id . '/' . $name . '.jpg';

			$file = getimagesize($large);
			$file['name'] = basename($large);
			$file['tmp_name'] = $large;

			$media = EB::mediamanager();

			$adapter = $media->getAdapter('user:' . $author->id);
			$result = $adapter->upload($file, 'user:' . $author->id);

			if (isset($result->type)) {
				$relativeImagePath = '/' . $file['name'];
				$result->path = $relativeImagePath;
			}

			$result = json_encode($result);

			$blog->image = $result;
		}
	}

	public function migrateK2Comments($k2obj, $blog)
	{
		$jSession = JFactory::getSession();

		$query = 'SELECT * FROM `#__k2_comments` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $this->db->Quote('com_k2.comments');
		$query .= ' ) ';
		$query .= 'AND a.' . $this->db->nameQuote( 'itemID' ) . ' = ' . $this->db->Quote( $k2obj->id ) . ' ORDER BY a.`id` ASC';

		$this->db->setQuery( $query );

		$comments = $this->db->loadObjectList();

		if (!$comments) {
			return;
		}

		$lft = 1;
		$rgt = 2;

		foreach ($comments as $comment) {
			$post = array();
			$post['post_id'] = $blog->id;
			$post['comment'] = $comment->commentText;
			$post['name'] = $comment->userName;

			// @rule: Since K2 does not store any title for comments, we just leave this blank.
			$post['title'] = '';
			$post['email'] = $comment->commentEmail;
			$post['url'] = $comment->commentURL;

			$table = EB::table('Comment');
			$table->bindPost($post);

			//the rest info assign here.
			$table->lft = $lft;
			$table->rgt = $rgt;

			$table->created_by = $comment->userID;
			$table->created	= $comment->commentDate;
			$table->modified = $comment->commentDate;
			$table->published = $comment->published;

			$table->store();

			//log the entry into migrate table.
			$migratorTable = EB::table('Migrate');
			$migratorTable->content_id = $comment->id;
			$migratorTable->post_id = $table->id;
			$migratorTable->session_id = $jSession->getToken();
			$migratorTable->component = 'com_k2.comments';
			$migratorTable->store();

			//do not touch this settings!
			$lft = $rgt + 1;
			$rgt = $lft + 1;
		}
	}
}
