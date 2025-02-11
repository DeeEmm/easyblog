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

require_once(__DIR__ . '/table.php');

class EasyBlogTableCategory extends EasyBlogTable
{
	public $id = '';
	public $created_by = null;
	public $title = '';
	public $alias = '';
	public $avatar = null;
	public $parent_id = null;
	public $private = null;
	public $created = null;
	public $status = null;
	public $published = null;
	public $autopost = null;
	public $ordering = null;
	public $description = null;
	public $level = null;
	public $lft = null;
	public $rgt = null;
	public $default = null;
	public $language = '';
	public $params = null;
	public $theme = null;
	public $repost_autoposting = null;
	public $repost_autoposting_interval = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_category' , 'id' , $db);
		$this->theme = '';

		$this->repost_autoposting = 0;
		$this->repost_autoposting_interval = 0;
	}


	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete($pk = null)
	{
		EB::loadLanguages(JPATH_ADMINISTRATOR);

		$config = EB::config();

		// Ensure that this is not a default category
		if ($this->isDefault()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_IS_DEFAULT', $this->title));
			return false;
		}

		// If the table contains posts, do not allow them to delete the category.
		if ($this->getCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $this->title));
			return false;
		}

		// If the table contains subcategories, do not allow them to delete the parent.
		if ($this->getChildCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $this->title));
			return false;
		}

		// If the current user deleting this is the creator of the category, remove the points too.
		$my = JFactory::getUser();

		if ($this->created_by == $my->id) {
			EB::loadLanguages(JPATH_ROOT);

			// Integrations with EasyDiscuss.
			EB::easydiscuss()->log('easyblog.delete.category', $my->id, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_CATEGORY', $this->title));
			EB::easydiscuss()->addPoint('easyblog.delete.category', $my->id);
			EB::easydiscuss()->addBadge('easyblog.delete.category', $my->id);

			// Integrations with EasySocial
			EB::easysocial()->assignPoints('category.remove', $this->created_by);

			// Integrations with JomSocial
			EB::jomsocial()->assignPoints('com_easyblog.category.remove', $this->created_by);

			// Assign altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_delete_category', $this->created_by);
		}

		// Remove avatar if previously already uploaded.
		$this->removeAvatar();

		// remove category.acl #125
		$this->removeACL();

		$state = parent::delete();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_CATEGORY_DELETE', 'category', array(
			'categoryTitle' => JText::_($this->title)
		));

		return $state;
	}

	/**
	 * Remove associated ACL items for this category
	 * @since	5.0
	 * @return boolean
	 */
	public function removeACL()
	{
		$db = EB::db();

		$query = "delete from `#__easyblog_category_acl` where `category_id` = " . $db->Quote($this->id);
		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	/**
	 * Removes a category avatar
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function removeAvatar($update = false)
	{
		if (empty($this->avatar)) {
			return false;
		}

		$config = EB::config();

		$path = $config->get('main_categoryavatarpath');
		$path = rtrim($path, '/');
		$path = JPATH_ROOT . '/' . $path;

		// Get the absolute path to the file.
		$file = $path . '/' . $this->avatar;
		$file = JPath::clean($file);

		if (JFile::exists($file)) {
			JFile::delete($file);
		}

		if ($update) {
			$this->avatar = '';
			$this->store();
		}
	}

	/**
	 * Determines if this category is the primary category for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isPrimary()
	{
		if (!isset($this->primary)) {
			return false;
		}

		return $this->primary;
	}

	/**
	 * Duplicate the current category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function duplicate()
	{
		// Clone the category
		$clone = clone($this);
		$clone->id = null;
		$clone->title = JText::sprintf('COM_EASYBLOG_CATEGORIES_COPY_TITLE', JText::_($this->title));
		$clone->alias = $clone->getUniqueAlias($this->alias);
		$clone->avatar = null;
		$clone->lft = null;
		$clone->rgt = null;
		$clone->level = null;

		// Ensure that the default is not copied over when cloning
		$clone->default = null;

		// Save the cloned category
		$clone->store();

		$model = EB::model('Category');

		// Duplicate the ACL for the new clone category
		$model->duplicateAcl($this, $clone);

		// Duplicate custom field groups
		$model->duplicateFieldGroups($this, $clone);

		return $clone;
	}

	/**
	 * Determines if this category is the default category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isDefault()
	{
		return $this->default;
	}

	/**
	 * Retrieves a unique alias for the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUniqueAlias($alias = '')
	{
		$alias = empty($alias) && empty($this->alias) ? $this->title : $this->alias;
		$original = $alias;

		$i = 1;

		while (empty($alias) || $this->aliasExists($alias)) {
			$alias = $original . '-' . $i;
			$alias = EBR::normalizePermalink($alias);
			$i++;
		}

		return $alias;
	}

	/**
	 * Determines if this category is the public category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isNotAssigned()
	{
		return $this->private == '0' || $this->private == '1';
	}

	/**
	 * Sets this category as the default category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setDefault()
	{
		// Get the  model
		$model = EB::model('Category');

		// Remove all default categories
		$model->resetDefault();

		$this->default = true;

		return $this->store();
	}

	/**
	 * Determines if an alias already exists
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function aliasExists($alias = '')
	{
		$alias = $alias ? $alias : $this->alias;

		$model = EB::model('Category');
		return $model->aliasExists($alias, $this->id);
	}

	/**
	 * Run tasks after saving a category
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function afterSave()
	{
		// Re-arrange the lft right hierachy and ordering
		$this->rebuildOrdering();

		// Update the ordering.
		$this->updateOrdering();
	}

	/**
	 * Overrides parent's bind method
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function bind($data, $ignore = [])
	{
		// Bind the params first. #2749
		if ($this->params && $data['params']) {
			$newParams = EB::makeArray($data['params']);
			$storedParams = EB::makeArray($this->params);

			$params = array_merge($storedParams, $newParams);

			$this->params = json_encode($params);
			unset($data['params']);
		}

		parent::bind( $data, $ignore );

		if (!$this->created) {
			$this->created = EB::date()->toSql();
		}

		// we check the alias only when the category is a new category or the alias is empty
		if (empty($this->id) || empty($this->alias)) {
			jimport( 'joomla.filesystem.filter.filteroutput');

			$i = 1;

			$oriAlias = empty($this->alias) ? $this->title : $this->alias;

			while (empty($this->alias) || $this->aliasExists()) {

				$this->alias = empty($this->alias) ? $this->title : $oriAlias . '-' . $i;
				$this->alias = EBR::normalizePermalink($this->alias);
				$i++;
			}
		}
	}

	/**
	 * Retrieves rss link for the category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRssLink()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, false, 'category');
	}

	/**
	 * Retrieve a list of tags that is associated with this category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getDefaultTags()
	{
		$params = new JRegistry($this->params);

		$tags = $params->get('tags');

		if (empty($tags)) {
			return array();
		}

		$tags = explode(',', $tags);

		return $tags;
	}

	/**
	 * Retrieve rss link for the category
	 *
	 * @deprecated	4.0
	 */
	public function getRSS()
	{
		return $this->getRssLink();
	}

	public function getAtom()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, true, 'category');
	}

	/**
	 * Retrieve categories avatar
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAvatar()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			// Legacy defaults
			$defaults = ['cdefault.png', 'default_category.png', 'components/com_easyblog/assets/images/default_category.png', 'components/com_easyblog/assets/images/cdefault.png'];

			$default = 'media/com_easyblog/images/avatars/category.png';
			$link = '';

			if (!in_array($this->avatar, $defaults) && !empty($this->avatar)) {
				$link = EB::image()->getAvatarRelativePath('category') . '/' . $this->avatar;
			}

			// Check if a template override exists
			if (!$link) {
				$override = FH::getTemplateOverrideFolder('easyblog') . '/images/avatars/category.png';

				if (JFile::exists($override)) {
					$link = FH::getTemplateOverrideFolder('easyblog', true) . '/images/avatars/category.png';
				}
			}

			// If we still cannot get the link, we'll just fall back to the default avatar
			if (!$link) {
				$link = $default;
			}

			$cache[$this->id] = rtrim(JURI::root(), '/') . '/' . $link;
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of posts in this category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCount($bloggerId = '')
	{
		static $counts = array();

		$options = array();

		if ($bloggerId) {
			$options['bloggerId'] = $bloggerId;
		}

		if (!isset($counts[$this->id])) {
			$model = EB::model('Category');
			$counts[$this->id] = $model->getTotalPostCount($this->id, $options);
		}

		return $counts[$this->id];
	}

	/**
	 * Use getCount instead
	 *
	 * @deprecated	4.0
	 * @access	public
	 */
	public function getPostCount()
	{
		return $this->getCount();
	}

	/**
	 * Retrieves the total number of subcategories this category has.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getChildCount($includePermission = false)
	{
		$db = EB::db();
		$config = EB::config();
		$my = JFactory::getUser();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM `#__easyblog_category` AS a';
		$query[] = 'WHERE a.`parent_id` = ' . $db->Quote($this->id);
		$query[] = 'AND a.`published` = ' . $db->Quote(1);

		if ($includePermission) {

			if ($config->get('main_category_privacy')) {
				$catLib = EB::category();
				$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`', CATEGORY_ACL_ACTION_SELECT);
				$query[] = ' AND (' . $catAccess . ')';
			}

			if (! EB::isFromAdmin()) {
				$filterLanguage = JFactory::getApplication()->getLanguageFilter();

				if ($filterLanguage && !FH::isSiteAdmin() && $config->get('layout_composer_category_language', 0)) {
					$query[] = EBR::getLanguageQuery('AND', 'a.language');
				}
			}
		}

		$query = implode(' ', $query);

		// echo '-- ' . $my->name . ' new<br/>';
		// echo '<code>' . str_replace('#_', 'jos', $query) . ';</code>';exit;

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve a list of active authors for this category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getActiveAuthors()
	{
		static $result = array();

		if (!isset($result[$this->id])) {

			//check if the active author already cached or not. if yes,
			//let retrieve those
			if (EB::cache()->exists($this->id, 'cats')) {
				$data = EB::cache()->get($this->id, 'cats');

				if (isset($data['author'])) {
					$result[$this->id] = $data['author'];
				} else {
					$result[$this->id] = array();
				}
			} else {
				$model = EB::model('Category');
				$result[$this->id] = $model->getActiveAuthors($this->id);
			}
		}

		return $result[$this->id];
	}

	/**
	 * Retrieve list of count on active authors for this category
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getActiveAuthorsCount()
	{
		static $result = array();

		if (!isset($result[$this->id])) {

			//check if the active author already cached or not. if yes,
			//let retrieve those
			if (EB::cache()->exists($this->id, 'cats')) {
				$data = EB::cache()->get($this->id, 'cats');

				if (isset($data['authorCount'])) {
					$result[$this->id] = $data['authorCount'];
				} else {
					$result[$this->id] = '0';
				}
			} else {
				$model = EB::model('Category');
				$result[$this->id] = $model->getActiveAuthorsCount($this->id);
			}
		}

		return $result[$this->id];
	}

	/**
	 * Deprecated. Use @getActiveAuthors() instead
	 *
	 * @deprecated 4.0
	 */
	public function getActiveBloggers()
	{
		return $this->getActiveAuthors();
	}

	/**
	 * Override parent's implementation of store
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		if (!$this->created) {
			$this->created = EB::date()->toSql();
		}

		// Generate an alias if alias is empty
		if (!$this->alias) {
			$this->alias = EBR::normalizePermalink($this->title);
		}

		$my = JFactory::getUser();

		// Add point integrations for new categories
		if (!$this->id && $my->id) {
			EB::loadLanguages();

			// Integrations with EasyDiscuss
			EB::easydiscuss()->log( 'easyblog.new.category' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_CATEGORY' , $this->title ) );
			EB::easydiscuss()->addPoint( 'easyblog.new.category' , $my->id );
			EB::easydiscuss()->addBadge( 'easyblog.new.category' , $my->id );

			// JomSocial integrations
			EB::jomsocial()->assignPoints('com_easyblog.category.add', $my->id);

			// Assign EasySocial points
			EB::easysocial()->assignPoints('category.create', $my->id);

			// Assign altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_add_category', $my->id);
		}

		// Figure out the proper nested set model
		if (!$this->id && !$this->lft) {
			// No parent id, we use the current lft,rgt
			if ($this->parent_id) {
				$left = $this->getLeft( $this->parent_id );
				$this->lft = $left;
				$this->rgt = $this->lft + 1;

				// Update parent's right
				$this->updateRight($left);
				$this->updateLeft($left);
			}

			if (!$this->parent_id) {
				$this->lft = $this->getLeft() + 1;
				$this->rgt = $this->lft + 1;
			}
		}

		if (!$this->id) {
			// new cats. we need to store the ordering.
			$this->ordering = $this->getOrdering($this->parent_id) + 1;
		}

		$isNew = !$this->id ? true : false;
		$state = parent::store();

		return $state;
	}

	/**
	 * For categories acl to resave the category acl settings.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function saveACL($post)
	{
		$type = $this->getParam('category_acl_type', CATEGORY_ACL_ACTION_SELECT);

		if (isset($post['category_acl_view']) && !empty($post['category_acl_view'])) {
			foreach ($post['category_acl_view'] as $item) {
				$categoryAcl = EB::table('CategoryAcl');
				$categoryAcl->category_id = $this->id;
				$categoryAcl->acl_id = CATEGORY_ACL_ACTION_VIEW;
				$categoryAcl->acl_type = 'view';
				$categoryAcl->type = 'group';
				$categoryAcl->content_id = $item;
				$categoryAcl->status = '1';
				$categoryAcl->store();
			}
		}

		if ($type == CATEGORY_ACL_ACTION_SELECT) {
			if (isset($post['category_acl_select']) && !empty($post['category_acl_select'])) {
				foreach ($post['category_acl_select'] as $item) {
					$categoryAcl = EB::table('CategoryAcl');
					$categoryAcl->category_id = $this->id;
					$categoryAcl->acl_id = CATEGORY_ACL_ACTION_SELECT;
					$categoryAcl->acl_type = 'create';
					$categoryAcl->type = 'group';
					$categoryAcl->content_id = $item;
					$categoryAcl->status = '1';
					$categoryAcl->store();
				}
			}
		} else {
			if (isset($post['category_acl_specific']) && !empty($post['category_acl_specific'])) {
				foreach ($post['category_acl_specific'] as $item) {
					$categoryAcl = EB::table('CategoryAcl');
					$categoryAcl->category_id = $this->id;
					$categoryAcl->acl_id = CATEGORY_ACL_ACTION_SELECT;
					$categoryAcl->acl_type = 'create';
					$categoryAcl->type = 'user';
					$categoryAcl->content_id = $item;
					$categoryAcl->status = '1';
					$categoryAcl->store();
				}
			}
		}
	}

	/**
	 * For categories acl to delete the category acl settings.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function deleteACL($aclId = '')
	{
		$db = EB::db();

		$query = array();

		$query[] = 'DELETE FROM `#__easyblog_category_acl`';
		$query[] = 'WHERE `category_id` = ' . $db->Quote($this->id);

		if (!empty($aclId)) {
			$query[] = 'AND `acl_id` = ' . $db->Quote($aclId);
		}

		$query = implode(' ', $query);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * For categories acl that have group assigned permissions.
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getGroupAssignedACL()
	{
		$categoryAcl = EB::table('categoryacl');

		return $categoryAcl->getGroupAssignedACL($this->id);
	}

	/**
	 * For categories that have user assigned permissions instead of user groups
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getUserAssignedACL()
	{
		$categoryAcl = EB::table('categoryacl');

		return $categoryAcl->getUserAssignedACL($this->id);
	}

	public function checkPrivacy()
	{
		$config = EB::config();

		$obj = new stdClass();
		$obj->allowed = true;
		$obj->message = '';

		$my = JFactory::getUser();

		if ($this->private == '1' && $my->id == 0) {
			$obj->allowed	= false;
			$obj->error		= EB::privacy()->getErrorHTML();
		} else {
			if( $this->private == '2' && $config->get('main_category_privacy')) {

				$cats = EB::getPrivateCategories();

				if (in_array($this->id, $cats)) {
					$obj->allowed = false;
					$obj->error = JText::_('COM_EASYBLOG_PRIVACY_NOT_AUTHORIZED_ERROR');
				}

			}
		}

		return $obj;
	}

	// category ordering with lft and rgt
	public function updateLeft( $left, $limit = 0 )
	{
		$db     = EB::db();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'lft' ) . '=' . $db->nameQuote( 'lft' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'lft' ) . '>=' . $db->Quote( $left );

		if( !empty( $limit ) )
			$query  .= ' and `lft`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function updateRight( $right, $limit = 0 )
	{
		$db     = EB::db();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'rgt' ) . '=' . $db->nameQuote( 'rgt' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'rgt' ) . '>=' . $db->Quote( $right );

		if( !empty( $limit ) )
			$query  .= ' and `rgt`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function getLeft( $parent = 0 )
	{
		$db     = EB::db();

		if( $parent != 0 )
		{
			$query  = 'SELECT `rgt`' . ' '
					. 'FROM ' . $db->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent );
		}
		else
		{
			$query  = 'SELECT MAX(' . $db->nameQuote( 'rgt' ) . ') '
					. 'FROM ' . $db->nameQuote( $this->_tbl );
		}
		$db->setQuery( $query );

		$left   = (int) $db->loadResult();

		return $left;
	}

	public function getOrdering($parent = 0)
	{
		$db     = EB::db();
		$query = "select max(ordering)";
		$query .= " from `#__easyblog_category`";
		if ($parent) {
			$query .= " where (`id` = " . $db->Quote($parent) . " or `parent_id` = " . $db->Quote($parent) . ")";
		}

		$db->setQuery( $query );
		$maxordering   = (int) $db->loadResult();

		return $maxordering;
	}


	function move( $direction, $where = '' )
	{
		$db = EB::db();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			//echo $query;exit;
			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			// echo '<br>' . $query;
			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();

			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			//echo '<br>' . $query;
			//exit;
			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft 	= $nextParent->lft - $this->lft;
			$newLft 	= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$nextItemChilds = $db->loadResultArray();
			$nextChildIds   = implode(',', $nextItemChilds);
			$nextChildCnt   = count($nextItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			//update current parent
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($newLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($newLft);
			$query  .= ' where `id` in (' . $childIds. ')';

			//echo '<br>' . $query;
			//exit;

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($nextParent->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db = EB::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' where parent_id = ' . $db->Quote( $parentId );
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId );

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery    = 'update `#__easyblog_category` set';
		$updateQuery    .= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery    .= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery    .= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	/**
	 * Update the lft value to a particular parent's lft
	 * @param  [int] $parentId
	 * @return boolean
	 */
	public function moveLftToLastOf($parentId)
	{
		$db = EB::db();

		$query = "select max(lft) from `#__easyblog_category`";
		$query .= " where `parent_id` = " . $db->Quote($parentId);

		$db->setQuery($query);
		$lft = $db->loadResult();

		if ($lft) {
			$update = "update `#__easyblog_category` set `lft` = `lft` + $lft";
			$update .= " where `lft` >= " . $db->Quote($this->lft);
			$update .= " and `rgt` <= " . $db->Quote($this->rgt);

			$db->setQuery($update);
			$db->query();
		}

		return true;
	}

	public function updateOrdering()
	{
		$db = EB::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if( count( $rows ) > 0 )
		{
			$orderNum = '1';

			foreach( $rows as $row )
			{
				$query  = 'update `#__easyblog_category` set';
				$query  .= ' `ordering` = ' . $db->Quote( $orderNum );
				$query  .= ' where `id` = ' . $db->Quote( $row->id );

				$db->setQuery( $query );
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}

	/**
	 * Determines if there are any fields binded to the category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasCustomFields()
	{
		$fields = $this->getCustomFields();

		if ($fields === false) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves custom fields for this category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCustomFields($access = 'write')
	{
		static $loaded = array();

		if (!isset($loaded[$this->id])) {

			$group = null;
			$fields = null;

			if (EB::cache()->exists($this->id, 'categories')) {
				$cachedCategory = EB::cache()->get($this->id, 'categories');

				$group = isset($cachedCategory['group']) ? $cachedCategory['group'] : null;
				$fields = isset($cachedCategory['field']) ? $cachedCategory['field'] : null;

				// need to do manual sorting here
				if ($fields) {
					$tmp = array();

					foreach ($fields as $f) {
						$tmp[$f->ordering] = $f;
					}

					ksort($tmp);
					$fields = $tmp;
				}

			} else {

				$model = EB::model('Categories');

				$group = $model->getCustomFieldGroup($this->id, $access);
				$fields = $model->getCustomFields($this->id, $access);
			}

			if (!$group && !$fields) {
				$obj = false;
			} else {
				$obj = new stdClass();
				$obj->group  = $group;
				$obj->fields = $fields;
			}

			$loaded[$this->id] = $obj;
		}

		return $loaded[$this->id];
	}

	/**
	 * Retrieves the custom field group for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @return	mixed 	false if category is not associated with the group
	 */
	public function getCustomFieldGroup()
	{
		static $loaded = [];

		if (!isset($loaded[$this->id])) {
			$table = EB::table('CategoryFieldGroup');
			$state = $table->load(array('category_id' => $this->id));

			$loaded[$this->id] = $table;
		}

		return $loaded[$this->id];
	}

	/**
	 * Bind custom field group to the category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeFieldGroup()
	{
		// Delete existing mapping first
		$model = EB::model('Category');
		$state = $model->deleteExistingFieldMapping($this->id);

		return $state;
	}

	/**
	 * Bind custom field group to the category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bindCustomFieldGroup($groupId = '')
	{
		if (!$groupId) {
			return false;
		}

		// Delete existing mapping first
		$model = EB::model('Category');
		$model->deleteExistingFieldMapping($this->id);

		// Create a new mapping
		$table 	= EB::table('CategoryFieldGroup');
		$table->category_id = $this->id;
		$table->group_id = $groupId;
		$state = $table->store();

		if (!$state) {
			$this->setError($table->getError());
		}

		return $state;
	}

	/**
	 * Retrieves the parameters for the menu
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getMenuParams()
	{

	}

	/**
	 * Retrieves the parent category
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function getParent()
	{
		if (!$this->parent_id) {
			return false;
		}

		$parent = EB::table('Category');
		$parent->load($this->parent_id);

		return $parent;
	}

	/**
	 * Retrieve a specific parameter value
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getParam($key, $default = null)
	{
		static $params = [];

		if (!isset($params[$this->id])) {
			$params[$this->id] = $this->getParams();
		}

		$val = $params[$this->id]->get($key);

		$prefix = 'layout_';

		if ($val == '-1') {
			$config = EB::config();
			$val = $config->get($prefix . $key, $default);
		}

		// If the value is still null, probably not set in the category
		if ($val === null) {
			$val = $default;
		}

		return $val;
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getExternalPermalink($format = null, $withlimitstart = false)
	{
		$url = $this->normalizeCatPermalink();

		if ($withlimitstart) {

			$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
			$url .= ($limitstart) ? '&limitstart=' . $limitstart : '';
		}

		$link = EBR::getRoutedURL($url, false, true, true);
		$link = EBR::appendFormatToQueryString($link, $format);

		return $link;
	}

	/**
	 * Normalize the non sef link whether need to include the lang query string or not
	 *
	 * @since	5.4.9
	 * @access	public
	 */
	public function normalizeCatPermalink()
	{
		$appendQueryLang = '';
		$defaultURL = 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id;

		// Determine the site got enable multilingual or not
		$isSiteMultilingualEnabled = FH::isMultiLingual();

		// No need do anything if the site is not enable multilingue and the blog language is set to all
		if (!$isSiteMultilingualEnabled || ($this->language == '*' || $this->language == '')) {
			return $defaultURL;
		}

		// Retrieve the current post language
		$catLanguage = EBR::getSiteLanguageTag($this->language);

		if (!$catLanguage) {
			$catLanguage = $this->language;

			// Convert the language to 2 character format
			$catLanguage = substr($catLanguage, 0, 2);
		}

		// language query string
		$defaultURL = $defaultURL . '&lang=' . $catLanguage;

		return $defaultURL;
	}

	/**
	 * Retrieves alias for the category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAlias()
	{
		$config = EB::config();
		$alias = $this->alias;

		if (EBR::isIDRequired()) {
			$alias = $this->id . '-' . $this->alias;
		}

		return $alias;
	}

	/**
	 * Retrieves the permalink for this category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true)
	{
		$url = EB::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, $xhtml);

		return $url;
	}

	/**
	 * Retrieves description of the category
	 *
	 * @since	5.4.9
	 * @access	public
	 */
	public function getDescription()
	{
		if (!$this->description) {
			return JText::_('COM_EB_NO_DESCRIPTION_FOR_CATEGORY');;
		}

		return JText::_($this->description);
	}

	/**
	 * Retrieves category dashbaord edit link.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getEditPermalink($xhtml = true)
	{
		$url = EB::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm&id=' . $this->id, $xhtml);

		return $url;
	}

	public function getMetaId()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`id` FROM `#__easyblog_meta` AS a';
		$query  .= ' WHERE a.`content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND a.`type` = ' . $db->Quote( META_TYPE_CATEGORY );

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * add / update meta for category
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function addMeta()
	{
		$id = $this->getMetaId();

		// @rule: Save meta tags for this entry.
		$meta = EB::table('Meta');
		$meta->load($id);

		$meta->set('keywords', '');

		$meta->description = $this->description ? strip_tags($this->description) : '';
		$meta->set('content_id', $this->id);
		$meta->set('type', META_TYPE_CATEGORY);
		$meta->store();
	}

	/**
	 * Generates a default params for the category
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function generateDefaultParams($additionalParams = [])
	{
		// Assign default category params
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/category.json';
		$content = file_get_contents($file);

		if ($content) {
			$params = [];

			$rawParams = json_decode($content);

			foreach($rawParams as $raw) {
				$params[$raw->name] = $raw->default;
			}

			$params = array_merge($params, $additionalParams);

			$this->params = json_encode($params);

			return true;
		}

		return false;
	}

	/**
	 * Retrieve a list of tags that is associated with this category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getDefaultParams()
	{
		static $_cache = null;

		if (! $_cache) {

			$manifest = JPATH_ROOT . '/components/com_easyblog/views/categories/tmpl/listings.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$_cache = new JRegistry($obj);
		}

		return $_cache;
	}

	/**
	 * Determine if the category can be deleted from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canDelete()
	{
		$acl = EB::acl();
		$my = EB::user();

		// Check for acl permission
		if (!$acl->get('delete_category')) {
			$this->setError(JText::_('COM_EASYBLOG_CATEGORY_DELETE_NOT_ALLOWED'));
			return false;
		}

		// Ensure that this is not a default category
		if ($this->isDefault()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_IS_DEFAULT', $this->title));
			return false;
		}

		// Ensure that the category that is being deleted is owned by the user
		if ($this->created_by != $my->id && !FH::isSiteAdmin()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_NOT_OWNER', $this->title));
			return false;
		}

		// If the table contains posts, do not allow them to delete the category.
		if ($this->getCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $this->title));
			return false;
		}

		// If the table contains subcategories, do not allow them to delete the parent.
		if ($this->getChildCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $this->title));
			return false;
		}

		return true;
	}

	/**
	 * Determines if this category is published or not
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function isPublished()
	{
		return $this->published ? true : false;
	}

	/**
	 * Method to save the default cover for the posts of the category
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function saveDefaultPostCover($file)
	{
		if (empty($file) || !isset($file['tmp_name'])) {
			return false;
		}

		$filename = $this->id . '_default.png';

		$path = JPATH_ROOT . '/images/easyblog_cpost_cover/' . $filename;
		JFile::upload($file['tmp_name'], $path);

		$params = $this->getParams();
		$params->set('post_cover', $filename);

		$this->params = $params->toString();
		$this->store();
	}

	/**
	 * Retrieve categories default post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getDefaultPostCover($root = true, $preventCache = false)
	{
		$params = $this->getParams();
		$cover = $params->get('post_cover', '');

		if (!$cover) {
			return false;
		}

		$path = JPATH_ROOT . '/images/easyblog_cpost_cover/' . $this->id . '_default.png';

		if (JFile::exists($path)) {
			if ($root) {
				$path = rtrim(JURI::root(), '/') . '/images/easyblog_cpost_cover/' . $this->id . '_default.png';
			}

			if ($preventCache) {
				$path .= '?' . time();
			}

			return $path;
		}

		return false;
	}

	/**
	 * Remove category default post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function removePostCover($update = false)
	{
		$cover = $this->getDefaultPostCover(false);

		if (!$cover) {
			return false;
		}

		if (JFile::exists($cover)) {
			JFile::delete($cover);

			$params = $this->getParams();
			$params->set('post_cover', '');

			$this->params = $params->toString();
			$this->store();
		}

		return true;
	}

	/**
	 * Allows caller to upload an avatar
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function uploadAvatar($inputName = 'Filedata')
	{
		$acl = EB::acl();
		$default = 'default_category.png';

		// Ensure that the current user is allowed to upload avatar
		if (!$acl->get('upload_cavatar')) {
			$message = JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR');

			$this->setError($message);

			return false;
		}

		$config = EB::config();
		$configPath = rtrim($config->get('main_categoryavatarpath'), '/');
		$storagePath = JPATH_ROOT . '/' . $configPath;

		$input = JFactory::getApplication()->input;
		$file = $input->files->get($inputName, '');

		// Ensure that the storage path exists
		if (!JFolder::exists($storagePath) && !JFolder::create($storagePath)) {
			$this->setError(JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'));
			return false;
		}

		// Ensure that the name doesn't conflict with other category avatars
		$file['name'] = $this->id . '_' . JFile::makeSafe($file['name']);

		if (!isset($file['name'])) {
			$this->avatar = $default;
			return $this->store();
		}

		$error = null;

		// Detect if there are any abnomalities with the image file
		if (!EB::image()->canUpload($file, $error)) {
			$this->setError($error);

			return false;
		}

		if ($file['error'] != 0) {
			$this->setError($file['error']);
			return false;
		}

		$oldAvatar = (empty($this->avatar)) ? $default : $this->avatar;
		$tempAvatar = '';

		// Move the old avatar temporarily, just in case there are any errors, so that we can revert them
		if ($oldAvatar !== $default) {
			$session = JFactory::getSession();
			$sessionId = $session->getToken();

			$fileExt = JFile::getExt(JPath::clean($storagePath . '/' . $oldAvatar));
			$tempAvatar	= JPath::clean($storagePath . '/' . $sessionId . '.' . $fileExt);

			JFile::move($storagePath . '/' . $oldAvatar, $tempAvatar);
		}

		$target = JPath::clean($storagePath . '/' . JFile::makeSafe($file['name']));

		if (JFile::exists($target)) {
			if ($oldAvatar !== $default) {
				JFile::move($tempAvatar, $target . '/' . $oldAvatar);
			}

			$this->setError(JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $file['name']));
			return false;
		}


		$image = EB::imagelib();
		$image->load($file['tmp_name']);
		$image->resize(EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT, false);
		$image->save($target);

		// Now we update the user avatar. If needed, we remove the old avatar.
		if ($oldAvatar !== $default && file_exists($tempAvatar)) {
			JFile::delete($tempAvatar);
		}

		$this->avatar = $file['name'];
		return $this->store();
	}
}
