<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/consumer.php');

class EasyBlogClientFlickr extends FlickrOauth
{
	public $callback = '';
	public $_access_token = '';
	private $param = '';

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = EB::request();

		$config = EB::config();
		$key = $config->get('integrations_flickr_api_key');
		$secret = $config->get('integrations_flickr_secret_key');

		parent::__construct($key, $secret);
	}

	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

	/**
	 * Sets the request token
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setRequestToken($token, $secret)
	{
		$this->_token = $token;
		$this->_secret = $secret;
	}

	/**
	 * Retrieves the request token for Flickr
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getRequestToken($callback = null)
	{
		$token = parent::getRequestToken($this->callback);

		$obj = new stdClass();
		$obj->token = $token['oauth_token'];
		$obj->secret = $token['oauth_token_secret'];

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier = $this->input->get('oauth_verifier', '');

		return $verifier;
	}

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to Facebook's login URL.
	 **/
	public function getAuthorizationURL($token)
	{
		$url = parent::getAuthorizeURL($token);

		return $url;
	}

	/**
	 * Retrieves the access token from Flickr
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAccess($verifier)
	{
		$this->token = new EBOAuthConsumer($this->_token, $this->_secret);

		$access = parent::getAccessToken($verifier);

		// If this hit an error, skip this.
		if (isset($access['oauth_problem'])) {
			return false;
		}

		if (!$access['oauth_token'] && !$access['oauth_token_secret']) {
			return false;
		}

		// Construct the object to return to the caller
		$obj = new stdClass();
		$obj->token = $access['oauth_token'];
		$obj->secret = $access['oauth_token_secret'];

		$params = new JRegistry();
		$params->set('user_id', $access['user_nsid']);
		$params->set('username', $access['username']);

		$obj->params = $params->toString();

		return $obj;
	}

	/**
	 * Retrieves the token object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function setToken()
	{
		$this->token = new EBOAuthConsumer($this->_access_token, $this->_secret_token);
	}

	/**
	 * Retrieves a list of Flickr photos from the user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPhotos($page = 1)
	{
		$sizesMapping = array('Square' => 'sq',
							'icon' => 't',
							'thumbnail' => 't',
							'small' => 's',
							'medium' => 'm',
							'large' => 'l',
							'original' => 'o');

		// Initialize the token for the current request.
		$this->setToken();

		$options = array('method' => 'flickr.people.getPhotos',
						 'format' => 'json',
						 'nojsoncallback' => 1,
						 'user_id' => 'me',
						 'privacy_filter' => 1,
						 'per_page' => 500,
						 'page' => $page,
						 'extras' => 'date_upload,original_format,media,description,license,url_sq,url_t,url_s,url_m,url_l,url_o'
						);
		$result = parent::get($options);

		// Ensure that we have valid data here
		if (empty($result->photos->photo)) {
			return false;
		}

		// Let's build the photos URL now.
		$photos = array();

		foreach ($result->photos->photo as $item) {

			$obj = new stdClass();
			$obj->title = $item->title;

			$obj->sizes	= array();

			foreach ($sizesMapping as $label => $prefix) {

				$info = new stdClass();
				$info->title = $label;

				$source = 'url_' . $prefix;

				// If an image source doesn't exist, we try to use the original copy
				if (!isset($item->$source)) {
					$info->width = $item->{'width_o'};
					$info->height = $item->{'height_o'};
					$info->source = $item->{'url_o'};
				} else {
					$info->width = $item->{'width_' . $prefix};
					$info->height = $item->{'height_' . $prefix};
					$info->source = $item->{'url_' . $prefix};
				}

				$obj->sizes[$label] = $info;
			}

			$obj->id = $item->id;
			$obj->dateupload = $item->dateupload;
			$obj->extension = $item->originalformat;
			
			$photos[]	= $obj;
		}

		return $photos;
	}

	/**
	 * Retrieves information about a single photo
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPhoto($photoId)
	{
		// Initialize the token for the current request.
		$this->setToken();

		$options = array('method' => 'flickr.photos.getInfo',
						'format' => 'json',
						'nojsoncallback' => 1,
						'photo_id' => $photoId,
						'privacy_filter' => 1
					);

		$result = parent::get($options);

		if (!$result->photo) {
			return false;
		}

		// Update the photo title
		$result->photo->title = $result->photo->title->_content;

		// Build the photo object
		$photo = $this->buildPhotoObject($result->photo);
		$photo->id = $result->photo->id;
		$photo->dateupload = $result->photo->dateuploaded;
		$photo->extension = $result->photo->originalformat;

		return $photo;
	}

	/**
	 * Formats the raw data provided by Flickr and get the correct URL to the Flickr image.
	 *
	 * @access	public
	 * @param	object	$photo	The metadata of the photo
	 * @return	object			A stdClass object with it's own properties.
	 */
	public function buildPhotoObject($photoItem)
	{
		$variations = array('original', 'large', 'medium', 'thumbnail', 'small', 'icon');

		// Initialize the token for the current request.
		$this->token = new EBOAuthConsumer($this->_access_token, $this->_secret_token);

		$options = array('method' => 'flickr.photos.getSizes',
						 'format' => 'json',
						 'nojsoncallback' => 1,
						 'photo_id' => $photoItem->id,
						 'privacy_filter' => 1
						);

		$result = parent::get($options);
		$sizes = $result->sizes->size;

		$photo = new stdClass();
		$photo->title = $photoItem->title;
		$photo->sizes	= array();

		foreach ($sizes as $size) {

			$variation = strtolower($size->label);

			// We only want sizes that are in the variations list
			if (!in_array($variation, $variations)) {
				continue;
			}

			$obj = new stdClass();
			$obj->title = $size->label;
			$obj->width = $size->width;
			$obj->height = $size->height;
			$obj->source = $size->source;

			$photo->sizes[$variation] = $obj;
		}

		// Since there is no "icon" size, we'll replicate it
		if ($photo->sizes && !isset($photo->sizes['icon'])) {
			$photo->sizes['icon'] = clone($photo->sizes['thumbnail']);
			$photo->sizes['icon']->title = JText::_('COM_EASYBLOG_MM_IMAGE_SIZE_ICON');
		}

		return $photo;
	}

	public function setParams($params)
	{
		$param 	= EB::registry($params);

		$this->_param	= $param;

		return $this->_param;
	}

	/**
	 * Sets the access token
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setAccess($access)
	{
		$access = new JRegistry($access);

		$this->_access_token = $access->get('token');
		$this->_secret_token = $access->get('secret');

		return true;
	}

	public function revokeApp()
	{
		return true;
	}
}
