<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogBlockHandlerPolls extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fas fa-poll-h';
	public $element = 'figure';

	/**
	 * Standard meta data of a block object
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;

		return $meta;
	}

	/**
	 * Supplies the default data to the js part
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function data()
	{
		$data = (object) [];

		return $data;
	}

	/**
	 * Perform validation of the block
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function validate($block)
	{
		// If there is no poll title or poll options
		if (!isset($block->data->pollId) || !$block->data->pollId) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve the html to be rendered during post edit
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		if (isset($block->data->pollId) && $block->data->pollId) {
			// Ensure that the poll is valid
			$poll = EB::polls($block->data->pollId);

			if (!$poll->id) {
				return false;
			}

			$html = $poll->getBlockHtml();

			return $html;
		}
	}

	/**
	 * Normalize the data
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function normalizeData($data)
	{
		if (isset($data->pollId) && $data->pollId) {
			$poll = EB::polls($data->pollId);

			if (!$poll->id) {
				// Reset the poll id if this poll is invalid
				$data->pollId = 0;

				// Set a toRemove flag so that we know this block is going to be removed
				$data->toRemove = true;
			}

			return $data;
		}
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		if ($textOnly) {
			return;
		}

		if (!$this->validate($block)) {
			return;
		}

		$themes = EB::themes();
		$themes->set('pollId', $block->data->pollId);
		$output = $themes->output('site/blocks/polls/default');

		return $output;
	}
}
