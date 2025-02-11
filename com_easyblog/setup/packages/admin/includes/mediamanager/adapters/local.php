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

class EasyBlogMediaManagerLocalSource extends EasyBlogMediaManagerAbstractSource
{
	/**
	 * Retrieves the file / folder information
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getItem($uri, $relative = false)
	{
		// Profiling
		$start = microtime(true);

		// Get the path based on the uri
		$path = EBMM::getPath($uri);

		// Determines if this is a directory
		$isFolder = @is_dir($path);

		// Determines if this is a file.
		$isFile = @is_file($path);

		// Some folders might not be created yet until they decide to upload it later
		if ((EBMM::isPostPlace($uri) || EBMM::isUserPlace($uri) || $uri=="shared") && !$isFile) {
			$isFolder = true;
		}

		$item = $isFolder ? $this->getFolderItem($uri) : $this->getFileItem($uri);

		// File stats
		$item->modified = @filemtime($path);
		$item->size = @filesize($path);

		// Variations
		if ($item->type=='image') {

			if ($relative) {
				$item->url = EBMM::getUrl($item->uri, true);
			}

			$folderurl = dirname($item->url);
			$filename  = basename($path);


			$item->preview = $folderurl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $filename;

			// Backwards compaitiblity to always use thumbnail version
			$item->thumbnail = $item->preview;

			$item->variations = self::getVariations($uri, $relative);
		}

		$end = microtime(true);
		$scantime = ($end - $start) * 1000;

		$item->scantime = $scantime;

		return $item;
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
		$variation->key  = 'system/original';
		$variation->name = 'original';
		$variation->type = 'system';
		$variation->url  = $fileurl;
		$variation->uri = $baseUri . '/' . basename($filename);

		// Get original variation width & height
		$info = @getimagesize($filepath);

		if ($info) {
			$variation->width  = $info[0];
			$variation->height = $info[1];
		}

		$variation->size = @filesize($filepath);

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
				$variation->key = $key;
				$variation->name = $name;
				$variation->type = $type;
				$variation->url = $url;
				if ($relative) {
					$variation->url = EB::String()->abs2rel($url);
				}

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
	 * Generates a unique file name
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUniqueFileName($path, $fileName)
	{
		// Break up file parts
		$dot = strrpos($fileName, '.');
		$name = $dot === false ? $fileName : substr($fileName, 0, $dot - 1);
		$extension = $dot === false ? '' : substr($fileName, $dot);

		$timestamp = EB::date()->format("Ymd-His");

		$i = 1;

		while (JFile::exists($path . '/' . $fileName)) {
			$fileName = $name . '_' . $timestamp . '_' . $i . $extension;
			$i++;
		}

		return $fileName;
	}

	/**
	 * Allows user to upload file via media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function upload($file, $folderUri = null)
	{
		$date = EB::date();

		// If $uri is not given, assume current user
		if (!$folderUri) {
			$folderUri = 'user:' . $this->my->id;
		}

		// Get folder item and path
		$folderPath = EBMM::getPath($folderUri);

		// Create folder (and parent folders) if necessary
		$createFolder = JFolder::create($folderPath);

		if (!$createFolder) {
			return EB::exception('Unable to create folder');
		}

		// Sanitize filename, prevent filename collision.
		$fileName = JFile::makeSafe($file['name']);

		// Check whether that is valid filename, if not we have to convert the filename to date time
		$dateTime = strpos($fileName, '.') === false || strpos($fileName, '.') === 0 ? $date->format("Ymd-His") : '';
		$requirePunctuation = strpos($fileName, '.') === false;

		$fileName = $dateTime . ($requirePunctuation ? '.' : '') . $fileName;

		// Check the image name is it got contain space, if yes need to replace to '-'
		// And this one have to do it after JFile::makeSafe() method if not it will not remove all the umlaut character and it won't generate the filename to datetime
		$fileName = str_replace(' ', '-', $fileName);

		// Ensure that there is no file naming collision
		$fileName = $this->getUniqueFileName($folderPath, $fileName);

		// Move uploaded file to its destination
		$uploadPath = $file['tmp_name'];
		$filePath = $folderPath . '/' . $fileName;

		// Try to move the uploaded file into the file path
		$state = JFile::copy($uploadPath, $filePath);

		if (!$state) {
			return EB::exception('COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR');
		}

		$isImage = $this->isImage($filePath, $file['type']);

		if ($isImage) {
			$item = $this->initImage($filePath, $file, $folderUri, $fileName);
			$image = EB::image();

			// If this is a webp image, we need to construct a jpeg image for it as a fallback option due to not all browser supports it #2233
			if ($image->isWebp($fileName)) {
				$fileName = JFile::stripExt($fileName);

				// Concatenate with its hash value so that we know this is a fallback image
				$fileName = $image->getWebpFallbackName($fileName);
				$newfilePath = $folderPath . '/' . $fileName;

				JFile::copy($filePath, $newfilePath);

				// Initialize for this newly created jpeg image
				$fallbackItem = $this->initImage($newfilePath, $file, $folderUri, $fileName);

				$item->fallbackItemUri = $fallbackItem->uri;
			}
		}

		if (!$isImage) {
			$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

			// Construct item
			$uri = $folderUri . '/' . $fileName;
			$item = $this->getItem($uri, $useRelative);

			// Once a file is uploaded, we also want to store this into the database
			$this->lib->getMediaObject($uri, $useRelative);
		}

		return $item;
	}

	/**
	 * Perform the initialization for an image
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function initImage($filePath, $file, $folderUri, $fileName)
	{
		$original = EB::imagelib();
		$original->load($filePath);

		$originalImageChanges = false;

		if (EB::hasImageWatermark()) {
			$originalImageChanges = true;

			$original->insertWatermark();
		}

		// Determine if we should make any changes to the original image
		if ((isset($file['type']) && $this->isImage($filePath, $file['type']) && $this->config->get('main_resize_original_image') && !$this->isGifImage($file['type']))) {
			$width = $this->config->get('main_original_image_width');
			$height = $this->config->get('main_original_image_height');

			$originalImageChanges = true;

			$original->resize($width, $height);
		}

		if ($originalImageChanges) {
			$quality = $this->config->get('main_original_image_quality');

			$original->save($filePath, $quality);
		}

		// Image optimization
		$optimizer = EB::imageoptimizer();
		$optimizer->optimize($filePath);

		// Build variations if it is an image file
		if (isset($file['type'])) {
			EB::imageset()->generate($filePath);
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		// Construct item
		$uri = $folderUri . '/' . $fileName;
		$item = $this->getItem($uri, $useRelative);

		// Once a file is uploaded, we also want to store this into the database
		$this->lib->getMediaObject($uri, $useRelative);

		return $item;
	}

	/**
	 * Determines if a given file is an image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isImage($path, $type)
	{
		static $items = [];

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
	 * Determines if the image is a GIF image
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function isGifImage($type)
	{
		return $type == 'image/gif';
	}

	/**
	 * Deletes a file from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function delete($uri)
	{
		$item = $this->getItem($uri);
		$path = EBMM::getPath($uri);

		// Remove folder
		if ($item->type == 'folder') {
			if (!JFolder::delete($path)) {
				return EB::exception('COM_EASYBLOG_MM_UNABLE_TO_DELETE_FOLDER');
			}
		}

		// Remove variations before removing original file,
		// so if anything goes wrong when removing variations, the
		// original file is still intact.
		if ($item->type == 'image') {
			$state = $this->deleteVariations($uri);
			$image = EB::image();

			// Delete the fallback image of the webp image as well
			if ($image->isWebp($uri)) {
				$baseUri = str_replace($item->title, '', $uri);
				$fileName = JFile::stripExt($item->title);

				$fallbackUri = $baseUri . $image->getWebpFallbackName($fileName);
				$state = $this->deleteVariations($fallbackUri);

				$fallbackPath = EBMM::getPath($fallbackUri);
				$fallbackState = JFile::delete($fallbackPath);

				if (!$fallbackState) {
					return EB::exception(JText::sprintf('COM_EASYBLOG_MM_UNABLE_TO_DELETE_ITEM', $fallbackPath));
				}
			}

			if ($state instanceof EasyBlogException) {
				return $state;
			}
		}

		if ($item->type != 'folder') {
			$state = JFile::delete($path);

			if (!$state) {
				return EB::exception(JText::sprintf('COM_EASYBLOG_MM_UNABLE_TO_DELETE_ITEM', $path));
			}
		}

		return true;
	}

	/**
	 * Allows caller to create a new folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createFolder($uri, $folderName)
	{
		$place = $this->lib->getPlace($uri);
		$path = $this->lib->getPath($uri);

		// Ensure that the user has access to create folder
		if (!$place->acl->canCreateFolder) {
			return EB::exception('You are not allowed to create folder here');
		}

		// Ensure that the current folder really exists on the site
		$exists = JFolder::exists($path);

		if (!$exists) {
			// Try to create the folder as this could be the initial place folder
			$state = JFolder::create($path);

			if (!$state) {
				return EB::exception('COM_EASYBLOG_MM_CURRENT_FOLDER_DOESNT_EXIST');
			}
		}

		// Construct the new path
		$path = $path . '/' . $folderName;

		$exists = JFolder::exists($path);

		// Check if the folder already exists
		if ($exists) {
			return EB::exception('COM_EASYBLOG_MM_FOLDER_ALREADY_EXISTS');
		}

		// Try to create the new folder now
		$state = JFolder::create($path);

		if (!$state) {
			return EB::exception('COM_EASYBLOG_MM_UNABLE_TO_CREATE_FOLDER');
		}

		// Return the new uri
		$uri = $uri . '/' . $folderName;

		return $uri;
	}

	/**
	 * Renames a file or a folder
	 *
	 * @since	5.1
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

		return $state;
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

	/**
	 * Allows caller to build a missing variation on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function rebuildVariation($uri, $name)
	{
		// Get the original image file path
		$filePath = EBMM::getPath($uri);

		// Re-initialize the image
		EB::imageset()->generate($filePath, [$name]);

		return true;
	}

	/**
	 * Creates a new image variation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createVariation($uri, $name, $params)
	{
		// Get the absolute path of the image file
		$filePath = EBMM::getPath($uri);

		// Get the file name of the image file
		$fileName = basename($uri);

		// Get the absolute path to the file's container
		$folderPath = dirname($filePath);

		// Get the uri of the folder
		$folderUri = dirname($uri);

		// Build target name, filename, path, uri, quality.
		$i = 0;

		do {
			// Determines if we should add a postfix count if the variation name already exists before
			$targetName = $name . (empty($i) ? '' : $i);

			// Generate the file name for this new variation
			$targetFileName = EBLOG_USER_VARIATION_PREFIX . '_' . $targetName . '_' . $fileName;

			$targetPath = $folderPath . '/' . $targetFileName;

			$i++;

		} while(JFile::exists($targetPath));

		// Store the new target uri
		$targetUri = $folderUri . '/' . $targetFileName;

		// Determines the resize quality
		$quality = isset($params->quality) ? $params->quality : $this->config->get('main_image_quality');

		$image = EB::imagelib();
		$image->load($filePath);
		$image->resize($params->width, $params->height);
		$state = $image->save($targetPath, $quality);

		// If it hits an error we should return the exception instead.
		if (!$state) {
			return EB::exception('COM_EASYBLOG_FAILED_TO_CREATE_VARIATION_PERMISSIONS');
		}

		$item = $this->getItem($uri);

		return $item;
	}

	/**
	 * Deletes a variation of an image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteVariation($uri, $name)
	{
		// We only allow deletion of user prefixes.
		$fileName = EBLOG_USER_VARIATION_PREFIX . '_' . $name . '_' . basename($uri);
		$filePath = EBMM::getPath(dirname($uri)) . '/' . $fileName;

		// Test if the file really exists on the site
		$exists = JFile::exists($filePath);

		if (!$exists) {
			return EB::exception('COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_AS_IT_DOESNT_EXISTS');
		}

		// Try to delete the variation now
		$state = JFile::delete($filePath);

		if (!$state) {
			return EB::exception('COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_PERMISSIONS');
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

		$errors = [];

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
}
