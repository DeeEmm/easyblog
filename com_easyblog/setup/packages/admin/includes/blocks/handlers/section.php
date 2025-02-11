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

class EasyBlogBlockHandlerSection extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-object-group';
	public $nestable = true;
	public $element = 'section';

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
