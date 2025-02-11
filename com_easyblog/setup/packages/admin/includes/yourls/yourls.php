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

class EasyBlogYourls extends EasyBlog
{
	/**
	 * Determines if YOURLs is enabled
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function enabled()
	{
		if (!$this->config->get('social_yourls_shortener') || !$this->config->get('social_yourls_url') || !$this->config->get('social_yourls_token')) {
			return false;
		}
		return true;
	}

	/**
	 * Retrieves the endpoint url
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function getEndpointUrl()
	{
		static $url = null;

		if (is_null($url)) {
			$url = $this->config->get('social_yourls_url');

			// Ensure that the correct url is provided
			if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
				$url = 'https://' . $url;
			}
		}

		return $url;
	}

	/**
	 * Retrieves the shortened url
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function getShortenedUrl(EasyBlogPost $post, $original)
	{
		static $cache = array();

		$key = $post->id;

		if (!isset($cache[$key])) {
			$params = $post->getParams(false);
			$yourls = $params->get('yourls', false);

			// Shorten urls on the fly
			if (!$yourls && $this->config->get('social_yourls_onload')) {

				// Prevent recursion when crawled by YOURLs
				$agent = @$_SERVER['HTTP_USER_AGENT'];

				if (stristr($agent, 'YOURLS') !== false) {
					return;
				}

				$yourls = $this->shorten($post);
			}

			if (!$yourls) {
				$yourls = $original;
			}

			$cache[$key] = $yourls;
		}

		return $cache[$key];
	}

	/**
	 * Shorten a post's permalink with YOURLs
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function shorten(EasyBlogPost $post)
	{
		if (!$this->enabled()) {
			return;
		}

		// Get the true permalink to the post
		$permalink = $post->getPermalink(false, true);

		$data = array(
			'signature' => $this->config->get('social_yourls_token'),
			'action' => 'shorturl',
			'url' => $permalink,
			'format' => 'simple'
		);

		$endpoint = $this->getEndpointUrl() . '/yourls-api.php';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$url = curl_exec($ch);

		$post->saveParam('yourls', $url);

		return $url;
	}
}
