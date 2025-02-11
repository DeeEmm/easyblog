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

class EasyBlogBlockHandlerAudio extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-headphones';
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
		$meta->properties['textpanel'] = false;

		// Get the audio player
		$theme = EB::themes();
		$meta->player = $theme->output('site/composer/blocks/handlers/audio/player');

		return $meta;
	}

	public function data()
	{
		$data = new stdClass();

		$data->url = '';
		$data->autoplay = false;
		$data->loop = false;

		// Fieldset options
		$data->showArtist = true;
		$data->showTrack = true;
		$data->showDownload = true;

		// Default data
		$data->artist = JText::_('COM_EASYBLOG_BLOCKS_AUDIO_ARTIST');
		$data->track = true;
		$data->download = '';

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
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		static $items = array();

		// We don't want to display anything here.
		if ($textOnly) {
			return;
		}

		$uid = uniqid();

		if (!isset($block->data->url) || !$block->data->url) {
			return;
		}

		$index = $block->data->uid . (int) $useRelative;

		if (isset($items[$index])) {
			return $items[$index];
		}

		$options = (array) $block->data;

		$output = EB::media()->renderAudioPlayer($block->data->url, $options, $useRelative);

		$items[$index] = $output;

		return $output;
	}
}
