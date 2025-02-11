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

class EasyBlogBlockHandlerTweet extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fab fa-twitter';
	public $element = 'embedded';

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
		$data = (object) array();

		return $data;
	}

	public function normalizeData($data)
	{
		$data->embed = html_entity_decode($data->embed);

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
		// if no url specified, return false.
		if (!isset($block->data->url) || !$block->data->url) {
			return false;
		}

		return true;
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		if ($textOnly) {
			return;
		}

		// If the source isn't set ignore this.
		if (!isset($block->data->embed) || !$block->data->embed) {
			return;
		}

		$template = EB::themes();
		$template->set('block', $block);
		$contents = $template->output('site/blocks/tweet');

		return $contents;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$url = $block->data->url;

		// retrieve the Tweet ID
		$regex = '#https?://twitter\.com/(?:\#!/)?(\w+)/status(es)?/(\d+)#is';

		if (preg_match($regex, $url, $match)) {

			$html = '<amp-twitter width="486" height="657" layout="responsive" data-tweetid="' . $match[3] . '" ></amp-twitter>';

			return $html;
		}

		return false;
	}
}
