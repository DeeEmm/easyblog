<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogMediaManagerEasySocialSource extends EasyBlogMediaManagerAbstractSource
{
	/**
	 * Determines if EasySocial integrations are available.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists()
	{
		$exists = EB::easysocial()->exists();

		if (!$exists) {
			return false;
		}
		
		ES::language()->loadSite();

		return true;
	}

	/**
	 * Retrieves a list of albums from a user
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false)
	{
		if (!$this->exists()) {
			return false;
		}

		// Determines if we are trying to view a single album or all albums
		$parts = explode(':', $uri);
		$viewAll = count($parts) == 1;

		// Let's build the photos URL now.
		$items = EBMM::filegroup();

		// Viewing of all albums
		if ($viewAll) {
			$model = ES::model('Albums');
			$albums = $model->getAlbums($this->my->id, SOCIAL_TYPE_USER);

			if ($albums) {
				foreach ($albums as $album) {
					$items['folder'][] = $this->decorateAlbum($album);
				}
			}

			// Folder
			$folder = new stdClass();
			$folder->place = 'easysocial';
			$folder->title = JText::_('COM_EASYBLOG_MM_PLACE_EASYSOCIAL');
			$folder->url = 'easysocial';
			$folder->uri = 'easysocial';
			$folder->key = 'easysocial';
			$folder->type = 'folder';
			$folder->icon = EBMM::$icons['place/easysocial'];
			$folder->root = true;
			$folder->scantime = 0;
			$folder->contents = $items;
			$folder->total = count($items['folder']);

		} else {

			// Get the album id it is trying to view
			$albumId = (int) $parts[1];
			
			$album = ES::table('Album');
			$album->load($albumId);

			// Render the photos model
			$model = ES::model('Photos');
			$options = array('album_id' => $albumId, 'pagination' => false);

			// Get the photos
			$photos = $model->getPhotos($options);
			if ($photos) {
				$urisIndex = array();
				$uris = array();
				$i = 0;

				foreach ($photos as $photo) {
					$items['file'][] = $this->getItem('easysocial:' . $album->id . '/' . $photo->id);
					$uris[] = 'easysocial:' . $album->id . '/' . $photo->id;
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
						}
					}
				}
			}

			// Folder
			$folder = new stdClass();
			$folder->place = 'easysocial';
			$folder->title = JText::_($album->get('title'));
			$folder->url = 'easysocial';
			$folder->uri = 'easysocial:' . $album->id;
			$folder->key = 'easysocial';
			$folder->type = 'folder';
			$folder->icon = EBMM::$icons['place/easysocial'];
			$folder->root = false;
			$folder->modified = $album->created;
			$folder->scantime = 0;
			$folder->size = 0;
			$folder->contents = $items;


			$folder->total = count($items);
		}

		return $folder;
	}

	/**
	 * Retrieves information about a particular item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItem($uri, $relative = false)
	{
		if (!$this->exists()) {
			return false;
		}

		// If this is an album request, we should list down all photos
		if ($this->isAlbum($uri)) {
			return $this->getItems($uri);
		}

		// Get the photo id
		$id = (int) EBMM::getFilename($uri);

		// Load the photo item
		$photo = FD::table('Photo');
		$photo->load($id);

		// Get the album object
		$album = $photo->getAlbum();

		$item = new stdClass();

		$item->place = 'easysocial';
		$item->filename = $photo->get('title');
		$item->title = $photo->get('title');
		$item->url = $photo->getSource('original');
		$item->uri = $uri;
		$item->path = 'easysocial';
		$item->type = 'image';
		$item->icon = EBMM::getIcon('image');
		$item->size = 0;
		$item->modified = $photo->created;
		$item->key = EBMM::getKey($uri);
		$item->thumbnail = $photo->getSource('thumbnail');
		$item->preview = $photo->getSource('thumbnail');
		$item->variations = $this->getPhotoVariations($photo, $album);
		$item->extension = $photo->getExtension('thumbnail');

		return $item;
	}

	/**
	 * Decorates the properties of an album
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function decorateAlbum(&$item)
	{
		$obj = new stdClass();
		$obj->place = 'easysocial';
		$obj->filename = JText::_($item->title);
		$obj->title = JText::_($item->title);
		$obj->uri = 'easysocial:' . $item->id;
		$obj->url = rtrim(JURI::root() , '/') . str_ireplace(JPATH_ROOT, '', $item->getStoragePath());
		$obj->key = EBMM::getKey('easysocial:' . $item->id);
		$obj->type = 'folder';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $item->created;
		$obj->size = 0;

		$obj->thumbnail = $item->getCoverUrl();
		$obj->preview = $item->getCoverUrl();

		return $obj;
	}

	/**
	 * Determines if a given uri is an album or a photo item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isAlbum($uri)
	{
		$parts = explode('/', $uri);

		if (count($parts) > 1) {
			return false;
		}

		return true;
	}

	/**
	 * Creates a new item object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createItem($uri)
	{
		return $item;
	}

	/**
	 * Retrieves variations for photos
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPhotoVariations(SocialTablePhoto $photo)
	{
		$result = array();

		require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/photos/photos.php');

		foreach (SocialPhotos::$sizes as $title => $size) {

			$key = 'system/' . strtolower($title);

			// Create variation
			$variation = new stdClass();
			$variation->key = $key;
			$variation->name = $title;
			$variation->type = 'system';
			$variation->url = $photo->getSource($title);
			$variation->width = $size['width'];
			$variation->height = $size['height'];
			$variation->size = 0;

			$result[$key] = $variation;
		}

		return $result;
	}
}
