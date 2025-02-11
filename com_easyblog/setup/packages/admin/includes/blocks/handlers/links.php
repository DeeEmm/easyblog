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

class EasyBlogBlockHandlerLinks extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fas fa-external-link-alt';
	public $element = 'none';

	public function data()
	{
		$data = new stdClass();
		$data->title = '';
		$data->content = '';
		$data->url = '';
		$data->images = array();
		$data->image = '';
		$data->showImage = true;
		$data->noFollow = false;
		$data->newWindow = false;

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->url) || !$block->data->url) {
			return false;
		}

		return true;
	}

	/**
	 * We need to format the block data before passing it back to json_encode
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function normalizeData($data)
	{
		// Ensure that title and content is proper entity
		// Skip double quotes and single quotes entities as it will be process later in @renderBlockData
		if (isset($data->title) && $data->title) {
			$data->title = htmlentities($data->title, ENT_NOQUOTES);
		}

		if (isset($data->content) && $data->content) {
			$data->content = htmlentities($data->content, ENT_NOQUOTES);
		}

		return $data;
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		if ($textOnly) {
			return;
		}

		// If there's no data associated with the block skip this
		if (!isset($block->data) || !$block->data) {
			return;
		}

		return $block->html;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$html = '<p><a href="' . $block->data->url . '">' . $block->data->title . '</a></p>';

		return $html;
	}
}
