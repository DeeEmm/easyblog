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

class EasyBlogImage extends EasyBlog
{
	public static $xssTags = ['abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--'];

	private $imageTypes = ['gif', 'jpg', 'jpeg', 'png', 'jfif', 'webp'];

	/**
	 * Proper check for file contents to ensure user doesn't upload anything funky
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canUpload($file, &$err)
	{
		$config = EB::config();

		if (empty($file['name'])) {
			$err = 'COM_EASYBLOG_WARNEMPTYFILE';
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = 'COM_EASYBLOG_WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		if (!$this->isImage($file['name'])) {
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		$maxWidth	= 160;
		$maxHeight	= 160;

		// maxsize should get from eblog config
		//$maxSize	= 2000000; //2MB
		//$maxSize	= 200000; //200KB

		// 1 megabyte == 1048576 byte
		$byte   		= 1048576;
		$uploadMaxsize  = (float) $config->get('main_upload_image_size', 0 );
		$maxSize 		= $uploadMaxsize * $byte;

		if ($maxSize > 0 && (float) $file['size'] > $maxSize) {
			$err = 'COM_EASYBLOG_WARNFILETOOLARGE';
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;

		if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
			$err = 'COM_EASYBLOG_WARNINVALIDIMG';
			return false;
		}

		return true;
	}

	/**
	 * Proper check for file contents to ensure user doesn't upload anything funky
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canUploadFile($file)
	{
		if (!isset($file['name']) || empty($file['name'])) {
			return EB::exception('COM_EASYBLOG_IMAGE_UPLOADER_PLEASE_INPUT_A_FILE_FOR_UPLOAD', EASYBLOG_MSG_ERROR);
		}

		// add this required "error" key if missing this.
		if (!isset($file['error'])) {
			$file['error'] = 0;
		}

		// Get the extension
		$extension = JFile::getExt($file['name']);
		$extension = strtolower($extension);

		// Check for allowed extensions
		if (!$this->isExtensionAllowed($extension)) {
			return EB::exception('COM_EASYBLOG_FILE_NOT_ALLOWED', EASYBLOG_MSG_ERROR);
		}

		// Ensure that the file that is being uploaded isn't too huge
		$fileSize = (int) $file['size'];

		if ($this->isExceededFilesizeLimit($fileSize)) {
			return EB::exception('COM_EASYBLOG_WARNFILETOOLARGE', EASYBLOG_MSG_ERROR);
		}

		// Ensure that the user doesn't do any funky stuff to the image
		if ($this->isImage($file['name'])) {
			$result = \JFilterInput::isSafeFile($file);

			if (!$result) {
				return EB::exception('COM_EASYBLOG_FILE_CONTAIN_XSS', EASYBLOG_MSG_ERROR);
			}
		}

		if ($this->containsXSS($file['tmp_name'])) {
			return EB::exception('COM_EASYBLOG_FILE_CONTAIN_XSS', EASYBLOG_MSG_ERROR);
		}

		return true;
	}

	/**
	 * Converts base64 image values into proper image data
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function convertBase64($str)
	{
		$image = str_replace('data:image/png;base64,', '', $str);
		$image = str_replace(' ', '+', $image);
		$image = base64_decode($image);

		return $image;
	}

	/**
	 * Downloads an external image given a url
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function download($url)
	{
		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		$containsXss = $this->contentsContainXss($contents);

		if ($containsXss) {
			throw new Exception('Content of url contains known xss codes');
		}

		// getimagesizefromstring is only available from php 5.4 onwards :(
		// so we need to store the strings as a file first.
		$tmpName = md5($url);

		$jConfig = EB::jConfig();
		$tmpPath = $jConfig->get('tmp_path') . '/' . $tmpName;

		JFile::write($tmpPath, $contents);

		$extension = $this->getExtension($tmpPath);

		// Check for allowed extensions
		if (!$this->isExtensionAllowed($extension)) {

			JFile::delete($tmpPath);

			return EB::exception('COM_EASYBLOG_FILE_NOT_ALLOWED', EASYBLOG_MSG_ERROR);
		}

		// Ensure that the file that is being uploaded isn't too huge
		$fileSize = (int) filesize($tmpPath);
		$exceededFilesize = $this->isExceededFilesizeLimit($fileSize);

		if ($exceededFilesize) {
			JFile::delete($tmpPath);
			return EB::exception('COM_EASYBLOG_WARNFILETOOLARGE', EASYBLOG_MSG_ERROR);
		}


		// Prepare the image data
		$file = getimagesize($tmpPath);
		$file['name'] = basename($url);
		$file['tmp_name'] = $tmpPath;
		$file['type'] = $file['mime'];

		$media = EB::mediamanager();
		$uri = 'user:' . $this->my->id;

		$adapter = $media->getAdapter($uri);
		$result = $adapter->upload($file);

		return $result;
	}

	/**
	 * Given a url for the file, get the name
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getFileName($imageUrl)
	{
		if (!$imageUrl) {
			return '';
		}

		// Unsplash photo does not have extension name on their link
		// So have to return back the url
		if (EB::unsplash()->isValidUrl($imageUrl)) {
			return $imageUrl;
		}

		$fileName = basename($imageUrl);
		return $fileName;
	}

	/**
	 * Retrieves a file extension given the name
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function getExtension($path)
	{
		$info = getimagesize($path);

		switch ($info['mime']) {
			case 'image/jpeg':
				$extension  = 'jpg';
			break;

			case 'image/png':
			case 'image/x-png':
			default:
				$extension  = 'png';
			break;
		}

		return $extension;
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return file type
	 */
	public static function getTypeIcon($fileName)
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Retrieve the file name of the fallback image of a webp image
	 *
	 * NOTE: Fallback image is needed due to webp image is not yet 100% supported at all browsers
	 * @since	6.0.0
	 * @access	public
	 */
	public function getWebpFallbackName($fileName)
	{
		$fileName = $fileName . '-' . md5($fileName) . '.jpeg';

		return $fileName;
	}

