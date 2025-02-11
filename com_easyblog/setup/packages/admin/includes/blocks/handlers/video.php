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

class EasyBlogBlockHandlerVideo extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-film';
	public $element = 'video';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the dimensions fieldset, font fieldset and text panel.
		$meta->dimensions->enabled = false;
		$meta->properties['fonts'] = false;
		$meta->properties['textpanel'] = false;

		// Set the template for the video player
		$template = EB::themes();
		$meta->player = $template->output('site/composer/blocks/handlers/video/player');

		return $meta;
	}

	public function data()
	{
		$data = new stdClass();
		$data->url = '';
		$data->width = '100%';
		$data->height = '';
		$data->ratio = '16:9';
		$data->autoplay = false;
		$data->loop = false;
		$data->muted = false;

		return $data;
	}

	/**
	 * We do not want to display anything
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		// If there's no url set on the block, we should just leave this to the parent to output the necessary data.
		if (!$block->data->url) {
			$meta = $this->meta();
			return $meta->html;
		}
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
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$ratioList = [
			[
				'name'      => 'Wide',
				'caption'   => '16:9',
				'value'     => '16:9',
				'padding'   => '56.25%',
				'classname' => 'ar-wide'
			],
			[
				'name'      => 'Normal',
				'caption'   => '4:3',
				'value'     => '4:3',
				'padding'   => '75%',
				'classname' => 'ar-photo'
			],
			[
				'name'      => 'Square',
				'caption'   => '1:1',
				'value'     => '1:1',
				'padding'   => '100%',
				'classname' => 'ar-square'
			],
			[
				'name'      => 'Unlocked',
				'caption'   => '<i class="fdi fa fa-unlock-alt"></i>',
				'value'     => '0',
				'padding'   => '100%',
				'classname' => 'ar-unlocked'
			]
		];

		$theme = EB::themes();
		$theme->set('ratioList', $ratioList);
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

	/**
	 * Displays the html output for a video block
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		if ($textOnly) {
			return;
		}

		// Ensure that we have the url of the video otherwise we wouldn't know how to display the video
		if (!isset($block->data->url) || !$block->data->url) {
			return;
		}

		$options = (array) $block->data;
		$output = EB::media()->renderVideoPlayer($block->data->url, $options, $useRelative);

		return $output;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$data = $block->data;

		// currently AMP doesn't support percentage so have to hardcode it
		$width = '400';
		$height = '250';

		$html = '<amp-video controls layout="responsive" width="' . $width . '" height="' . $height . '">';
		$html .= '<source src="' . $data->url . '" type="video/mp4" />';
		$html .= '<div fallback><p>This browser does not support the video element.</p></div>';
		$html .= '</amp-video>';

		return $html;
	}
}
