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

class EasyBlogTableTag extends EasyBlogTable
{
	public $id = null;
	public $created_by = null;
	public $title = '';
	public $alias = '';
	public $description = '';
	public $created = null;
	public $status = null;
	public $published = null;
	public $default = null;
	public $ordering = null;
	public $params = null;
	public $language = '';

	public function __construct(& $db )
	{
		parent::__construct('#__easyblog_tag' , 'id' , $db);
	}

	/**
	 * Determines if a user can delete this tag
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canDelete($viewerId = null)
	{
		$viewer = JFactory::getUser($viewerId);

		// Ensure that the user owns this tag
		if ($this->created_by == $viewer->id || FH::isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if a user can manage this tag
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canManage($viewerId = null)
	{
		// Since its the same acl checks for deletion, we just reuse the same method
		return $this->canDelete($viewerId);
	}

	/**
	 * Loads a tag
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function load($id = null, $loadByTitle = false, $debug = false)
	{
		if (!$loadByTitle) {
			static $items = null;

			if (!isset($items[$id])) {
				$items[$id] = parent::load($id);
			}

			return $items[$id];
		}

		static $tags = array();

		$index = $id . $loadByTitle;

		if (!isset($tags[$index])) {
			$db = EB::db();

			$query = array();
			$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_tag');
			$query[] = 'WHERE ( BINARY';

			// It seems like when it trying to load a tag that have '-' in the title, it will fail. Let's comment it out for now. #2931
			// $query[] = $db->quoteName('title') . '=' . $db->Quote(EBString::str_ireplace('-', ' ', $id));

			// the reason we cast it to BINARY so that tags with different camel case can be retrieve correctly. #831 (git)
			$query[] = $db->quoteName('title') . '=' . $db->Quote($id);
			$query[] = 'OR BINARY ' . $db->quoteName('alias') . '=' . $db->Quote(EBString::str_ireplace(':', '-', EBString::strtolower($id)));
			$query[] = ')';

			$query = implode(' ', $query);

			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result) {
				parent::bind($result);

				$tags[$index] = clone $this;
			} else {
				$tags[$index] = false;
			}
		}

		return parent::bind($tags[$index]);
	}

	/**
	 * Determines if the alias exists
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function aliasExists()
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if ($this->id != 0){
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Determines if the tag already exists
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function exists($title, $isNew = true)
	{
		$db	= EB::db();

		$query = [];
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_tag');
		$query[] = 'WHERE BINARY' . $db->quoteName('title') . '=' . $db->Quote($title);

		if (!$isNew) {
			$query[] = 'AND ' . $db->quoteName('id') . '!=' . $db->Quote($this->id);
		}

		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bind($data, $ignore = [])
	{
		parent::bind($data, $ignore);

		if (empty($this->created)) {
			$date = EB::date();
			$this->created = $date->toSql();
		}

		if (!$this->title) {
			return;
		}

		// we only do this when this is a new tag or tag with empty alias
		if (empty($this->id) || empty($this->alias)) {
			jimport('joomla.filesystem.filter.filteroutput');

			$i = 1;
			while($this->aliasExists() || empty($this->alias)) {

				$this->alias = empty($this->alias) ? $this->title : $this->alias . '-' . $i;
				$i++;
			}
		}

		$this->alias = EBR::normalizePermalink($this->alias);
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		$db = EB::db();

		// Ensure that tag associations are removed
		$this->deletePostTag();

		if ($this->created_by != 0) {

			// Load site language files
			EB::loadLanguages();

			$config = EB::config();

			// Integrations with EasyDiscuss
			$easydiscuss = EB::easydiscuss();
			$easydiscuss->log('easyblog.delete.tag', $this->created_by, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG', $this->title));
			$easydiscuss->addPoint('easyblog.delete.tag', $this->created_by);
			$easydiscuss->addBadge('easyblog.delete.tag', $this->created_by);

			// Assign EasySocial points
			$easysocial = EB::easysocial();
			$easysocial->assignPoints('tag.remove', $this->created_by);

			// Assign jomsocial points
			EB::jomsocial()->assignPoints('com_easyblog.tag.remove', $this->created_by);

			// Assign point for altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_delete_tag', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_TAG_DELETED', $this->title));
		}

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_TAG_DELETE', 'tag', array(
			'tagTitle' => JText::_($this->title)
		));

		return parent::delete();
	}

	/**
	 * Delete all associated blog posts with this tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deletePostTag()
	{
		$model = EB::model('Tags');

		return $model->deleteAssociationByTagId($this->id);
	}

	/**
	 * Retrieves the number of posts that are associated with this tag.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostCount()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post_tag');
		$query[] = 'WHERE ' . $db->qn('tag_id') . '=' . $db->Quote($this->id);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}


	/**
	 * Retrieves rss link for the tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRssLink()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->id, false, 'tag');
	}

	/**
	 * Gets the translated tag title
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTitle()
	{
		return JText::_($this->title);
	}

	/**
	 * Gets the translated tag description
	 *
	 * @since	6.0.11
	 * @access	public
	 */
	public function getDescription()
	{
		return JText::_($this->description);
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getExternalPermalink($format = null)
	{
		$format = !is_null($format) ? '&format=' . $format : '';
		$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->id . $format, false, true, true);

		return $link;
	}

