<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogMediaManagerPostSource extends EasyBlogMediaManagerAbstractSource
{
	/**
	 * Retrieves a list of items from a given uri
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getItems($uri, $includeVariations=false)
	{
		// List down posts from the site
		$model = EB::model('MediaManager');
		$posts = $model->getPosts();
	
		// Get path and folder
		$folder = $this->getFolderItem($uri);

		// Get the absolute path to the main "articles" folder
		$folderPath = EBMM::getPath($folder->uri);

		if (!$posts) {
			return $folder;
		}

		// Filegroup is the array where files are stored.
		// Sort arrays are used to speed up file sorting.
		$filegroup = EBMM::filegroup();

		// The strategy used here is to use a single loop that build:
		// - data that is ready-to-use
		// - sort arrays so sorting becomes cheap.
		// - variations
		$variations = array();
		$total = 0;

		foreach ($posts as $post) {

			// Get the folder path of the article
			$articlePath = $folderPath . '/' . $post->id;

			// Get the uri for the article
			$uri = 'post:' . $post->id;

			$items = parent::getItems($uri);

			$filegroup['folder'][] = $items;

			$total++;
		}

		// Set the folder contents
		$folder->contents = $filegroup;
		$folder->total = $total;

		return $folder;
	}

}
