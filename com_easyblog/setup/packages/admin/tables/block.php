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

class EasyBlogTableBlock extends EasyBlogTable
{
	public $id = null;
	public $element = null;
	public $group = null;
	public $title = null;
	public $description = null;
	public $published = null;
	public $created = null;
	public $keywords = null;
	public $ordering = null;
	public $params = "";

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_composer_blocks' , 'id' , $db);
	}

	public function getCreated()
	{
		$date = EB::date($this->created);

		return $date;
	}

	/**
	 * Retrieves the form for the block
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function getForms()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/blocks/' . $this->element . '.json';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		$contents = file_get_contents($file);
		$data = json_decode($contents);

		if (!isset($data->params)) {
			return false;
		}

		if (!$data->params) {
			return false;
		}

		$forms = array();

		foreach ($data->params as $form) {

			// Normalize this so that it works with form.dropdown
			if (isset($form->options)) {
				$options = array();

				foreach ($form->options as $option) {
					$options[$option->value] = $option->title;
				}

				$form->options = $options;

			}
			$forms[] = $form;
		}


		return $forms;
	}

	/**
	 * Publishes a block
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		if ($this->isSystem()) {
			return true;
		}

		// Set the state
		$this->published = 1;

		// Store the post
		return $this->store();
	}

	/**
	 * Unpublishes a block
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		if ($this->isSystem()) {
			return true;
		}

		// Set the state
		$this->published = 0;

		// Store the post
		return $this->store();
	}

	/**
	 * Determine if the block is a system block
	 *
	 * @since   5.2.7
	 * @access  public
	 */
	public function isSystem()
	{
		return $this->published == 2;
	}
}
