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

class EasyBlogBlockHandlerHeading extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-heading';
	public $element = 'heading';

	public function data()
	{
		$params = $this->table->getParams();
		$default = strtoupper($params->get('default', 'h2'));

		$data = (object) array(
			'level' => $default,
			'default' => JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_ENTER_HEADING')
		);

		return $data;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		// Detect if there is anchor link in the content and replace it with amp 'style'
		$replace = '<span role="button" tabindex="0" on="tap:$2.scrollTo(duration=500, position=top)">$4</span>';
		$output = preg_replace('/<a(.*?)href="#([^"]+)"(.*?)>(.*?)<\/a>/', $replace, $block->html);

		// if there is a custom_id assigned, this is more likely to be the anchor content
		// So we wrap it in amp 'style' of anchor
		if (isset($block->data->custom_id)) {
			$output = '<span id="' . $block->data->custom_id . '">' . $output . '</span>';
		}

		return $output;
	}
}
