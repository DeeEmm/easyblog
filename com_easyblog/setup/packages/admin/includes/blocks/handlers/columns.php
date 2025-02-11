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

class EasyBlogBlockHandlerColumns extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-columns';
	public $nestable = true;
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

		$column = new stdClass();
		$column->size = 6;
		$column->content = JText::_('COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE');

		$data->columns = array($column, $column);

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
		$content = EB::blocks()->renderViewableBlock($block);

		// convert html entities back to it string. e.g. &nbsp; back to empty space
		$content = html_entity_decode($content);

		// strip html tags to precise length count.
		// column block can have image block inside. we need to allow img tag.
		$content = strip_tags($content, '<img>');

		// remove any blank space.
		$content = trim($content);

		// get content length
		$contentLength = EBString::strlen($content);

		if ($contentLength > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retrieve the html to be rendered during post edit
	 *
	 * @since   6.0.7
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		if ($block->type == 'columns') {

			$editableHtmlContent = $block->editableHtml;

			// legacy here mean the column block HTML code store it from Easyblog 5.x
			// if return false mean the column block HTML code is generate from Easyblog 6.x
			$isLegacy = EBString::strpos($editableHtmlContent, '<div class="col col-md') !== false ? true : false;

			if ($isLegacy) {
				// Replace the 6.x column block new HTML DOM from previous version
				$block->editableHtml = EB::string()->replaceColumnBlockHTMLCode($editableHtmlContent);
			}
		}

		return $block->editableHtml;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		return $block->html;
	}
}
