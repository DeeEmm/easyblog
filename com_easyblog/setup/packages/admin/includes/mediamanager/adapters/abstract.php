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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogMediaManagerAbstractSource extends EasyBlog
{
	protected $rx_exclude;
	protected $variationTypes = [
		EBLOG_BLOG_IMAGE_PREFIX => 'blogimage',
		EBLOG_USER_VARIATION_PREFIX => 'user',
		EBLOG_SYSTEM_VARIATION_PREFIX => 'system'
	];

	public function __construct($lib)
	{
		parent::__construct();

		$this->lib = $lib;
		$this->rx_exclude = '/^\\.|^(index\\.html|svn|CVS|__MACOSX)$/uiU';
		$this->rx_variations = $this->rx_variations();
	}

	/**
	 * Creates a new item object for media manager
	 *
	 * @since	5.0
	 * @access	public
	 */
	protected function createItem($uri)
	{
		$item = new stdClass();

		$item->place = EBMM::getPlaceId($uri);
		$item->title = EBMM::getTitle($uri);

		$item->url = EBMM::getUrl($uri);
		$item->uri = $uri;
		$item->key = EBMM::getKey($uri);

		return $item;
	}

	/**
	 * This should be implemented by child instances
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function createVariation($uri, $name, $params)
	{
	}

	/**
	 * Deletes a file from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete($uri)
	{
		$item = $this->getItem($uri);
		$path = EBMM::getPath($uri);

		// Remove folder
		if ($item->type == 'folder') {
			$state = JFolder::delete($path);

			if (!$state) {
				return EB::exception('Unable to remove folder.');
			}
		}

		// Remove variations before removing original file,
		// so if anything goes wrong when removing variations, the
		// original file is still intact.
		if ($item->type=='image') {

			$state = $this->deleteVariations($uri);

			if ($state instanceof EasyBlogException) {
				return $state;
			}
		}

		$state = JFile::delete($path);

		if (!$state) {
			return EB::exception('Unable to remove file.');
		}

		return true;
	}


	/**
	 * Remove all image variations from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteVariations($uri)
	{
		// Accepts uri or item.
		if (is_string($uri)) {
			$item = $this->getItem($uri);
		} else {
			$item = $uri;
		}

		$errors = array();

		foreach ($item->variations as $key => $variation) {

			// Skip original variation
			if ($key == 'system/original') {
				continue;
			}

			// Get the variations path
			$file = EBMM::getPath($variation->uri);

			if (!JFile::exists($file)) {
				$errors[] = $file;

				continue;
			}

			$state = JFile::delete($file);

			if (!$state) {
				$errors[] = $file;
			}
		}

		if (count($errors)) {
			// TODO: Language
			return EB::exception('Unable to remove the following image variations: ' . implode(', ', $errors) . '.');
		}

		return true;
	}

	/**
	 * Determines if the file is excluded
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isExcluded($file)
	{
		if (preg_match($this->rx_exclude, $file)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if EasyBlog is running on dev mode
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function isDevelopmentMode()
	{
		return $this->config->get('main_development') == 'development';
	}

	/**
	 * Returns the structure of the folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function getFolderItem($uri)
	{
		$item = $this->createItem($uri);

		$item->type = 'folder';
		$item->icon = EBMM::$icons['folder'];
		$item->root = strpos($uri, '/') === false;
		$item->scantime = 0;
		$item->contents = EBMM::filegroup();
		$item->items = array();

		return $item;
	}

	/**
	 * Retrieves information about a uri
	 *
	 * @since	5.0
	 * @access	public
	 */
	protected function getFileItem($uri)
	{
		$item = $this->createItem($uri);

		$extension = EBMM::getExtension($item->title);

		$item->extension = $extension;
		$item->type = EBMM::getType($extension);
		$item->icon = EBMM::getIcon($extension);

		return $item;
	}

	/**
	 * Retrieves a list of items from a given uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItems($uri, $includeVariations = false)
	{
		static $fallbackNames = [];

		// Get path and folder
		$folder = $this->getFolderItem($uri);
		$folderpath = EBMM::getPath($uri);

		// Read the source directory
		$handle = @opendir($folderpath);

		if (!$handle) {
			return $folder;
		}

		// Scan
		// ~149ms on 700 files (first-time)
		// ~48ms on 700 files (subsequent)
		if ($this->isDevelopmentMode()) {
			$start = microtime(true);
		}

		// Filegroup is the array where files are stored.
		// Sort arrays are used to speed up file sorting.
		$filegroup = EBMM::filegroup();
		$sort_by_modified = EBMM::filegroup();
		$sort_by_title = EBMM::filegroup();

		// Temporary placeholders for images
		$index = 0;
		$images = [];

		// Temporary uri placeholders
		$uriIndex = 0;
		$uris = [];
		$urisIndex = [];

		// The strategy used here is to use a single loop that build:
		// - data that is ready-to-use
		// - sort arrays so sorting becomes cheap.
		// - variations
		$variations = [];
		$total = 0;

		$filenames = [];

		// Get the files first
		while (($name = readdir($handle)) !== false) {
			$filenames[] = $name;
		}

		// Sort the files in string length since we are unable to control the order from the readdir
		array_multisort(array_map('strlen', $filenames), $filenames);

		foreach ($filenames as $filename) {
			// Skip it if this is a fallback image
			if ($fallbackNames && in_array($filename, $fallbackNames)) {
				continue;
			}

			if (EB::image()->isWebp($filename)) {
				$name = JFile::stripExt($filename);
				// Get its fallback image name
				$fallbackName = $name . '-' . md5($name) . '.jpeg';

				// Cache the fallback name
				$fallbackNames[] = $fallbackName;
			}

			// Exclude files
			if (preg_match($this->rx_exclude, $filename)) {
				continue;
			}

			// Collect variations
			// ~8ms on 700 files
			if (preg_match($this->rx_variations, $filename, $parts)) {

				if (!$includeVariations) {
					continue;
				}

				// 2.0 thumbnails
				if (count($parts) == 6) {
					$source = $parts[5];
					$type = 'system';
					$name = 'thumb';

				// 3.5 thumbnails
				} else {
					$source = $parts[3];
					$type = $this->variationTypes[$parts[1]];
					$name = $parts[2];
				}

				$key = $type . '/' . $name;

				$variation = new stdClass();
				$variation->name = $name;
				$variation->url  = $folder->url . '/' . $filename;
				$variation->filename = $filename;

				if (!isset($variations[$source])) {
					$variations[$source] = array();
				}

				$variations[$source][$key] = $variation;
				continue;
			}

			$item = new stdClass();
			$item->title = $filename;
			$item->filename = $filename;
			$item->place = $folder->place;
			$item->uri = $folder->uri . '/' . $filename;
			$item->url = $folder->url . '/' . $filename;
			$item->key = EBMM::getKey($item->uri);

			// Folder
			$filepath = $folderpath . '/' . $filename;

			if (is_dir($filepath)) {
				$item->type = 'folder';
				$item->icon = EBMM::$icons['folder'];
				$item->modified = null;
			} else {
				// File
				$extension = EBMM::getExtension($filename);
				$item->extension = $extension;
				$item->type = EBMM::getType($extension);
				$item->icon = EBMM::getIcon($extension);
				$item->modified = @filemtime($filepath);
			}

			// Get item type
			$type = $item->type;

			// Add thumbnail & preview
			$item->thumbnail = '';
			$item->preview = '';

			if ($type == 'image') {
				$item->thumbnail = $folder->url . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $filename;
				$item->preview   = $folder->url . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;
			}

			if ($type != 'folder') {
				$type = 'file';
			}

			// Add to filegroup
			$filegroup[$type][] = $item;

			// Add to sort arrays
			$sort_by_title[$type][] = $item->title;
			$sort_by_modified[$type][] = $item->modified;

			// Insert images into the temporary array
			if ($item->type == 'image' && $includeVariations) {
				$images[] = $index;

				$index++;
			}

			if ($type == 'file') {
				$uris[] = $item->uri;
				$urisIndex[] = $uriIndex;

				$uriIndex++;
			}

			$total++;
		}

		// Sort folders
		// Folder doesn't need to be sorted by modification time.
		array_multisort($sort_by_title['folder'], SORT_ASC, $filegroup['folder']);

		// Sort files
		// ~0.6ms on 700 files
		array_multisort($sort_by_modified['file'], SORT_DESC, $sort_by_title['file'], SORT_ASC, $filegroup['file']);

		// Add variations & thumbnail property
		// ~6ms on 700 files
		if ($includeVariations && $images) {
			foreach($images as $imageIndex) {

				// Get the item
				$item = $filegroup['file'][$imageIndex];

				if ($item->type != 'image') {
					continue;
				}

				$sizes = isset($variations[$item->title]) ? $variations[$item->title] : array();

				// Original variation
				$variation = new stdClass();
				$variation->name = 'original';
				$variation->type = 'system';
				$variation->url  = $item->url;

				$sizes['system/original'] = $variation;

				$item->variations = $sizes;
			}
		}

		$folder->contents = $filegroup;
		$folder->total = $total;

		if ($this->isDevelopmentMode()) {
			$end = microtime(true);
			$scantime = ($end - $start) * 1000;

			$folder->scantime = $scantime;
		}

		if ($uris) {
			$model = EB::model('MediaManager');
			$result = $model->getObjects($uris);

			if ($result) {
				foreach ($urisIndex as $index) {
					$item = $filegroup['file'][$index];
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
		}

		// Debug
		// dump($folder->scantime, $result);

		return $folder;
	}

	/**
	 * Retrieve information about a file or a folder
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getItem($uri, $relative = false)
	{
		$start = microtime(true);

		$path = EBMM::getPath($uri);
		$isFolder = @is_dir($path);

		$item = $isFolder ? $this->getFolderItem($uri) : $this->getFileItem($uri);

		// File stats
		$item->modified = @filemtime($path);
		$item->size = @filesize($path);

		// Variations
		if ($item->type == 'image') {

			$folderurl = dirname($item->url);
			$filename  = basename($path);

			$item->thumbnail = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $filename;
			$item->preview   = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;
			$item->variations = $this->getVariations($uri, $relative);
		}

		if (is_dir($path)) {
			$item->type = 'folder';
			$item->icon = EBMM::$icons['folder'];
			$item->modified = null;
		} else {
			$extension = EBMM::getExtension($item->title);

			$item->extension = $extension;
			$item->type = EBMM::getType($extension);
			$item->icon = EBMM::getIcon($extension);
			$item->modified = @filemtime($path);
		}

		$end = microtime(true);
		$scantime = ($end - $start) * 1000;

		$item->scantime = $scantime;

		return $item;
	}

	/**
	 * Structures the variation object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getVariationItem($name, $url, $fileName)
	{
		$variation = new stdClass();

		$variation->name = $name;
		$variation->url = $url;
		$variation->filename = $fileName;

		return $variation;
	}

	public function getVariations($uri, $relative = false)
	{
		// Build variations
		$variations = array();

		// Url & paths
		$baseUri = dirname($uri);
		$filename = basename($uri);
		$filepath = EBMM::getPath($uri);
		$fileurl = EBMM::getUrl($uri, $relative);
		$folderpath = dirname($filepath);
		$folderurl = dirname($fileurl);

		// Original variation
		$variation = new stdClass();
		$variation->name = 'original';
		$variation->type = 'system';
		$variation->url  = $fileurl;

		// Get original variation width & height
		$info = @getimagesize($path);

		if ($info) {
			$variation->width  = $info[0];
			$variation->height = $info[1];
		}

		$variations['system/original'] = $variation;

		// Scan for variations
		$handle = @opendir($folderpath);

		if ($handle) {

			// Regex to match variations related to this file
			$filter = $this->rx_variations($filename);

			while (($filename = readdir($handle)) !== false) {

				if (!preg_match($filter, $filename, $parts)) {
					continue;
				}

				// 2.0 thumbnails
				if (count($parts)==6) {
					$source = $parts[5];
					$type   = 'system';
					$name   = 'thumb';

				// 3.5 thumbnails
				} else {
					$source = $parts[3];
					$type   = $this->variationTypes[$parts[1]];
					$name   = $parts[2];
				}

				// Variables
				$key  = $type . '/' . $name;
				$url  = $folderurl . '/' . $filename;
				$path = $folderpath . '/' . $filename;

				// Create variation
				$variation = new stdClass();
				$variation->name = $name;
				$variation->type = $type;
				$variation->url = $url;
				$variation->uri = $baseUri . '/' . basename($filename);

				// Get variation width & height
				$info = @getimagesize($path);

				if ($info) {
					$variation->width  = $info[0];
					$variation->height = $info[1];
				}

				$variation->size = @filesize($path);

				// Add to variations
				$variations[$key] = $variation;
			}
		}

		return $variations;
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
		return false;
	}

	/**
	 * Render folder items from media manager
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function renderFolderItems($folder, $nextPage = 1)
	{
		return false;
	}

	/**
	 * Render folder contents from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderFolderContents($folder)
	{
		$theme = EB::themes();
		$theme->set('folder', $folder);

		$html = $theme->output('site/composer/media/contents');

		return $html;
	}

	protected function rx_variations($filename=null)
	{
		$filename = empty($filename) ? '.*' : preg_quote($filename);

		$regex = '/^(' .

			// 3.5 thumbnails
			EBLOG_BLOG_IMAGE_PREFIX . '|' .
			EBLOG_USER_VARIATION_PREFIX . '|' .
			EBLOG_SYSTEM_VARIATION_PREFIX .
			')_([^_]*)_(' . $filename . ')' .

			// 2.0 thumbnails
			'|(' . EBLOG_MEDIA_THUMBNAIL_PREFIX . ')(' . $filename . ')/ui';

		return $regex;
	}

	public static function uniqueFilename($folderpath, $filename)
	{
		// Break up file parts
		$dot = strrpos($filename, '.');
		$name = $dot===false ? $filename : substr($filename, 0, $dot - 1);
		$extension = $dot===false ? '' : substr($filename, $dot);
		$timestamp = EB::date()->format("Ymd-His");
		$i = 1;

		while (JFile::exists($folderpath . '/' . $filename)) {
			$filename = $name . '_' . $timestamp . '_' . $i . $extension;
			$i++;
		}

		return $filename;
	}

	/**
	 * Alias function for uniqueFilename
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUniqueFileName($path, $fileName)
	{
		return self::uniqueFilename($path, $fileName);
	}


	public function upload($file, $folderuri=null)
	{
		// If $uri is not given, assume current user
		if (empty($folderuri)) {
			$my = EB::user();
			$folderuri = 'user:' . $my->id;
		}

		// Get folder item and path
		$folderpath = EBMM::getPath($folderuri);

		// Create folder (and parent folders) if necessary
		// We used to copy index.html on it. Now we don't.
		$createFolder = JFolder::create($folderpath);

		if (!$createFolder) {
			// TODO: Language
			return EB::exception('Unable to create folder');
		}

		// Sanitize filename, prevent filename collision.
		$filename = JFile::makeSafe($file['name']);

		// Check the image name is it got contain space, if yes need to replace to '-'
		// And this one have to do it after JFile::makeSafe() method if not it will not remove all the umlaut character and it won't generate the filename to datetime
		$filename = str_replace(' ', '-', $filename);

		$filename = self::uniqueFilename($folderpath, $filename);

		// Move uploaded file to its destination
		$uploadpath = $file['tmp_name'];
		$filepath = $folderpath . '/' . $filename;

		// Try to move the uploaded file into the file path
		$state = JFile::copy($uploadpath, $filepath);

		if (!$state) {
			return EB::exception('COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR');
		}

		// If this is an image, also create image variations.
		if (@getimagesize($filepath)!==false) {
			EB::imageset()->generate($filepath);
		}

		// Construct item
		$uri = $folderuri . '/' . $filename;
		$item = $this->getItem($uri);

		return $item;
	}

	/**
	 * Determines if this item is a legacy image file
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isLegacyImageVariation()
	{

	}

	/**
	 * Renames a file
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function rename($sourceUri, $targetUri)
	{
		$sourcePath = EBMM::getPath($sourceUri);
		$targetPath = EBMM::getPath($targetUri);

		$sourceItem = $this->getItem($sourceUri);

		// Get the new filename
		$newFileName = basename($targetPath);

		// Rename the main file
		$newUri = dirname($sourceUri) . '/' . $newFileName;
		$source = EBMM::getPath($sourceUri);
		$target = EBMM::getPath($newUri);

		$state = JFile::move($source, $target);

		// For images, we want to rename the variations as well.
		if ($sourceItem->type == 'image' && isset($sourceItem->variations)) {

			foreach ($sourceItem->variations as $key => $variation) {

				if ($key == 'system/original') {
					continue;
				}

				// Get the new uri
				$newUri = dirname($variation->uri) . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation->name . '_' . $newFileName;
				$source = EBMM::getPath($variation->uri);
				$target = EBMM::getPath($newUri);

				// Try to move the file now
				$state = JFile::move($source, $target);
			}
		}

	}

	/**
	 * Rename all variations when the main file is renamed
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renameVariations($sourceUri, $targetUri)
	{
		$fileName = basename($targetUri);
	}

	/**
	 * Moves a file or folder from destination to target
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function move($source, $target)
	{
		$sourceUri = EBMM::getUri($source);
		$targetUri = EBMM::getUri($target);

		// Get the absolute paths
		$sourcePath = EBMM::getPath($sourceUri);
		$targetPath = EBMM::getPath($targetUri);

		// Get the new filename
		$newFileName = basename($sourcePath);

		// Get the absolute path to the target file / folder
		$newTargetPath = $targetPath . '/' . $newFileName;
		$newTargetUri = $targetUri . '/' . $newFileName;

		// Get the item details
		$item = $this->getItem($sourceUri);

		// Test if the source items really exists
		if ($item->type == 'folder' && !JFolder::exists($sourcePath)) {
			return EB::exception('Unable to locate source folder to move.');
		}

		// Test if the source items really exists
		if ($item->type != 'folder' && !JFile::exists($sourcePath)) {
			return EB::exception('Unable to locate source file to move.');
		}

		// Test for target path
		if ($item->type == 'folder' && JFolder::exists($newTargetPath)) {
			return EB::exception('A folder with the same name exists on the specified target path.');
		}


		if ($item->type != 'folder' && JFile::exists($newTargetPath)) {
			return EB::exception('A file with the same name exists on the specified target path.');
		}

		// If this is a folder, just move the folder
		if ($item->type == 'folder') {

			$state = JFolder::move($sourcePath, $newTargetPath);

			if (!$state) {
				return EB::exception('Unable to move folder.');
			}
		}

		// Move the file
		if ($item->type != 'folder') {

			JFile::move($sourcePath, $newTargetPath);

			// Move variations
			if ($item->type == 'image') {

				// For images, we want to rename the variations as well.
				if ($item->type == 'image' && isset($item->variations)) {

					foreach ($item->variations as $key => $variation) {

						if ($key == 'system/original') {
							continue;
						}

						// Get the new uri
						$newUri = $targetUri . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation->name . '_' . $newFileName;
						$target = EBMM::getPath($newUri);

						// Get the original file for the variation
						$source = EBMM::getPath($variation->uri);

						// Check if the file exists first
						if (!JFile::exists($source)) {
							continue;
						}

						// Try to move the file now
						$state = JFile::move($source, $target);
					}
				}

			}
		}


		// Get the new item info about the moved object
		$item = $this->getItem($newTargetUri);

		return $item;
	}

	public function serializeParam($params)
	{
		$parts = array();

		$map = array(
			'width'   => 'w',
			'height'  => 'h',
			'x'       => 'x',
			'y'       => 'y',
			'quality' => 'q'
		);

		foreach($map as $key => $value) {
			if (!empty($params->$key)) {
			 $parts[] = $value . $params->$key;
			}
		}

		$str = implode(',', $parts);
	}
}
