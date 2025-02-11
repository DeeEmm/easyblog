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

class EasyBlogImageset
{
	public $sizes = [
		'large',
		'thumbnail',
		'amp'
	];

	/**
	 * This method will generate variation sizes used in EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getVariationsConfig($sizes = [])
	{
		$variations = [];

		if (!$sizes) {
			$sizes = $this->sizes;
		}

		$config = EB::config();

		foreach ($sizes as $size) {

			$variation = (object) [
				'name' => $size,
				'width' => $config->get('main_image_' . $size . '_width'),
				'height' => $config->get('main_image_' . $size . '_height'),
				'quality' => $config->get('main_image_' . $size . '_quality')
			];

			$variations[] = $variation;
		}

		return $variations;
	}

	/**
	 * Create image variations
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function generate($imagePath, $sizes = [])
	{
		$images = [];

		// Ensure that the image really exists on the site.
		$exists = file_exists($imagePath);

		if (!$exists) {
			return EB::exception('Invalid file path provided to generate imagesets.', EASYBLOG_MSG_ERROR);
		}

		// Get the original image file name
		$fileName = basename($imagePath);

		// Get the original image containing folder
		$folder = dirname($imagePath);

		if (!is_array($sizes)) {
			$sizes = [$sizes];
		}

		$variations = $this->getVariationsConfig($sizes);

		// Determines if there's a specific size to generate
		$optimizer = EB::imageoptimizer();

		foreach ($variations as $variation) {

			// Get the original image resource
			$original = EB::imagelib();
			$original->load($imagePath);

			// Clone the original image to avoid original image width and height being modified
			$image = clone($original);

			// For amp versions, we only want to resize it based on the width since there is a max width
			$respectAspectRatio = true;
			$preventUpsize = true;

			if ($variation->name === 'amp') {
				$variation->height = null;
				$preventUpsize = false;
			}

			$variation->path = $folder . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation->name . '_' . $fileName;

			$image->resize($variation->width, $variation->height, $respectAspectRatio, $preventUpsize);
			$image->save($variation->path, $variation->quality);

			// Optimize image
			$optimizer->optimize($variation->path);

			unset($image, $original);

			$images[$variation->name] = $variation;
		}

		return $images;
	}

	/**
	 * Regenerate the list of image variations
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function regenerate($imagePath, $variations)
	{
		if (!$variations) {
			return false;
		}

		$sizes = [];

		foreach ($variations as $variation) {

			// This method should not be responsible for the origianl variation
			if ($variation->key == 'system/original') {
				continue;
			}

			$sizes[] = $variation->name;
		}

		return $this->generate($imagePath, $sizes);
	}
}
