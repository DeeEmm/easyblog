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

class EasyBlogTableBlockTemplates extends EasyBlogTable
{
	public $id = null;
	public $block_id = null;
	public $user_id = null;
	public $title = null;
	public $description = null;
	public $data = null;
	public $published = null;
	public $created = null;
	public $global = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_composer_block_templates', 'id', $db);
	}

	/**
	 * Determine if user is able to delete the block template
	 *
	 * @since	6.0.0
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

		return true;
	}

	/**
	 * Determine if user is able to publish the block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canPublish()
	{
		$access = EB::acl();

		if (!$access->get('create_block_templates') && !FH::isSiteAdmin()) {
			return false;
		}

		if (!$this->isOwner()) {
			return false;
		}

		return true;
	}

	/**
	 * Determine if current viewer is the owner of the block template
	 *
	 * @since	6.0.0
	 * @access	public
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
	 * Get the author of the block template
	 *
	 * @since	6.0.0
	 * @access	public
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
	 * Method to duplicate the current block template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function duplicate()
	{
		$newTemplate = EB::table('BlockTemplates');

		// Bind existing data
		$newTemplate->bind($this);

		// Reset the id
		$newTemplate->id = null;

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
}
