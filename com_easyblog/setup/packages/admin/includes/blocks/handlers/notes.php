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

class EasyBlogBlockHandlerNotes extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi far fa-sticky-note';

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
		$data = [
			'type' => 'paragraph'
		];

		return (object) $data;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$types = [
			'paragraph',
			'list'
		];

		$themes = EB::themes();
		$themes->set('types', $types);

		return $themes->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function getAMPHtml($block)
	{
		return $block->html;
	}
}