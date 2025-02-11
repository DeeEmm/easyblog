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

class EasyBlogSimpleImage
{
	public $image = null;
	public $type = null;
	public $path = null;
	public $width = null;
	public $height = null;
	public $resource = null;

	/**
	 * Loads an image resource given the path to the file
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function load($filePath)
	{
		// ini_set('gd.jpeg_ignore_warning', 1);

		// Get the info about the image
		list($width, $height, $type, $attr) = @getimagesize($filePath);

		// Set the width / height
		$this->width = $width;
		$this->height = $height;

		// Set the image type
		$this->type = $type;

		// Set the file name
		$this->path = $filePath;

		// Create a new image resource
		if ($this->type == IMAGETYPE_JPEG) {

			// to by passed the warning message from gd-jpeg, libjpeg about the invalid SOS parameter although the jpeg is an valid one.
			ini_set('gd.jpeg_ignore_warning', 1);

			// lets create the resource for this jpeg file.
			$this->resource = @imagecreatefromjpeg($this->path);

			// although we've set the gd.jpeg_ignore_warning, but some server actually dont allow to ini_set during execution time, we need to perform further check.
			if (!$this->resource && function_exists('file_get_contents')) {
				// if the resourse is null, that means the jpeg hit the invalid SOS parameter issue. Lets try to create from string.
				$data = file_get_contents($this->path);
				$this->resource = @imagecreatefromstring($data);
			}
		}

		if ($this->type == IMAGETYPE_GIF) {
			$this->resource = imagecreatefromgif($this->path);
		}

		if ($this->type == IMAGETYPE_PNG) {
			$this->resource = imagecreatefrompng($this->path);
		}

		if (defined('IMAGETYPE_WEBP') && $this->type == IMAGETYPE_WEBP) {
			$this->resource = imagecreatefromwebp($this->path);	
		}

		// Fix the orientation of the image
		$this->fixOrientation();
	}

	/**
	 * Fix the orientation of the image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function fixOrientation()
	{
		// Exif methods must exist so we can read the exif data
		if (!function_exists('exif_read_data')) {
			return false;
		}

		// Since exif can only be read from jpeg files, skip this if it's not a jpeg file
		if ($this->type != IMAGETYPE_JPEG) {
			return false;
		}

		// Read the exif data
		$exif = @exif_read_data($this->path);

		// Get the orientation of the image
		$orientation = isset($exif['Orientation']) && !empty($exif['Orientation']) ? $exif['Orientation'] : 1;

		if ($orientation == 3) {
			$this->rotate(180);
		}

		if ($orientation == 5) {
			$this->rotate(-90);
		}

		if ($orientation == 6) {
			$this->rotate(90);
		}

		if ($orientation == 8) {
			$this->rotate(-90);
		}
	}

	/**
	 * Rotates an image resource
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function rotate($degrees)
	{
		$degrees = $degrees * -1;

		$this->resource = imagerotate($this->resource, $degrees, 0);

		// Get the new width and height
		$this->width = imageSX($this->resource);
		$this->height = imageSY($this->resource);
	}

	/**
	 * Retrieves the width of the image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Returns the height of the image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Resizes an image to the specified height
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resizeToHeight($height)
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;

		return $this->resize($width, $height);
	}

	/**
	 * Resizes an image to the specified width
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resizeToWidth($width)
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;

		return $this->resize($width, $height);
	}

	/**
	 * Scales an image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function scale($scale)
	{
		$width = $this->getWidth() * $scale / 100;
		$height = $this->getHeight() * $scale / 100;

		return $this->resize($width, $height);
	}


	/**
	 * Resizes the image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resize($width, $height)
	{
		$resource = '';

		if ($this->type == IMAGETYPE_JPEG || (defined('IMAGETYPE_WEBP') && $this->type == IMAGETYPE_WEBP)) {
			$resource = imagecreatetruecolor($width, $height);
			imagecopyresampled($resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}

		if ($this->type == IMAGETYPE_GIF) {
			$resource = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->resource);

			imagepalettecopy($resource, $this->resource);

			// Make this transparent
			imagefill($resource, 0, 0, $transparent);
			imagecolortransparent($resource, $transparent);
			imagetruecolortopalette($resource, true, 256);
			imagecopyresized($resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}

		if ($this->type == IMAGETYPE_PNG) {

			$resource = imagecreatetruecolor($width, $height);
			$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);

			imagealphablending($resource, false);
			imagesavealpha($resource, true);
			imagefilledrectangle($resource, 0, 0, $width, $height, $transparent);
			imagecopyresampled($resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}

		// Update with the new resource now
		$this->resource = $resource;
	}

	/**
	 * Resize an image within a specified maximum width / height canvas.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resizeWithin($maxWidth, $maxHeight)
	{
		// Get the width of the original image
		$originalImageWidth = $this->getWidth();

		// Get the height of the original image
		$originalImageHeight = $this->getHeight();

		// Default target width and height
		$finalWidth = $originalImageWidth;
		$finalHeight = $originalImageHeight;

		if ($finalWidth > $maxWidth) {
			$ratio = $maxWidth / $originalImageWidth;

			$finalWidth = $originalImageWidth * $ratio;
			$finalHeight = $originalImageHeight * $ratio;
		}

		if ($finalHeight > $maxHeight) {
			$ratio = $maxHeight / $originalImageHeight;

			$finalWidth = $originalImageWidth * $ratio;
			$finalHeight = $originalImageHeight * $ratio;
		}

		// Resize the image now
		return $this->resize($finalWidth, $finalHeight);
	}

	/**
	 * Resizes an image to fit the width and height specified
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resizeToFit($maxWidth, $maxHeight)
	{
		$originalWidth = $this->getWidth();
		$originalHeight = $this->getHeight();

		// Default target width and height
		$finalWidth = $originalWidth;
		$finalHeight = $originalHeight;

		$newX = 0;
		$newY = 0;
		$oriX = 0;
		$oriY = 0;

		$newWidth = $maxWidth;
		$newHeight = $maxHeight;

		if ($originalWidth > $maxWidth) {
			$ratio = $maxWidth / $originalWidth;

			$finalWidth = $originalWidth * $ratio;
			$finalHeight = $originalHeight * $ratio;
		}

		if ($finalHeight > $maxHeight) {
			$ratio = $maxHeight / $originalHeight;

			$finalWidth = $originalWidth * $ratio;
			$finalHeight = $originalHeight * $ratio;
		}

		if ($maxWidth > $finalWidth) {
			$newX = intval(($maxWidth - $finalWidth) / 2);
		}

		if ($maxHeight > $finalHeight) {
			$newY = intval(($maxHeight - $finalHeight) / 2);
		}

		// Build a new image resource
		$resource = imagecreatetruecolor($newWidth, $newHeight);

		if ($this->type == IMAGETYPE_JPEG || (defined('IMAGETYPE_WEBP') && $this->type == IMAGETYPE_WEBP)) {
			imagecopyresampled($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $originalWidth, $originalHeight);
		}

		if ($this->type == IMAGETYPE_GIF) {
			$transparent = imagecolortransparent($this->resource);

			imagepalettecopy($this->resource, $resource);
			imagefill($resource, 0, 0, $transparent);
			imagecolortransparent($resource, $transparent);
			imagetruecolortopalette($resource, true, 256);
			imagecopyresized($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $originalWidth, $originalHeight);
		}

		if ($this->type == IMAGETYPE_PNG) {
			$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);

			imagealphablending($resource, false);
			imagesavealpha($resource, true);
			imagefilledrectangle($resource, 0, 0, $newWidth, $newHeight, $transparent);
			imagecopyresampled($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $originalWidth, $originalHeight);
		}

		$this->resource = $resource;
	}

	/**
	 * Resize an image to fill a provided width / height
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function resizeToFill($maxWidth, $maxHeight)
	{
		$originalWidth = $this->getWidth();
		$originalHeight = $this->getHeight();

		// Final width / height
		$finalWidth = $originalWidth;
		$finalHeight = $originalHeight;

		// Get the ratio for the image
		$ratio = $maxWidth / $originalWidth;

		$finalWidth = $originalWidth * $ratio;
		$finalHeight = $originalHeight * $ratio;

		if ($finalHeight < $maxHeight) {
			$ratio = $maxHeight / $originalHeight;

			$finalWidth = $originalWidth * $ratio;
			$finalHeight = $originalHeight * $ratio;
		}

		$top = $maxHeight - $finalHeight;
		$left = $maxWidth - $finalWidth;
		$width = ($finalWidth + $left) / $ratio;
		$height = ($finalHeight + $top) / $ratio;

		$top = abs($top / 2) / $ratio;
		$left = abs($left / 2) / $ratio;

		// Rebuild the image resource
		$resource = imagecreatetruecolor($maxWidth, $maxHeight);

		if ($this->type == IMAGETYPE_JPEG || (defined('IMAGETYPE_WEBP') && $this->type == IMAGETYPE_WEBP)) {
			imagecopyresampled($resource, $this->resource, 0, 0, $left, $top, $maxWidth, $maxHeight, $width, $height);
		}

		if ($this->type == IMAGETYPE_GIF) {
			$transparent = imagecolortransparent($this->resource);

			imagepalettecopy($this->resource, $resource);
			imagefill($resource, 0, 0, $transparent);
			imagecolortransparent($resource, $transparent);
			imagetruecolortopalette($resource, true, 256);
			imagecopyresized($resource, $this->resource, 0, 0, $left, $top, $maxWidth, $maxHeight, $width, $height);
		}

		if ($this->type == IMAGETYPE_PNG) {
			$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);

			imagealphablending($resource, false);
			imagesavealpha($resource, true);
			imagefilledrectangle($resource, 0, 0, $maxWidth, $maxHeight, $transparent);
			imagecopyresampled($resource, $this->resource, 0, 0, $left, $top, $maxWidth, $maxHeight, $width, $height);
		}

		$this->resource = $resource;
	}

	/**
	 * Crops an image
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function crop($width, $height)
	{
		$originalWidth = $this->getWidth();
		$originalHeight = $this->getHeight();

		// Final width / height
		$finalWidth = $originalWidth;
		$finalHeight = $originalHeight;

		$newX = 0;
		$newY = 0;
		$oriX = 0;
		$oriY = 0;

		$oriX = intval(($originalWidth - $finalWidth) / 2);
		$oriY = intval(($originalHeight - $finalHeight) / 2);

		// Construct a new image resource
		$resource = imagecreatetruecolor($finalWidth, $finalHeight);

		if ($this->type == IMAGETYPE_JPEG || (defined('IMAGETYPE_WEBP') && $this->type == IMAGETYPE_WEBP)) {
			imagecopyresampled($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $finalWidth, $finalHeight);
		}

		if ($this->type == IMAGETYPE_GIF) {
			$transparent = imagecolortransparent($this->resource);
			imagepalettecopy($this->resource, $resource);
			imagefill($resource, 0, 0, $transparent);
			imagecolortransparent($resource, $transparent);
			imagetruecolortopalette($resource, true, 256);
			imagecopyresized($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $finalWidth, $finalHeight);
		}

		if ($this->type == IMAGETYPE_PNG) {
			$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);

			imagealphablending($resource, false);
			imagesavealpha($resource, true);
			imagefilledrectangle($resource, 0, 0, $finalWidth, $finalHeight, $transparent);
			imagecopyresampled($resource, $this->resource, $newX, $newY, $oriX, $oriY, $finalWidth, $finalHeight, $finalWidth, $finalHeight);
		}

		$this->resource = $resource;
	}

	/**
	 * Saves the current image resource
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function write($destination, $compression = 80, $type = null)
	{
		$contents = '';
		$type = is_null($type) ? $this->type : $type;

		if ($type == IMAGETYPE_JPEG) {
			ob_start();
			imagejpeg($this->resource, null, $compression);
			$contents = ob_get_contents();
			ob_end_clean();
		}

		if ($type == IMAGETYPE_GIF) {
			ob_start();
			imagegif($this->resource, null);
			$contents = ob_get_contents();
			ob_end_clean();
		}

		if ($type == IMAGETYPE_PNG) {
			ob_start();
			imagepng($this->resource, null);
			$contents = ob_get_contents();
			ob_end_clean();
		}

		if (defined('IMAGETYPE_WEBP') && $type == IMAGETYPE_WEBP) {
			ob_start();
			imagewebp($this->resource, null);
			$contents = ob_get_contents();
			ob_end_clean();
		}

		if (!$contents) {
			return false;
		}

		jimport('joomla.filesystem.file');

		// Try to save the file
		$state = JFile::write($destination, $contents);

		return $state;
	}

	/**
	 * Deprecated. Use @write instead
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 80, $permissions = null)
	{
		return $this->write($filename, $compression);
	}

	/**
	 * Outputs the image resource
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function output($type = null)
	{
		$type = is_null($type) ? $this->type : $type;

		if ($type == IMAGETYPE_JPEG) {
			imagejpeg($this->resource);
		}

		if ($type == IMAGETYPE_GIF) {
			imagegif($this->resource);
		}

		if ($type == IMAGETYPE_PNG) {
			imagepng($this->resource);
		}

		if (defined('IMAGETYPE_WEBP') && $type == IMAGETYPE_WEBP) {
			imagewebp($this->resource);
		}
	}
}
