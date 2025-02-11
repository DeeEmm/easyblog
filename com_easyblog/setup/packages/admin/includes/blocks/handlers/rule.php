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

class EasyBlogBlockHandlerRule extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-minus';
	public $nestable = false;
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
		$data = (object) [];

		return $data;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$styles = [
			'style1',
			'style2',
			'style3',
			'style4',
			'style5',
			'style6',
			'style7'
		];

		$themes = EB::themes();
		$themes->set('styles', $styles);
		$themes->set('block', $this);
		$themes->set('data', $meta->data);
		$themes->set('params', $this->table->getParams());

		$output = $themes->output('site/composer/blocks/handlers/rule/fieldset');

		return $output;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		//rule is not consider a content. always return false.
		return false;
	}
}