	/**
	 * Determine if the image is on webp format
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isWebp($fileName)
	{
		$extension = JFile::getExt($fileName);

		return $extension == 'webp';
	}

	/**
	 * Checks if an image file name is an image type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isImage($fileName)
	{
		$extension = JFile::getExt($fileName);
		$extension = strtolower($extension);

		return in_array($extension, $this->imageTypes);
	}

	/**
	 * Checks if an image file name is an image type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isUnsplashImage($fileName)
	{
		if (stristr(strtolower($fileName), 'unsplash.com') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the image extension is allowed
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function isExtensionAllowed($extension)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

		// Check for allowed extensions
		$allowed = EBMM::getAllowedExtensions();

		if (!in_array($extension, $allowed)) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the image size exceeded limit
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function isExceededFilesizeLimit($size)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

		$maximumAllowed = EBMM::getAllowedFilesize();

		if ($maximumAllowed !== false && $size > $maximumAllowed) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the contents contains any of the known possible xss tags
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	private function contentsContainXss($contents)
	{
		foreach (self::$xssTags as $tag) {
			// If this tag is matched anywhere in the contents, we can safely assume that this file is dangerous
			if (stristr($contents, '<' . $tag . ' ') || stristr($contents, '<' . $tag . '>') || stristr($contents, '<?php') || stristr($contents, '?\>')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the file contains any funky html tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function containsXSS($path)
	{
		// Sanitize the content of the files
		$contents = file_get_contents($path, false, null, 0, 256);

		// If we can't read the file, just skip this altogether
		if (!$contents) {
			return false;
		}

		return $this->contentsContainXss($contents);
	}

	public function parseSize($size)
	{
		if ($size < 1024) {
			return $size . ' bytes';
		}
		else
		{
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	public static function imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	public static function countFiles( $dir )
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}

	public static function getAvatarDimension($avatar)
	{
		$config			= EB::config();

		//resize the avatar image
		$avatar	= JPath::clean( JPATH_ROOT . DIRECTORY_SEPARATOR . $avatar );
		$info	= @getimagesize($avatar);
		if(! $info === false)
		{
			$thumb	= EasyImageHelper::imageResize($info[0], $info[1], 60);
		}
		else
		{
			$thumb  = array( EBLOG_AVATAR_THUMB_WIDTH, EBLOG_AVATAR_THUMB_HEIGHT);
		}

		return $thumb;
	}

	/**
	 * Retrieves the relative path to the respective avatar storage
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getAvatarRelativePath($type = 'profile')
	{
		$config = EB::config();

		// Default path
		$path = '';

		if ($type == 'category') {
			$path = $config->get('main_categoryavatarpath');
		} else if($type == 'team') {
			$path = $config->get('main_teamavatarpath');
		} else {
			$path = $config->get('main_avatarpath');
		}

		// Ensure that there are no trailing slashes
		$path = rtrim($path, '/');

		return $path;
	}


	public static function rel2abs($rel, $base)
	{
		return EB::string()->rel2abs( $rel, $base );
	}

	/**
	 * Generates a standard response
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	private function getMessageObj($code = '', $message = '', $item = false)
	{
		$obj = new stdClass();
		$obj->code = $code;
		$obj->message = $message;

		if ($item) {
			$obj->item = $item;
		}

		return $obj;
	}

	/**
	 * Process image to be used in AMP Article
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processAMP($content, $userId = '')
	{
		$pattern = '#<img[^>]*>#i';
		preg_match_all($pattern, $content, $matches);

		if (!$matches) {
			return array();
		}

		foreach ($matches[0] as $image) {

			preg_match('/src=["\']([^"\']+)["\']/', $image, $src);

			$url = $src[1];

			if (stristr($url, 'https:') === false && stristr($url, 'http:') === false) {

				if (stristr($url, '//') === false) {

					$url = rtrim(JURI::root(), '/') . '/' . ltrim($url);
				} else {
					$uri = JURI::getInstance();

					$scheme = $uri->toString(array('scheme'));

					$scheme = str_replace('://', ':', $scheme);

					$url = $scheme . $url;
				}
			}

			// we need to supress the warning here in case allow_url_fopen disabled on the site. #865
			$imageData = @getimagesize($url);

			// If height is missing, we try to get the original image size config
			if (!$imageData || empty($imageData[1])) {

				$config = EB::config();
				$maxWidth	= $config->get( 'main_image_thumbnail_width' );
				$maxHeight	= $config->get( 'main_image_thumbnail_height' );

				if ($maxWidth) {
					$imageData = array($maxWidth . 'px', $maxHeight . 'px');
				} else {
					// If dimension still missing, we skip this image
					$content = str_ireplace($image, '', $content);
					continue;
				}
			}

			$coverInfo = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';

			$ampImage = '<amp-img src="' . $url . '" ' . $coverInfo .  ' layout="responsive" ></amp-img>';

			ob_start();
			echo '<!-- START -->';
			echo $ampImage;
			echo '<!-- END -->';
			$output = ob_get_contents();
			ob_end_clean();

			//For legacy gallery, it always be wrap in <p>. We need to take it out.
			$output = str_replace('<!-- START -->', '<p>', $output);
			$output = str_replace('<!-- END -->', '<p>', $output);

			$content = str_ireplace($image, $output, $content);
		}

		return $content;
	}

	/**
	 * Process image to be used in Instant Articles
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processInstantImages($content)
	{
		$pattern = '/<img[^>]*>/';

		preg_match_all($pattern, $content, $matches);

		if (!$matches) {
			return array();
		}

		foreach ($matches[0] as $image) {

			preg_match('/src="([^"]+)"/', $image, $src);

			$url = $src[1];

			if (stristr($url, 'https:') === false && stristr($url, 'http:') === false) {

				if (stristr($url, '//') === false) {

					$url = rtrim(JURI::root(), '/') . '/' . ltrim($url);
				} else {
					$uri = JURI::getInstance();

					$scheme = $uri->toString(array('scheme'));

					$scheme = str_replace('://', ':', $scheme);

					$url = $scheme . $url;
				}
			}

			$imageData = getimagesize($url);

			// If height is missing, we try to get the original image size config
			if (!$imageData || empty($imageData[1])) {

				$maxWidth = $this->config->get('main_image_thumbnail_width');
				$maxHeight = $this->config->get('main_image_thumbnail_height');

				if ($maxWidth) {
					$imageData = array($maxWidth . 'px', $maxHeight . 'px');
				} else {
					// If dimension still missing, we skip this image
					$content = str_ireplace($image, '', $content);
					continue;
				}
			}

			$coverInfo = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';

			$figure = '<figure><img src="' . $url . '" ' . $coverInfo .  '/></figure>';

			ob_start();
			echo '<!-- START -->';
			echo $figure;
			echo '<!-- END -->';
			$output = ob_get_contents();
			ob_end_clean();

			//For legacy gallery, it always be wrap in <p>. We need to take it out.
			$output = str_replace('<!-- START -->', '</p>', $output);
			$output = str_replace('<!-- END -->', '<p>', $output);

			$content = str_ireplace($image, $output, $content);
		}

		return $content;
	}
}
