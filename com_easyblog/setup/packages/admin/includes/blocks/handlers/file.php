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

class EasyBlogBlockHandlerFile extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fas fa-file-upload';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;
		$meta->properties['textpanel'] = false;

		// Set the template for the video player
		$theme = EB::themes();
		$meta->preview = $theme->output('site/composer/blocks/handlers/file/preview');

		return $meta;
	}

	public function data()
	{
		$data = new stdClass();

		$data->name = '';
		$data->type = '';
		$data->size = '';
		$data->url = '';

		// For fieldset
		$data->showicon = 1;
		$data->showsize = 1;

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
	 * Displays the html output for a file preview block
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		// For RSS documents, we just want to display a hyperlink
		$doc = JFactory::getDocument();

		if ($doc->getType() == 'feed') {
			return '&nbsp;<a href="' . $block->data->url . '" target="_blank">' . $block->data->name . '</a>';
		}

		// Always use the current url
		$url = $block->data->url;

		if ($useRelative) {
			$udpatedUrl = EB::string()->abs2rel($url);
		} else {
			$udpatedUrl = $url;
		}

		$block->html = EBString::str_ireplace($url, $udpatedUrl, $block->html);

		return parent::getHtml($block, $textOnly);
	}
}