	/**
	 * Retrieves the alias of a tag
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
	 * Gets the tag permalink
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true)
	{
		$link = 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->id;
		$link = EBR::_($link, $xhtml);

		return $link;
	}

	/**
	 * Sets a tag as default
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function setDefault()
	{
		$this->default = true;
		return $this->store();
	}

	/**
	 * Removes a tag from default
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function removeDefault()
	{
		$this->default = false;
		return $this->store();
	}

	/**
	 * Saves a tag on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		// Check for empty title
		if (empty($this->title)){
			$this->setError(JText::_('COM_EASYBLOG_INVALID_TAG'));
			return false;
		}

		// @rule: Check if such tag exists.
		if ($this->exists($this->title, !$this->id)){
			$this->setError(JText::_('COM_EASYBLOG_TAG_ALREADY_EXISTS'));

			return false;
		}

		// @task: If alias is null, we need to generate them here.
		jimport('joomla.filesystem.filter.filteroutput');

		$i = 1;

		while ($this->aliasExists() || empty($this->alias)) {
			$this->alias = empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		$this->alias = EBR::normalizePermalink($this->alias);

		$date = EB::date($this->created);
		$this->created = $date->toSql();

		$isNew = !$this->id;
		$state = parent::store();
		$my	= JFactory::getUser();

		$actionLogString = 'COM_EB_ACTIONLOGS_TAG_UPDATE';

		if ($isNew) {
			$actionLogString = 'COM_EB_ACTIONLOGS_TAG_CREATE';
		}

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log($actionLogString, 'tag', [
			'link' => 'index.php?option=com_easyblog&view=tags&layout=form&id=' . $this->id,
			'tagTitle' => JText::_($this->title)
		]);

		// add tag meta
		$this->addMeta();

		// Integrate with 3rd party extension
		if ($isNew && $my->id != 0) {

			EB::loadLanguages();
			$config = EB::getConfig();

			// @rule: Integrations with EasyDiscuss
			EB::easydiscuss()->log('easyblog.new.tag', $my->id, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG', $this->title));
			EB::easydiscuss()->addPoint('easyblog.new.tag', $my->id);
			EB::easydiscuss()->addBadge('easyblog.new.tag', $my->id);

			// Assign EasySocial points
			EB::easysocial()->assignPoints('tag.create', $my->id);

			// Assign jomsocial points
			EB::jomsocial()->assignPoints('com_easyblog.tag.add', $my->id);

			// Assign point for altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_add_tag', $my->id, JText::sprintf('COM_EASYBLOG_AUP_TAG_ADDED', $this->title));
		}

		return $state;
	}

	/**
	 * Retrieve a list of tags that is associated with this tag
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getDefaultParams()
	{

		static $_cache = null;

		if (! $_cache) {

			$manifest = JPATH_ROOT . '/components/com_easyblog/views/tags/tmpl/tag.xml';
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
	 * Publishes this tag
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function publishItem()
	{
		$this->published = true;

		$state = $this->store();

		if ($state) {
			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_TAG_PUBLISH', 'tag', [
				'link' => 'index.php?option=com_easyblog&view=tags&layout=form&id=' . $this->id,
				'tagTitle' => JText::_($this->title)
			]);
		}

		return $state;
	}

	/**
	 * Unpublishes this tag
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function unpublishItem()
	{
		$this->published = false;
		$state = $this->store();

		if ($state) {
			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_TAG_UNPUBLISH', 'tag', [
				'link' => 'index.php?option=com_easyblog&view=tags&layout=form&id=' . $this->id,
				'tagTitle' => JText::_($this->title)
			]);
		}

		return $state;
	}

	/**
	 * add / update meta for tag
	 *
	 * @since	6.0.11
	 * @access	public
	 */
	public function addMeta()
	{
		if (!$this->id) {
			return;
		}

		$metaTbl = EB::table('Meta');
		$metaTbl->load([
			'type' => META_TYPE_TAG,
			'content_id' => $this->id
		]);

		// Skip this if this tag id already exist in the meta table
		if ($metaTbl->id) {
			return;
		}

		$meta = [];
		$meta['keywords'] = '';
		$meta['description'] = '';
		$meta['type'] = META_TYPE_TAG;
		$meta['content_id'] = $this->id;

		$metaTbl->bind($meta);
		$metaTbl->store();
	}
}
