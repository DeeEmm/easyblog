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

require_once(__DIR__ . '/abstract.php');

class EasyBlogMediaManagerAmazonSource extends EasyBlogMediaManagerAbstractSource
{
	/**
	 * Get post id from uri
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	protected function getPostId($uri)
	{
		$test = $uri;

		if (strpos($test, '/') !== false) {
			$parts = explode('/', $test);
			$test = $parts[0];
		}

		$parts = explode('amazon:', $test);
		return isset($parts[1]) && $parts[1] ? $parts[1] : false;
	}

	/**
	 * Generate amazon image url based on uri and size
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function generateImageURL($uri, $size)
	{
		static $_cache = array();

		$idx = $uri . '_' . $size;

		if (!isset($_cache[$idx])) {

			$_cache[$idx] = '';

			$media = EB::table('Media');
			$media->load(array('uri' => $uri));

			if ($media->id) {

				if ($size == 'original') {
					$_cache[$idx] = $media->url;

					return $_cache[$idx];
				}

				$_cache[$idx] = $media->url;

				$variations = $media->getVariations();
				$key = 'system/' . $size;

				if ($variations && isset($variations[$key])) {
					$variant = $variations[$key];

					$_cache[$idx] = $variant->url;
				}
			}
		}

		return $_cache[$idx];
	}

	/**
	 * Retrieves a list of images the user has on Flickr
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false)
	{
		$storage = EB::storage(EASYBLOG_MEDIA_STORAGE_TYPE_AMAZON);

		// get data from
		$postId = $this->getPostId($uri);

		// Folder
		$folder = new stdClass();
		$folder->place = EBMM::getPlaceId($uri);
		$folder->title = EBMM::getTitle($uri);

		// simulate post url
		$folder->url = $storage->getPermalink(EBMM::getPath('post:' . $postId, ''));
		$folder->uri = $uri;
		$folder->key = EBMM::getKey($uri);
		$folder->type = 'folder';
		$folder->icon = EBMM::$icons['place/amazon'];
		$folder->root = true;
		$folder->scantime = 0;

		// There is a possibility that this object is already stored in the database
		$model = EB::model('MediaManager');
		$results = $model->getPlaceObjects($uri);

		if (!$results) {

			$folder->contents = array();
			$folder->contents['folder'] = array();
			$folder->contents['file'] = array();

			return $folder;
		}

		// Let's build the photos URL now.
		$items = EBMM::filegroup();

		if ($results) {
			foreach ($results as $item) {

				if ($item->type == 'image') {

					$sizes = array();

					// now we need to get others variations.
					if ($item->params) {
						$params = json_decode($item->params);
						$variations = (array) $params->variations;

						foreach ($variations as $variant) {
							$sizes[$variant->key] = $variant;
						}
					}

					$item->variations = $sizes;
				}
				$items['file'][] = $item;
			}
		}

		$folder->contents = $items;
		$folder->total = count($items['file']);

		return $folder;
	}


	/**
	 * Retrieves the file / folder information
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getItem($uri, $relative = false)
	{
		// always set as false
		$relative = false;

		// Profiling

		// Determines if this is a directory
		$placeId = EBMM::getPlaceId($uri);

		$start = microtime(true);

		// Get the path based on the uri
		$path = EBMM::getPath($uri);

		// Determines if this is a directory
		$isFolder = $placeId == $uri;

		// Determines if this is a file.
		$isFile = $isFolder ? false : true;

		$item = $isFolder ? $this->getFolderItem($uri) : $this->getFileItem($uri);

		// File stats
		$item->modified = 0;
		$item->size = 0;

		// Variations
		if ($item->type=='image') {

			$folderurl = dirname($item->url);
			$filename  = basename($path);

			$item->thumbnail = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $filename;
			$item->preview   = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;
			$item->variations = self::getVariations($uri, $relative);

			if (isset($item->variations['system/original'])) {
				$original = $item->variations['system/original'];
				$item->size = $original->size;
			}
		}

		$end = microtime(true);
		$scantime = ($end - $start) * 1000;

		$item->scantime = $scantime;

		return $item;
	}

	/**
	 * Creates a new item object for media manager
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	protected function createItem($uri)
	{
		$storage = EB::storage('amazon');

		$item = new stdClass();

		$postId = $this->getPostId($uri);

		$item->place = EBMM::getPlaceId($uri);
		$item->title = EBMM::getTitle($uri);

		// need to override the url to point to S3
		$url = EBMM::getUrl($uri, true);
		$url = ltrim($url, '/');
		$url = $postId .'/' . $url;

		$item->url = $storage->getPermalink($url);

		$item->uri = $uri;
		$item->key = EBMM::getKey($uri);

		return $item;
	}

	public function getVariations($uri, $relative = false)
	{
		$media = EB::table('Media');
		$media->load(array('uri' => $uri));

		$variations = $media->getVariations();

		return $variations;
	}

	/**
	 * Retrieve title from a filename
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getFileTitle($fileName)
	{
		// Break up file parts
		$parts = explode('.', $fileName);
		$title = $parts[0];

		return $title;
	}

	/**
	 * Allows user to upload file via media manager
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function upload($file, $folderUri = null)
	{
		$tmpFolderPrefix = '_tmp';

		$date = EB::date();

		// If $uri is not given, assume current user
		if (!$folderUri) {
			return EB::exception('COM_EB_MM_INVALID_MEDIA_URI');
		}

		// we need to craete a temp folder for upload process
		$postId = $this->getPostId($folderUri);

		//fake the post place
		$postPlaceId = 'post:'. $postId;

		// Get folder item and path
		$tmpFolderPath = EBMM::getPath($postPlaceId, JPATH_ROOT, $tmpFolderPrefix);

		// Create folder (and parent folders) if necessary
		$createFolder = JFolder::create($tmpFolderPath);

		if (!$createFolder) {
			return EB::exception('COM_EB_MM_UNABLE_TO_CREATE_TEMP_FOLDER');
		}

		// Sanitize filename, prevent filename collision.
		$fileName = JFile::makeSafe($file['name']);

		// Check whether that is valid filename, if not we have to convert the filename to date time
		$requirePunctuation = false;

		if (strpos($fileName, '.') === false) {
			$requirePunctuation = true;
		}

		$fileName = $date->format("Ymd-His") . ($requirePunctuation ? '.' : '') . $fileName;

		// Check the image name is it got contain space, if yes need to replace to '-'
		// And this one have to do it after JFile::makeSafe() method if not it will not remove all the umlaut character and it won't generate the filename to datetime
		$fileName = str_replace(' ', '-', $fileName);

		// Ensure that there is no file naming collision
		$fileName = $this->getUniqueFileName($tmpFolderPath, $fileName);

		// Move uploaded file to its destination
		$uploadPath = $file['tmp_name'];
		$filePath = $tmpFolderPath . '/' . $fileName;

		// Try to move the uploaded file into the file path
		$state = JFile::copy($uploadPath, $filePath);

		if (!$state) {
			return EB::exception('COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR');
		}

		// Should we resize the original image?
		if (isset($file['type']) && $this->isImage($filePath, $file['type']) && $this->config->get('main_resize_original_image') && !$this->isGifImage($file['type'])) {
			$width = $this->config->get('main_original_image_width');
			$height = $this->config->get('main_original_image_height');
			$quality = $this->config->get('main_original_image_quality');

			$this->resizeImage($filePath, $width, $height, $quality);
		}

		$isImage = $this->isImage($filePath, $file['type']);

		// Image optimization
		if ($isImage) {
			$optimizer = EB::imageoptimizer();
			$optimizer->optimize($filePath);
		}

		// preparing for amazon sync
		$syncItems = array();
		$storage = EB::storage('amazon');

		$obj = new stdClass();
		$obj->source = $filePath;

		// build dest
		$dest = $filePath;
		// since the path now is pointing to local directory,
		// we need to replace the path.
		$dest = str_replace(JPATH_ROOT, '', $dest);
		$dest = ltrim($dest, '/');

		// remove the tmp folder prefix
		$dest = str_replace ($tmpFolderPrefix . '/', '/', $dest);
		$obj->dest = $dest;
		$obj->url = $storage->getPermalink($obj->dest);

		$obj->filename = basename($obj->dest);
		$obj->extension = EBMM::getExtension($obj->filename);
		$obj->type = EBMM::getType($obj->extension);
		$obj->mime = EBMM::getMimeType($obj->filename);
		$obj->isImage = $isImage;

		if ($obj->isImage) {
			// get mine type from physical image.
			$info = getimagesize($obj->source);
			$obj->mime = $info['mime'];
			$obj->width = $info[0];
			$obj->height = $info[1];
		}

		$obj->size = @filesize($filePath);

		$syncItems['original'] = $obj;

		$variations = null;

		// Build variations if it is an image file
		if (isset($file['type']) && $this->isImage($filePath, $file['type'])) {

			$variations = EB::imageset()->generate($filePath);

			// build the remote path into amazon.
			foreach ($variations as $key => $variant) {

				$obj = new stdClass();

				$obj->source = $variant->path;

				// build dest
				$dest = $variant->path;
				// since the path now is pointing to local directory,
				// we need to replace the path.
				$dest = str_replace(JPATH_ROOT, '', $dest);
				$dest = ltrim($dest, '/');

				// remove the tmp folder prefix
				$dest = str_replace ($tmpFolderPrefix . '/', '/', $dest);
				$obj->dest = $dest;
				$obj->url = $storage->getPermalink($obj->dest);

				$obj->filename = basename($obj->dest);
				$obj->extension = EBMM::getExtension($obj->filename);
				$obj->type = EBMM::getType($obj->extension);
				$obj->mime = EBMM::getMimeType($obj->filename);
				$obj->width = $variant->width;
				$obj->height = $variant->height;
				$obj->size = @filesize($variant->path);

				$syncItems[$key] = $obj;
			}
		}

		if ($syncItems) {
			foreach ($syncItems as $key => $sitem) {
				$state = $storage->push($sitem->filename, $sitem->source, $sitem->dest, $sitem->mime);
			}
		}

		// // Construct item
		$uri = $folderUri . '/' . $fileName;
		$item = $this->populateMediaObjects($uri, $syncItems);

		// now lets remove the tmp folder.
		@JFolder::delete($tmpFolderPath);

		return $item;
	}

	/**
	 * Resizes an image
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function resizeImage($path, $width, $height, $quality)
	{
		$image = EB::simpleImage();
		$image->load($path);

		$image->resizeWithin($width, $height);
		$state = $image->write($path, $quality);

		return $state;
	}

	/**
	 * Do nothing.
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function rebuildVariation($uri, $name)
	{
		return false;
	}


	/**
	 * Populate required media object into database
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function populateMediaObjects($uri, $variations)
	{
		// current unix timestamp
		$now = EB::date()->toUnix();

		$data = explode('/', $uri);
		$placeId = $data[0];
		$filename = $data[1];
		$title = $this->getFileTitle($filename);

		// Url should be the original source
		$oriObj = $variations['original'];

		$obj = new stdClass();
		$obj->uri = $uri;
		$obj->place = $placeId;
		$obj->parent = $placeId;
		$obj->filename = $filename;
		$obj->title = $title;
		$obj->url = $oriObj->url;
		$obj->key = EBMM::getKey($obj->uri);
		$obj->type = $oriObj->type;
		$obj->icon = EBMM::getIcon($oriObj->extension);
		$obj->modified = $now;
		$obj->created = JFactory::getDate()->toSql();
		$obj->created_by = $this->my->id;

		$meta = new stdClass();
		$meta->size = $oriObj->size;
		$meta->modified = $now;
		$meta->extension = $oriObj->extension;

		if ($oriObj->isImage) {

			$obj->preview = $variations['large']->url;

			$meta->thumbnail = $variations['thumbnail']->url;
			$items = array();

			foreach ($variations as $idx => $item) {

				// if ($idx == 'original') {
				// 	// continue
				// 	continue;
				// }

				$key = 'system/' . strtolower($idx);

				// Create variation
				$variation = new stdClass();
				$variation->key  = $key;
				$variation->name = ucfirst($idx);
				$variation->type = 'system';
				$variation->url  = $item->url;
				$variation->width  = $item->width;
				$variation->height = $item->height;
				$variation->size = $item->size;

				$items[$key] = $variation;
			}

			$meta->variations = $items;
		}

		$obj->params = json_encode($meta);

		$media = EB::table('Media');
		$media->bind($obj);
		$media->store();

		return $obj;
	}

	/**
	 * Remove a file and it's variation from amazon
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function delete($uri)
	{
		$media = EB::table('Media');
		$media->load(array('uri' => $uri));

		if (!$media->id) {
			return false;
		}

		$urls = array();

		$storage = EB::storage('amazon');

		// get ready the relationPath;
		$amazonRoot = $storage->getPermalink('');
		$amazonRoot = rtrim($amazonRoot, '/');

		$path = str_replace($amazonRoot, '', $media->url);
		$relativePath = ltrim($path, '/');
		$urls[] = $relativePath;

		// Remove variations before removing original file,
		// so if anything goes wrong when removing variations, the
		// original file is still intact.
		if ($media->type == 'image') {
			$variations = $media->getVariations();

			if ($variations) {
				foreach ($variations as $variant) {

					$vPath = str_replace($amazonRoot, '', $variant->url);
					$relativePath = ltrim($vPath, '/');
					$urls[] = $relativePath;
				}
			}

		}

		if ($urls) {
			$storage->delete($urls);
		}

		return true;
	}

	/**
	 * Determines if the image is a GIF image
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function isGifImage($type)
	{
		return $type == 'image/gif';
	}

	/**
	 * Determines if a given file is an image
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function isImage($path, $type)
	{
		static $items = array();

		if (!isset($items[$path])) {
			$isImage = @getimagesize($path) !== false;

			if ($isImage && $type != 'application/x-shockwave-flash') {
				$items[$path] = true;
			} else {
				$items[$path] = false;
			}
		}

		return $items[$path];
	}

	/**
	 * Creates a new image variation
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function createVariation($uri, $name, $params)
	{
		// enforce variation name to be in lowercase.
		$name = strtolower($name);

		// lets get the origina file from S3
		$media = EB::table('Media');
		$media->load(array('uri' => $uri));

		if (!$media->id) {
			return EB::exception('COM_EB_MM_VARIATION_ORIGINAL_NOT_FOUND');
		}

		$storage = EB::storage('amazon');

		$amazonRoot = $storage->getPermalink('');
		$amazonRoot = rtrim($amazonRoot, '/');

		$postId = $this->getPostId($uri);

		$path = str_replace($amazonRoot, '', $media->url);
		$relativePath = ltrim($path, '/');


		//fake the post place
		$postPlaceId = 'post:'. $postId;

		// Get folder item and path
		$tmpFolderPrefix = '_tmp';
		$tmpFolderPath = EBMM::getPath($postPlaceId, JPATH_ROOT, $tmpFolderPrefix);

		if (!JFolder::exists($tmpFolderPath)) {
			// Create folder (and parent folders) if necessary
			$createFolder = JFolder::create($tmpFolderPath);

			if (!$createFolder) {
				return EB::exception('COM_EB_MM_UNABLE_TO_CREATE_TEMP_FOLDER');
			}
		}

		// Get the file name of the image file
		$fileName = basename($uri);

		// tmp local file for resizing
		$tmpFilePath = $tmpFolderPath . '/' . $fileName;

		// pull original file from S3
		$storage->pull($relativePath, true, $tmpFilePath);
		$downloaded = JFile::exists($tmpFilePath);

		if (!$downloaded) {
			return EB::exception('COM_EB_MM_UNABLE_TO_FETCH_IMAGE_FROM_AMAZON');
		}

		$targetFileName = EBLOG_USER_VARIATION_PREFIX . '_' . $name . '_' . $fileName;

		$targetPath = $tmpFolderPath . '/' . $targetFileName;

		// Get the uri of the folder
		$folderUri = dirname($uri);

		// Store the new target uri
		$targetUri = $folderUri . '/' . $targetFileName;

		// Determines the resize quality
		$quality = isset($params->quality) ? $params->quality : $this->config->get('main_image_quality');

		// Resize image
		$image = EB::imagelib();
		$image->load($tmpFilePath);

		$image->resize($params->width, $params->height);
		$state = $image->save($targetPath, $quality);

		// If it hits an error we should return the exception instead.
		if (!$state) {
			return EB::exception('COM_EASYBLOG_FAILED_TO_CREATE_VARIATION_PERMISSIONS');
		}

		$optimizer = EB::imageoptimizer();
		$optimizer->optimize($targetPath);

		// successfully resize. let proceed to upload the new variation into S3
		$obj = new stdClass();

		$obj->source = $targetPath;

		// build dest
		$dest = $targetPath;
		// since the path now is pointing to local directory,
		// we need to replace the path.
		$dest = str_replace(JPATH_ROOT, '', $dest);
		$dest = ltrim($dest, '/');

		// remove the tmp folder prefix
		$dest = str_replace ($tmpFolderPrefix . '/', '/', $dest);
		$obj->dest = $dest;
		$obj->url = $storage->getPermalink($obj->dest);

		$obj->filename = basename($obj->dest);
		$obj->extension = EBMM::getExtension($obj->filename);
		$obj->type = EBMM::getType($obj->extension);
		$obj->mime = EBMM::getMimeType($obj->filename);
		$obj->width = $params->width;
		$obj->height = $params->height;
		$obj->size = @filesize($targetPath);

		$state = $storage->push($obj->filename, $obj->source, $obj->dest, $obj->mime);

		if (!$state) {
			return EB::exception('COM_EB_MM_UNABLE_TO_PUSH_VARIATION_TO_AMAZON');
		}

		// now lets remove the tmp folder.
		@JFolder::delete($tmpFolderPath);

		$item = $this->getItem($uri);

		// now we need to manually add this new variation into media object

		$idx = strtolower($name);
		$type = $this->variationTypes[EBLOG_USER_VARIATION_PREFIX];
		$key = $type . '/' . $idx;

		// Create variation
		$variation = new stdClass();
		$variation->key  = $key;
		$variation->name = ucfirst($idx);
		$variation->type = $type;
		$variation->url  = $obj->url;
		$variation->width  = $obj->width;
		$variation->height = $obj->height;
		$variation->size = $obj->size;

		$item->variations[$key] = $variation;

		return $item;
	}

	/**
	 * Deletes a variation of an image
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function deleteVariation($uri, $name)
	{
		$name = strtolower($name);

		$media = EB::table('Media');
		$media->load(array('uri' => $uri));

		if (!$media->id) {
			return EB::exception('COM_EB_MM_INVALID_MEDIA_URI');
		}

		$variations = $media->getVariations();

		$type = $this->variationTypes[EBLOG_USER_VARIATION_PREFIX];
		$key = $type . '/' . $name;

		if (!isset($variations[$key])) {
			return EB::exception('COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_AS_IT_DOESNT_EXISTS');
		}

		$variation = $variations[$key];


		$storage = EB::storage('amazon');

		// get ready the relationPath;
		$amazonRoot = $storage->getPermalink('');
		$amazonRoot = rtrim($amazonRoot, '/');

		$path = str_replace($amazonRoot, '', $variation->url);
		$relativePath = ltrim($path, '/');

		// delete this single variation from S3
		$storage->delete($relativePath);


		// now we need to remove the variation from the media object.
		unset($variations[$key]);
		$media->updateVariations($variations);

		return true;
	}

}
