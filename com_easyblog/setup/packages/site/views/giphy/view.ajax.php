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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewGiphy extends EasyBlogView
{
	/**
	 * Search for GIFs and stickers of GIPHY via query
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function search()
	{
		EB::requireLogin();

		// Get the search query input
		$query = $this->input->get('query', '', 'string');

		// The type of the GIPHY item to be searched
		$type = $this->input->get('type', '', 'string');

		$giphy = EB::giphy();

		// Search and get the data
		$data = $giphy->getData($type, $query);

		$hasGiphies = true;

		if (!$data) {
			$data = false;
			$hasGiphies = false;
		}

		$themes = EB::themes();
		$themes->set('giphies', $data);
		$themes->set('type', $type);

		$html = $themes->output('site/composer/blocks/handlers/giphy/list');

		return $this->ajax->resolve($hasGiphies, $html);
	}
}
