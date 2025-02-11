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

class EasyBlogBlockHandlerTable extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-table';
	public $element = 'table';

	public function meta()
	{
		static $meta;
		if (isset($meta)) return $meta;

		$meta = parent::meta();

		return $meta;
	}

	public function data()
	{
		$data = (object) [
						'striped' => 1,
						'bordered' => 1,
						'hover' => 1,
						'condensed' => 1,
						'rows' => 2,
						'columns' => 3
		];

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
		// AMP doesn't allow inline style attribute
		$html = str_replace('style="table-layout: fixed"', '', $block->html);

		return $html;
	}
}
