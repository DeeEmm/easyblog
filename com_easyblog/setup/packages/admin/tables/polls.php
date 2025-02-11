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

class EasyBlogTablePolls extends EasyBlogTable
{
	public $id = null;
	public $title = null;
	public $multiple = null;
	public $allow_unvote = null;
	public $created = null;
	public $user_id = null;
	public $expiry_date = null;
	public $state = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_polls', 'id', $db);
	}

	/**
	 * Perform the necessary actions first before we store the table
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function savePoll($data)
	{
		// Do not save the poll if the title is empty
		if (!isset($data->title) || !$data->title) {
			return false;
		}

		$my = JFactory::getUser();

		$this->title = $data->title;
		$this->user_id = $my->id;
		$this->multiple = (int) $data->isMultiple;
		$this->allow_unvote = (int) $data->unvoteAllowed;
		$this->expiry_date = $data->expiry_date ? $data->expiry_date : '0000-00-00 00:00:00';
		$this->state = EB_PUBLISHED;

		$state = parent::store();

		$itemsExclusion = [];

		// Update its item as well
		foreach ($data->items as $index => $item) {
			if (is_array($item)) {
				$item = (object) $item;
			}

			if (is_string($item)) {
				$item = json_decode($item);
			}

			$value = isset($item->content) && $item->content ? $item->content : '';

			// Do not save the empty item
			if (!$value) {
				unset($data->items[$index]);

				continue;
			}

			$itemId = isset($item->id) && $item->id ? (int) $item->id : null;

			$table = EB::table('PollItems');
			$table->load($itemId);

			$table->poll_id = (int) $this->id;
			$table->value = $value;

			$table->store();

			// Update its item id on the block data as well
			$item->id = $table->id;

			$itemsExclusion[] = $item->id;

			// Always ensure that there is content property in it
			$item->content = $table->value;
		}

		$model = EB::model('Polls');

		// Delete the items that are no longer associated with this poll
		$model->deleteItems($this->id, $itemsExclusion);

		return true;
	}

	/**
	 * Determine if the poll allows multiple choice
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isMultiple()
	{
		return $this->multiple;
	}

	/**
	 * Retrieve the title of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTitle()
	{
		return JText::_($this->title);
	}

	/**
	 * Overrides parent's delete method.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		$model = EB::model('Polls');
		$model->deletePolls($this->id);
	}

	/**
	 * Set the state of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function setState($state)
	{
		$this->state = $state;
	}
}