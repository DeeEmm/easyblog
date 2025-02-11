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

class EasyBlogUnsplash extends EasyBlog
{
	private $base = 'https://api.unsplash.com';

	private $endpoints = [
		'search' => '/search/photos',
		'photos' => '/photos',
		'users' => '/users'
	];

	public $items = null;
	public $appName = null;
	public $accessKey = null;
	public $limit =  null;

	public function __construct()
	{
		parent::__construct();

		$this->items = null;

		$this->appName = $this->config->get('unsplash_app_name');
		$this->accessKey = $this->config->get('unsplash_access_key');
		$this->limit = $this->config->get('unsplash_limit');
	}

	/**
	 * Request data from the GIPHY API
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getData($options = [])
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$query = isset($options['query']) && $options['query'] ? $options['query'] : null;
		$username = isset($options['username']) && $options['username'] ? $options['username'] : null;
		$photoId = isset($options['id']) && $options['id'] ? $options['id'] : null;

		$data = $this->getItems($options);

		if ($query && isset($data->results)) {
			// Format the necessary data
			$this->items = $this->format($data->results);

			return $this->items;
		}

		if ((!$query || $username || $photoId) && !empty($data)) {
			// Format the necessary data
			$this->items = $this->format($data);

			return $this->items;
		}

		return false;
	}

	/**
	 * Get the photos from the connector with the query given
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getItems($options)
	{
		$query = isset($options['query']) && $options['query'] ? $options['query'] : null;
		$username = isset($options['username']) && $options['username'] ? $options['username'] : null;
		$page = isset($options['page']) && $options['page'] ? $options['page'] : null;
		$photoId = isset($options['id']) && $options['id'] ? $options['id'] : null;

		$url = $this->getUrl('photos', $options);
		$queries = $this->photos($page);

		if ($query) {
			$url = $this->getUrl('search');
			$queries = $this->search($query, $page);
		}

		if ($username) {
			$url = $this->getUrl('users', $options);
			$queries = $this->userPhotos($username, $page);
		}

		$url .= '?' . http_build_query($queries);

		$connector = FH::connector($url);
		$data = $connector->execute()->getResult();

		$data = json_decode($data);

		return $data;
	}

	/**
	 * Format the data given
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function format($data)
	{
		if (is_object($data)) {
			// Format the trigger the download link correctly
			$data->links->download_location .= '/?' . http_build_query(['client_id' => $this->accessKey]);

			return $data;
		}

		$items = [];

		foreach ($data as $item) {
			// Format the trigger the download link correctly
			$item->links->download_location .= '/?' . http_build_query(['client_id' => $this->accessKey]);

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Retrieve the url for a specific endpoint
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getUrl($endpoint, $options = [])
	{
		if (!isset($this->endpoints[$endpoint])) {
			return $this->base;
		}

		if ($endpoint == 'users') {
			return $this->base . $this->endpoints[$endpoint] . '/' . $options['username'] . $this->endpoints['photos'];
		}

		// The url to get a single photo based on the id given
		if ($endpoint == 'photos' && isset($options['id']) && $options['id']) {
			return $this->base . $this->endpoints[$endpoint] . '/' . $options['id'];
		}

		$url = $this->base . $this->endpoints[$endpoint];

		return $url;
	}

	/**
	 * Search photos Endpoint
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function search($query, $page = null)
	{
		$options = [];

		$options['client_id'] = $this->accessKey;
		$options['query'] = $query;
		$options['per_page'] = $this->limit;

		if ($page) {
			$options['page'] = $page;
		}

		return $options;
	}

	/**
	 * Get a list of photos Endpoint
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function photos($page = null)
	{
		$options = [];

		$options['client_id'] = $this->accessKey;
		$options['per_page'] = $this->limit;

		if ($page) {
			$options['page'] = $page;
		}

		return $options;
	}

	/**
	 * Get a list of photos of a user Endpoint
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function userPhotos($username, $page = null)
	{
		$options = [];

		$options['client_id'] = $this->accessKey;
		$options['username'] = $username;
		$options['per_page'] = $this->limit;

		if ($page) {
			$options['page'] = $page;
		}

		return $options;
	}

	/**
	 * Determine whether is it enabled or not
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isEnabled()
	{
		// Do not need to proceed if the one of the keys/ app name is not provided
		if (!$this->appName || !$this->accessKey) {
			return false;
		}

		if ($this->config->get('unsplash_enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Check for a valid GIPHY URL
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isValidUrl($url)
	{
		$pattern = "/(^|\\s)(https?:\\/\\/)?(([a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.([a-z]{2,6}))|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(:[0-9]{1,5})?(\\/.*)?/uism";

		$match = preg_match($pattern, $url);

		if (!$match) {
			return false;
		}

		// If it contains single or double quote, just return false
		if (stristr($url, '"') != false || stristr($url, "'") != false) {
			return false;
		}

		if (stristr($url, 'unsplash.com') == false) {
			return false;
		}

		return true;
	}
}
