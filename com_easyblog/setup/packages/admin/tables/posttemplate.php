<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTablePostTemplate extends EasyBlogTable
{
	public $id = null;
	public $user_id = null;
	public $title = null;
	public $data = null;
	public $created = null;
	public $system = null;
	public $screenshot = null;
	public $core = null;
	public $params = null;
	public $published = null;
	public $doctype = null;
	public $ordering = null;

	static $thumbnail = 'template-thumbnail.png';
	static $defaultThumbnails = '/media/com_easyblog/images/templates/blank.png';

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_post_templates', 'id', $db);
	}

	/**
	 * Determines if the post template is a blank templae
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isBlank()
	{
		return $this->system == 2;
	}

	/**
	 * Determines if the post template is a system templae
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isSystem()
	{
		return $this->system == 1;
	}

	/**
	 * Determines if the post template is a core templae
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isCore()
	{
		return $this->core == 1;
	}

	/**
	 * Determines if the post template is a core templae
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isOwner()
	{
		$my = EB::user();

		if (FH::isSiteAdmin($my->id) || $this->user_id == $my->id) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Dertermines if this templates is for ebd editor
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEbd()
	{
		return $this->doctype == 'ebd';
	}

	/**
	 * Determine if this templates is for legacy editor
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isLegacy()
	{
		return $this->doctype == 'legacy';
	}

	/**
	 * Determine if this templates is locked
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function isLocked()
	{
		$isLocked = $this->getParams()->get('lock', false);

		if ($isLocked) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if this templates can be locked
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function canLock()
	{
		// Templates can be locked by the site admin only
		if (!FH::isSiteAdmin()) {
			return false;
		}

		// Legacy editor do not have blocks to lock with.
		if ($this->isLegacy()) {
			return false;
		}

		return true;
	}

	/**
	 * Set the lock state for the post template
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function setLockState($state)
	{
		$state = $state == 'lock' ? 1 : 0;

		$params = new JRegistry($this->params);
		$params->set('lock', $state);

		$this->params = $params->toString();

		$this->store();
	}

	/**
	 * Lock the Post Template
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function lock()
	{
		// Only site admin can lock it
		if (!FH::isSiteAdmin()) {
			return;
		}

		// If the post template is locked, there is no point to lock again
		if ($this->isLocked()) {
			return;
		}

		$this->setLockState('lock');
	}

	/**
	 * Unlock the Post Template
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function unlock()
	{
		// Only site admin can unlock it
		if (!FH::isSiteAdmin()) {
			return;
		}

		// If the post template never been locked before/ not locked, there is no point to unlock
		if (!$this->isLocked()) {
			return;
		}

		$this->setLockState('unlock');
	}

	/**
	 * Retrieves the author of the template
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getAuthor()
	{
		static $authors = array();

		if (!isset($authors[$this->user_id])) {
			$user = EB::user($this->user_id);
			$authors[$this->user_id] = $user;
		}

		return $authors[$this->user_id];
	}

	/**
	 * Loads a blog post
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function load($id=null, $reset=true)
	{
		// Load post from post table
		$state = parent::load($id);

		// Determine the editor to use for the new template
		if (!$id) {
			$this->doctype = EB::user()->getEditor() == 'composer' ? 'ebd' : 'legacy';
		}

		return $state;
	}

	/**
	 * Retrieves the creation date in JDate format
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getCreated()
	{
		$date = EB::date($this->created);

		return $date;
	}

	/**
	 * Retrieves the edit link for the template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getEditLink($xhtml = true)
	{
		if ($this->isBlank()) {
			return 'javascript:void(0);';
		}

		$url = 'index.php?option=com_easyblog&view=templates&layout=form&tmpl=component&id=' . $this->id;

		if (EB::isFromAdmin()) {
			return $url;
		}

		$url = EBR::getRoutedURL($url, $xhtml, true);

		return $url;
	}

	/**
	 * Performs check against the properties of the table
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function check()
	{
		if (!$this->title) {
			$this->setError(JText::_('COM_EASYBLOG_TEMPLATES_PLEASE_ENTER_A_TITLE_FOR_YOUR_TEMPLATE'));

			return false;
		}

		return true;
	}

	/**
	 * Retrieves the document object
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getDocument()
	{
		static $documents = array();

		if (!isset($documents[$this->id])) {

			$document = EB::document();
			$document->load($this->data);

			$documents[$this->id] = $document;
		}

		return $documents[$this->id];
	}

	/**
	 * An exportable result of this object
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function export()
	{
		$obj = new stdClass();
		$obj->id = $this->id;
		$obj->title = $this->title;
		$obj->document = json_decode($this->data);
		$obj->formattedDate = $this->getCreated()->format(JText::_('DATE_FORMAT_LC2'));

		return $obj;
	}

	/**
	 * Store the thumbnail of the template
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function storeThumbnail($file)
	{
		$config = EB::config();

		// Do not proceed if image doesn't exist.
		if (empty($file) || !isset($file['tmp_name'])) {
			$this->setError(JText::_('COM_EASYBLOG_IMAGE_UPLOADER_PLEASE_INPUT_A_FILE_FOR_UPLOAD'));
			return false;
		}

		if (!$this->id) {
			$this->setError(JText::_('COM_EASYBLOG_TEMPLATE_THUMBNAIL_INVALID_TEMPLATE_ID'));
			return false;
		}

		$source = $file['tmp_name'];
		$thumbnailPath = '/images/easyblog_post_templates/' . $this->id . '/' . self::$thumbnail;
		$absolutePath = JPATH_ROOT . $thumbnailPath;

		// Try to upload the image
		$state = JFile::upload($source, $absolutePath);

		if (!$state) {
			$this->setError(JText::_('COM_EASYBLOG_TEMPLATE_THUMBNAIL_UPLOAD_ERROR'));
			return false;
		}

		return true;
	}

	/**
	 * Get thumbnails for the template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getThumbnails($external = false)
	{
		// Check for custom thumbnail
		if ($this->hasOverrideThumbnails()) {

			$overrideThumbnails = $this->getOverridePath();

			if ($external) {
				return rtrim(JURI::root(), '/') . $overrideThumbnails;
			}

			return $overrideThumbnails;
		}

		// Return default thumbnail path
		return $this->getDefaultThumbnails($external);
	}

	/**
	 * Get default thumbnails for the template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDefaultThumbnails($external = false)
	{
		if (isset($this->screenshot) && $this->screenshot) {

			$absolutePath = JPATH_ROOT . $this->screenshot;

			if (JFile::exists($absolutePath)) {

				if ($external) {
					return rtrim(JURI::root(), '/') . $this->screenshot;
				}

				return $this->screenshot;
			}
		}

		// default template
		if ($external) {
			return rtrim(JURI::root(), '/') . self::$defaultThumbnails;
		}

		return self::$defaultThumbnails;
	}

	/**
	 * Get override path for thumbnail
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getOverridePath()
	{
		$thumbnailPath = '/images/easyblog_post_templates/' . $this->id . '/' . self::$thumbnail;

		return $thumbnailPath;
	}

	/**
	 * Determine if override is exists
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasOverrideThumbnails()
	{
		$path = JPATH_ROOT . $this->getOverridePath();

		if (JFile::exists($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Method to remove thumbnails override
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeOverrideThumbnails()
	{
		if (!$this->hasOverrideThumbnails()) {
			return false;
		}

		// Get override path
		$path = JPATH_ROOT . $this->getOverridePath();

		// Let's delete it
		JFile::delete($path);

		return true;
	}

	/**
	 * Duplicate current template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function duplicate()
	{
		// Do not allow blank template to be duplicated
		if ($this->isBlank()) {
			return;
		}

		$newTemplate = EB::table('PostTemplate');
		// $newTemplate->load();

		// Bind existing data
		$newTemplate->bind($this);

		// Reset the id
		$newTemplate->id = null;

		// duplicated templat cannot be core.
		$newTemplate->core = 0;

		// New title
		$originalTitle = $this->title;

		if (strpos($this->title, 'COM_EASYBLOG_') !== false) {
			// load frontend language file.
			EB::loadLanguages(JPATH_ROOT);
			$originalTitle = JText::_($this->title);
		}

		$newTemplate->title = JText::sprintf('COM_EASYBLOG_DUPLICATE_OF_POST', $originalTitle);

		$newTemplate->store();

		return true;
	}

	/**
	 * Determine if user can view the selected templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canView()
	{
		if ($this->isOwner()) {
			return true;
		}

		if (!$this->published) {
			return false;
		}

		// Site wide template
		if ($this->system > 0) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if user can publish or unpublish the templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canPublish()
	{
		$access = EB::acl();

		if (!$access->get('create_post_templates') && !FH::isSiteAdmin()) {
			return false;
		}

		if (!$this->isOwner()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if user can delete the templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canDelete()
	{
		// Cannot delete non-existence template
		if (!$this->id) {
			return false;
		}

		// Basically if you can publish, you can delete as well
		if (!$this->canPublish()) {
			return false;
		}

		// System template is not deletable
		if ($this->isCore() || $this->isBlank()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if user can create post template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canCreate()
	{
		$access = EB::acl();

		if (!$access->get('create_post_templates') && !FH::isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the user is able to edit the post template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canEdit()
	{
		// Cannot create means cannot edit.
		if (!$this->canCreate()) {
			return false;
		}

		// User must have the ability to delete the template in order to edit it.
		if (!$this->canDelete()) {
			return false;
		}

		return true;
	}

	/**
	 * A special function used to bind various data from the form post.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function restoreFromPost($data)
	{
		if (!$data) {
			return;
		}

		// Bind the data
		$this->bind($data);
	}

	public function move($direction, $where = '')
	{
		$db = EB::db();

		if ($direction == -1) {
			// getting prev item
			$query = 'select `id`, `ordering` from `#__easyblog_post_templates` where `ordering` < ' . $db->Quote($this->ordering);
			$query .= ' order by ordering desc limit 1';

			$db->setQuery($query);
			$prevTemplate  = $db->loadObject();

			//now update the ordering.
			$query = 'update `#__easyblog_post_templates` set';
			$query .= ' `ordering` = `ordering` - 1';
			$query .= ' where `id` = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			//now update the previous template's ordering.
			$query = 'update `#__easyblog_post_templates` set';
			$query .= ' `ordering` = `ordering` + 1';
			$query .= ' where `id` = ' . $db->Quote($prevTemplate->id);
			$db->setQuery($query);
			$db->query();

			return true;
		} else  {
			// getting next item
			$query = 'select `id`, `ordering` from `#__easyblog_post_templates` where `ordering` > ' . $db->Quote($this->ordering);
			$query .= ' order by ordering asc limit 1';

			$db->setQuery($query);
			$nextTemplate = $db->loadObject();

			//now update the ordering.
			$query = 'update `#__easyblog_post_templates` set';
			$query .= ' `ordering` = `ordering` + 1';
			$query .= ' where `id` = ' . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();

			//now update the previous template's ordering.
			$query = 'update `#__easyblog_post_templates` set';
			$query .= ' `ordering` = `ordering` - 1';
			$query .= ' where `id` = ' . $db->Quote($nextTemplate->id);

			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	public function updateOrdering()
	{
		$db = EB::db();

		$query = 'select `id` from `#__easyblog_post_templates`';
		$query .= ' order by ordering';

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (count($rows) > 0) {
			$orderNum = '1';

			foreach ($rows as $row) {
				$query = 'update `#__easyblog_post_templates` set';
				$query .= ' `ordering` = ' . $db->Quote($orderNum);
				$query .= ' where `id` = ' . $db->Quote($row->id);

				$db->setQuery($query);
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}
}
