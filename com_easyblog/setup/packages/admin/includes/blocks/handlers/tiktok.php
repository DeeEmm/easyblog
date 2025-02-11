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

class EasyBlogBlockHandlerTiktok extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fab fa-tiktok';
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

		$themes = EB::themes();
		$themes->set('html', false);
		$preview = $themes->output('site/blocks/tiktok');

		$meta->preview = $preview;

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
		if (!isset($block->data->url) || !$block->data->url) {
			return false;
		}

		if (!isset($block->data->html) || !$block->data->html) {
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
		if ($this->validate($block)) {
			$themes = EB::themes();
			$themes->set('html', $block->data->html);
			$html = $themes->output('site/blocks/tiktok');

			return $html;
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
		$themes->set('html', $block->data->html);
		$html = $themes->output('site/blocks/tiktok');

		return $html;
	}
}
