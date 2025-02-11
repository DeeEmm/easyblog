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

class EasyBlogBlockHandlerAlerts extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-exclamation';
	public $element = 'blockquote';

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
		$data = [
			'type' => 'warning',
			'content' => '<strong>' . JText::_('COM_EASYBLOG_BLOCKS_ALERTS_PREVIEW_TITLE') . '</strong><br />' . JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NOTE_ENTER_IMPORTANT_ALERTS')
		];

		return (object) $data;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$types = [
			'success',
			'info',
			'warning',
			'danger'
		];

		$theme = EB::themes();
		$theme->set('types', $types);
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
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