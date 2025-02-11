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

class EasyBlogBlockHandlerComparison extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi far fa-file-image';
	public $nestable = false;
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		return $meta;
	}

	public function data()
	{
		$data = new stdClass();
		$data->left = true;
		$data->left_text = JText::_('COM_EB_LEFT');

		$data->right = true;
		$data->right_text = JText::_('COM_EB_RIGHT');

		$data->left_modified = false;
		$data->right_modified = false;

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.3.0
	 * @access  public
	 */
	public function validate($block)
	{
		$content = EB::blocks()->renderViewableBlock($block, true);

		// convert html entities back to it string. e.g. &nbsp; back to empty space
		$content = html_entity_decode($content);

		// strip html tags to precise length count.
		$content = strip_tags($content);

		// remove any blank space.
		$content = trim($content);

		// get content length
		$contentLength = EBString::strlen($content);

		return $contentLength > 0;
	}

	/**
	 * Retrieves the output for the block when it is being edited
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		$block->editableHtml = EBString::str_ireplace('(&quot;', "('", $block->editableHtml);
		$block->editableHtml = EBString::str_ireplace('&quot;)', "')", $block->editableHtml);

		return isset($block->editableHtml) ? $block->editableHtml : '';
	}

	/**
	 * Displays the html output for a comparison block
	 *
	 * @since   5.3.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		if ($textOnly) {
			return;
		}

		$block->html = EBString::str_ireplace('(&quot;', "('", $block->html);
		$block->html = EBString::str_ireplace('&quot;)', "')", $block->html);

		$options = (array) $block->data;

		$template = EB::themes();
		$template->set('block', $block);
		$contents = $template->output('site/blocks/comparison');

		return $contents;
	}
}
