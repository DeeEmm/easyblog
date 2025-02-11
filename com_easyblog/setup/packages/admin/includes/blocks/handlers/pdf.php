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

class EasyBlogBlockHandlerPdf extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi far fa-file-pdf';
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

		return $meta;
	}

	public function data()
	{
		$data = (object) array();
		$data->pdfjs = rtrim(JURI::root(), '/') . '/media/com_easyblog/pdfjs/web/viewer.html?file=';

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
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
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		if ($textOnly) {
			return;
		}

		if (!isset($block->data->url) || !$block->data->url) {
			return;
		}

		// Set a default height value
		if (!isset($block->data->height)) {
			$block->data->height = 600;
		}

		$root = JURI::root();

		// Remove the http: or https: from the url
		$match = str_ireplace(array('http:', 'https:'), '', $root);

		// Remove the slash from the last
		$match = rtrim($match, '/');

		// Remove the domain from the url in order to use relative path
		$block->data->url = JURI::root(true) . str_replace($match, '', $block->data->url);

		$url = JURI::root(true) . '/media/com_easyblog/pdfjs/web/viewer.html?file=' . urlencode($block->data->url);

		$template = EB::themes();
		$template->set('block', $block);
		$template->set('url', $url);
		$contents = $template->output('site/blocks/pdf');

		return $contents;
	}
}
