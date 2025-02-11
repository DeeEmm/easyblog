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

class EasyBlogMediaManagerUnsplashSource extends EasyBlog
{
	public function __construct($lib)
	{
		$this->lib = $lib;

		parent::__construct();
	}

	/**
	 * Determines if the current place needs a login screen.
	 * Should be extended on child if needs overriding.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasLogin()
	{
		return false;
	}

	/**
	 * Render folder contents from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderFolderItems($folder, $nextPage = 1)
	{
		$nextPage++;

		// Now we need to generate the images
		$theme = EB::themes();
		$theme->set('items', $folder->contents['file']);
		$theme->set('uri', $folder->uri);
		$theme->set('nextPage', $nextPage);

		$html = $theme->output('site/composer/media/items');

		return $html;
	}

	/**
	 * Render folder contents from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderFolderContents($folder)
	{
		// Now we need to generate the images
		$theme = EB::themes();
		$theme->set('folder', $folder);

		$html = $theme->output('site/composer/media/contents');

		return $html;
	}

	/**
	 * Returns the information of an object
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getItem($uri, $relative = false, $options = [], $info = false)
	{
		// Main unsplash page, check
		if ($uri === 'unsplash') {

			// Simulate the "folder" event by creating a fake meta object
			$meta = new stdClass();
			$meta->title = JText::_('COM_EB_MM_UNSPLASH');
			$meta->uri = $uri;
			$meta->key = $this->lib->getKey($uri);
			$meta->items = new stdClass();
			$meta->items->folder = [];
			$meta->items->files = [];
			$meta->query = isset($options['query']) && $options['query'] ? $options['query'] : '';

			return $meta;
		}

		// We need to fix the uri because it is prefixed with unsplash:12345
		$photoId = str_ireplace('unsplash:', '', $uri);

		$options = [];
		$options['id'] = $photoId;

		$unsplash = EB::unsplash();
		$result = $unsplash->getData($options);

		// Decorate the photo object for MM
		$photo = $this->decorate($result, $uri, $info);

		return $photo;
	}

	/**
	 * Retrieves a list of photos on Unsplash
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false, $page = 1, $options = [])
	{
		// Folder
		$folder = new stdClass();
		$folder->place = 'unsplash';
		$folder->title = JText::_('COM_EB_MM_UNSPLASH');
		$folder->url = 'unsplash';
		$folder->uri = 'unsplash';
		$folder->key = 'unsplash';
		$folder->type = 'folder';
		$folder->icon = EBMM::$icons['place/unsplash'];
		$folder->root = true;
		$folder->scantime = 0;

		$options['page'] = $page;

		// Get list of photos from Unsplash
		$unsplash = EB::unsplash();
		$result = $unsplash->getData($options);

		if (!$result) {
			$folder->contents = [];
			$folder->contents['folder'] = [];
			$folder->contents['file'] = [];

			return $folder;
		}

		// Let's build the photos URL now.
		$items = EBMM::filegroup();
		$uris = [];
		$i = 0;

		foreach ($result as $row) {
			$items['file'][] = $this->decorate($row, 'unsplash:' . $row->id);
			$uris[$row->id] = 'unsplash:' . $row->id;
			$urisIndex[] = $i;

			$i++;
		}

		// There is a possibility that this object is already stored in the database
		$model = EB::model('MediaManager');
		$result = $model->getObjects($uris);

		if ($result) {
			foreach ($urisIndex as $index) {
				$item = $items['file'][$index];
				$itemUri = $item->uri;

				$object = isset($result[$itemUri]) ? $result[$itemUri] : false;

				// If we do have the data from the db, just use it
				if ($object) {
					$item->title = $result[$item->uri]->title;
				} else {
					// If the data isn't on the database yet, we use the file title
					$item->title = $item->filename;
				}
			}
		}

		$folder->contents = $items;
		$folder->total = count($items['file']);

		return $folder;
	}

	/**
	 * Given a raw format of a unsplash object and convert it into a media manager object.
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function decorate(&$item, $uri, $info = false)
	{
		$obj = new stdClass();
		$obj->uri = $uri;
		$obj->place = 'unsplash';

		// There is no photo title provided in the REST api
		$name = uniqid();
		$obj->filename = $name;
		$obj->title = $name;

		// Url should be the original source
		$obj->url = $item->urls->raw;
		$obj->key = EBMM::getKey('unsplash:' . $item->id);
		$obj->type = 'image';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $item->updated_at;
		$obj->size = 0;
		$obj->large = $item->urls->full;
		$obj->thumbnail = $item->urls->thumb;
		$obj->preview = $item->urls->thumb;
		$obj->extension = '';

		$themes = EB::themes();
		$themes->set('name', $item->user->name);
		$themes->set('username', $item->user->username);
		$themes->set('appName', $this->config->get('unsplash_app_name'));

		// This attribution needs to be injected into the image caption when it is being selected
		// This is required by Unsplash as mentioned here https://help.unsplash.com/en/articles/2511315-guideline-attribution
		$attribution = $themes->output('site/composer/media/unsplash/attribution');
		$obj->attribution = $attribution;

		$obj->variations = [];

		$urls = (array) $item->urls;

		// The original width and height
		$width = $item->width;
		$height = $item->height;

		// When the photo is being selected, need to trigger the download endpoint as required by Unsplash
		// https://help.unsplash.com/en/articles/2511258-guideline-triggering-a-download
		if ($info) {
			$connector = FH::connector($item->links->download_location);
			$connector->execute();
		}

		$variations = ['raw' => 'original', 'full' => 'large', 'regular' => 'medium', 'small' => 'small', 'thumb' => 'thumbnail'];

		// Unused variation
		if (isset($urls['small_s3'])) {
			unset($urls['small_s3']);
		}

		foreach ($urls as $type => $link) {
			$key = 'system/' . strtolower($variations[$type]);

			// Create variation
			$variation = new stdClass();
			$variation->key  = $key;
			$variation->name = strtolower($variations[$type]);
			$variation->type = 'system';
			$variation->url = $link;
			$variation->width  = $width;
			$variation->height = $height;

			// When the photo is being selected to get the info
			if (($type == 'regular' || $type == 'small' || $type == 'thumb') && $info) {
				$connector = FH::connector($link);
				$image = $connector->execute()->getResult();

				// Store the image to a temporary directory first
				$tmpPath = JPATH_ROOT . '/tmp/' . $item->id . '_' . $type . '_' . 'jpg';
				JFile::write($tmpPath, $image);

				// Get the image size now
				$imageData = getimagesize($tmpPath);

				// Now update the width and height
				$variation->width  = $imageData[0];
				$variation->height = $imageData[1];

				// Delete it back once got the size
				JFile::delete($tmpPath);
			}

			$variation->size = 0;

			$obj->variations[$key] = $variation;
		}

		return $obj;
	}
}
