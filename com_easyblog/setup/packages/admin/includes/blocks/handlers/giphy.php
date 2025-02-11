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

class EasyBlogBlockHandlerGiphy extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fab fa-giphy';
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

		$themes = EB::themes();
		$themes->set('url', false);
		$themes->set('alignment', 'center');

		$meta->preview = $themes->output('site/composer/blocks/handlers/giphy/preview');

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
		$data = (object) [
			'alignment' => 'center',
			'currentQuery' => (object) [
				'gifs' => '',
				'stickers' => ''
			],

			// By default, gifs will always be first
			'currentView' => 'gifs'
		];

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
		// if no url specified, return false.
		if (!isset($block->data->url) || !$block->data->url) {
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
			$themes->set('url', $block->data->url);
			$themes->set('alignment', $block->data->alignment);

			$html = $themes->output('site/composer/blocks/handlers/giphy/preview');

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

		// If the source isn't set ignore this.
		if (!$this->validate($block)) {
			return;
		}

		$alignment = 'center';

		if ($block->data->alignment !== 'center') {
			$alignment = $block->data->alignment;
		}

		$themes = EB::themes();
		$themes->set('giphy', $block->data->url);
		$themes->set('alignment', $alignment);
		$output = $themes->output('site/blocks/giphy');

		return $output;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		if (!$this->validate($block)) {
			return;
		}

		return '<amp-img width="480" height="384" layout="responsive" src="' . $block->data->url . '"></amp-img>';
	}
}
