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

class EasyBlogMediaManagerUsersSource extends EasyBlogMediaManagerAbstractSource
{
	/**
	 * Retrieves a list of items from a given uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false)
	{
		// Retrieve a list of authors from the site.
		$model = EB::model('Blogger');
		$result = $model->getBloggers('latest', 0, $filter = 'showallblogger', '', array(), array($this->my->id));

		// Get path and folder
		$folder = $this->getFolderItem($uri);

		// Get the absolute path to the main "articles" folder
		$folderPath = EBMM::getPath($folder->uri);

		if (!$result) {
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

		// Map them with the profile table
		$authors = array();

		if ($result) {
			foreach ($result as $row) {
				$author = EB::user($row->id);

				$authorPath = $folderPath . '/' . $row->id;

				$uri = 'user:' . $row->id;

				$items = parent::getItems($uri);

				$filegroup['folder'][] = $items;

				$total++;
			}
		}


		// Set the folder contents
		$folder->contents = $filegroup;
		$folder->total = $total;

		return $folder;
	}

}
