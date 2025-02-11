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

class EasyBlogMediaManagerFlickrSource extends EasyBlog
{
	private $oauth = null;
	private $login = false;

	public function __construct($lib)
	{
		$this->lib = $lib;

		parent::__construct();

		// Test if the user is already associated with Flickr
		$this->oauth = EB::table('OAuth');
		$this->oauth->loadByUser($this->my->id, EBLOG_OAUTH_FLICKR);
	}

	/**
	 * Creates the oauth client
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getClient()
	{
		// If the account is already associated, we just need to get the photos from Flickr
		$client = EB::oauth()->getClient(EBLOG_OAUTH_FLICKR);
		$client->setAccess($this->oauth->access_token);
		$client->setParams($this->oauth->params);

		return $client;
	}

	/**
	 * Determines if the current place needs a login screen. 
	 * Should be extended on child if needs overriding.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasLogin()
	{
		return $this->login;
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
		// Check if the user has authorized
		if (!$this->oauth->id || !$this->oauth->access_token) {

			$this->login = true;

			$redirect = base64_encode(rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=media&layout=flickrLogin&tmpl=component&callback=updateFlickrContent');
			$login = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&task=oauth.request&client=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $redirect;

			$theme = EB::themes();
			$theme->set('login', $login);
			$html = $theme->output('site/composer/media/flickr/login');

			return $html;
		}
		
		// Now we need to generate the images
		$theme = EB::themes();
		$theme->set('folder', $folder);

		$html = $theme->output('site/composer/media/contents');

		return $html;
	}

	/**
	 * Retrieves a list of images the user has on Flickr
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false, $page = 1)
	{
		// Folder
		$folder = new stdClass();
		$folder->place = 'flickr';
		$folder->title = JText::_('COM_EASYBLOG_MM_FLICKR');
		$folder->url = 'flickr';
		$folder->uri = 'flickr';
		$folder->key = 'flickr';
		$folder->type = 'folder';
		$folder->icon = EBMM::$icons['place/flickr'];
		$folder->root = true;
		$folder->scantime = 0;
		$folder->flickrLogin = false;

		// Check if the user has authorized
		if (!$this->oauth->id || !$this->oauth->access_token) {

			$folder->flickrLogin = true;
			$folder->contents = array();
			$folder->contents['folder'] = array();

			// Trick the javascript library into thinking that there are files
			$folder->contents['file'] = array('a');

			return $folder;
		}

		// If account is already associated, we just need to get the photos from their Flickr account.
		$client = $this->getClient();

		// Get list of photos from Flickr
		$result = $client->getPhotos($page);

		if (!$result) {
			$folder->contents = array();
			$folder->contents['folder'] = array();
			$folder->contents['file'] = array();

			return $folder;
		}

		// Let's build the photos URL now.
		$items = EBMM::filegroup();
		$uris = array();
		$i = 0;

		foreach ($result as $row) {
			$items['file'][] = $this->decorate($row, 'flickr:' . $row->id);
			$uris[$row->id] = 'flickr:' . $row->id;
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
	 * Returns the information of an object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItem($uri)
	{
		// Main flickr page, check
		if ($uri === 'flickr') {

			// Simulate the "folder" event by creating a fake meta object
			$meta = new stdClass();
			$meta->title = JText::_('COM_EASYBLOG_MM_FLICKR');
			$meta->uri = $uri;
			$meta->key = $this->lib->getKey($uri);
			$meta->items = new stdClass();
			$meta->items->folder = array();
			$meta->items->files = array();
			
			return $meta;
		}

		// We need to fix the uri because it is prefixed with flickr:12345
		$photoId = str_ireplace('flickr:', '', $uri);

		$client = $this->getClient();
		$result = $client->getPhoto($photoId);

		// Decorate the photo object for MM
		$photo = $this->decorate($result, $uri);



		return $photo;
	}

	/**
	 * Given a raw format of a flickr object and convert it into a media manager object.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function decorate(&$item, $uri)
	{
		$obj = new stdClass();
		$obj->uri = $uri;
		$obj->place = 'flickr';
		$obj->filename = $item->title;
		$obj->title = $item->title;

		// Url should be the original source
		$obj->url = $item->sizes['original']->source;
		$obj->key = EBMM::getKey('flickr:' . $item->id);
		$obj->type = 'image';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $item->dateupload;
		$obj->size = 0;

		$obj->extension = $item->extension;
		$obj->thumbnail = $item->sizes['medium']->source;
		$obj->preview = $item->sizes['medium']->source;

		$obj->variations = array();

		foreach ($item->sizes as $size) {

			$key = 'system/' . strtolower($size->title);

			// Create variation
			$variation = new stdClass();
			$variation->key  = $key;
			$variation->name = $size->title;
			$variation->type = 'system';
			$variation->url  = $size->source;
			$variation->width  = $size->width;
			$variation->height = $size->height;
			$variation->size = 0;

			$obj->variations[$key] = $variation;
		}

		return $obj;
	}

}
