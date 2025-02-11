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

class EasyBlogBlockHandlerButtons extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi far fa-square';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();
		$meta->dimensions->respectMinContentSize = true;

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;

		return $meta;
	}

	public function data()
	{
		$data = (object) [
			'caption' => JText::_('COM_EASYBLOG_BLOCKS_BUTTON_CONTENT'),
			'style' => 'btn-default',
			'size' => '',
			'link' => '',
			'nofollow' => 0,
			'target' => ''
		];

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
		// button not consider content. just return false.
		return false;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$buttons = [
			'default',
			'primary',
			'success',
			'info',
			'warning',
			'danger'
		];

		$theme = EB::themes();
		$theme->set('buttons', $buttons);
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
		$html = '<p><button class="btn-eb">'.$block->html.'</button></p>';

		return $html;
	}
}