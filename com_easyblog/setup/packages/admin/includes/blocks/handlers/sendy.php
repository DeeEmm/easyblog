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

class EasyBlogBlockHandlerSendy extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi far fa-paper-plane';
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
		$data->url = '';
		$data->list_id = '';

		$data->title = true;
		$data->title_text = JText::_('COM_EB_SUBSCRIBE_TO_UPDATES');

		$data->info = false;
		$data->info_text = JText::_('COM_EB_SENDY_INFO');

		$data->email_placeholder = JText::_('COM_EB_SENDY_EMAIL_PLACEHOLDER');
		$data->name = true;
		$data->name_placeholder = JText::_('COM_EB_SENDY_NAME_PLACEHOLDER');

		$data->button = JText::_('COM_EB_SENDY_SUBSCRIBE_BUTTON');

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

		$data = $block->data;

		if (!$data->url || !$data->list_id) {
			return;
		}

		$template = EB::themes();
		$template->set('block', $block);
		$template->set('data', $data);

		$contents = $template->output('site/blocks/sendy');

		return $contents;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.3.0
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		return $block->html;
	}
}
