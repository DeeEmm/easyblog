<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogCrawler
{
	private $hooks = [];
	private $contents = null;

	/**
	 * Invoke the crawling.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function crawl($url)
	{
		$tmp = str_ireplace(array('http://', 'https://'), '', $url);

		if (!EB::string()->isValidDomain($tmp)) {
			return false;
		}

		// Ensure that urls always contains a protocol
		if (stristr($url, 'http://') === false && stristr($url , 'https://') === false) {
			$url = 'http://' . $url;
		}

		// Normalize the video link to oembed URL
		// E.g. Vimeo video link , Vimeo server will block the site IP address if keep crawl to their video page directly.
		$url = $this->normalizeCrawlURL($url);

		// Load up the connector first.
		$connector = FH::connector($url);
		$connector->setUserAgent('Facebot');
		$connector->execute();

		// Get the result and parse them.
		$info = parse_url($url);
		$this->contents	= $connector->getResult();

		// Replace any href="// with href="scheme://"
		$this->contents = str_ireplace('src="//', 'src="' . $info['scheme'] . '://', $this->contents);

		// // Get the final url, if there's any redirection.
		// $originalUrl = $url;
		// $url = $connector->getFinalUrl($url);

		$state = $this->parse($url);

		if (!$state) {
			return false;
		}

		return $this;
	}

	/**
	 * Loads adapters into the current namespace allowing the processing part
	 * to call these adapters.
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function parse($url)
	{
		// Load available hooks.
		$hooks = JFolder::files(__DIR__ . '/hooks');

		// Load up the html parser
		$parser	= EB::simplehtml()->str_get_html($this->contents);

		if (!$parser) {
			return false;
		}

		$info = parse_url($url);
		$uri = $info['scheme'] . '://' . $info['host'];
		$absoluteUrl = $url;

		foreach ($hooks as $hook) {
			$file = __DIR__ . '/hooks/' . $hook;

			require_once($file);
			$name = str_ireplace('.php', '', $hook);

			$class = 'EasyBlogCrawler' . ucfirst($name);

			// When item doesn't exist set it to false.
			if (!class_exists($class)) {
				continue;
			}

			$obj = new $class();
			$this->hooks[$name]	= $obj->process($parser, $this->contents, $uri, $absoluteUrl, $url, $this->hooks);
		}

		return true;
	}

	/**
	 * Retrieves the hooks values.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getData()
	{
		return $this->hooks;
	}

	/**
	 * Normalize the video link to oembed URL
	 *
	 * @since	5.4.5
	 * @access	public
	 */
	public function normalizeCrawlURL($OrigUrl = '')
	{
		// For now only check for Vimeo video link
		// We need to monitor and see if other video provider will ban on the site IP or not
		// For now only Vimeo video provider will ban on the site if keep crawl their video page directly.
		preg_match('/(vimeo|youtube|youtu)\.(com|be)\/(.*)/is', $OrigUrl, $matches);

		if (!$matches) {
			return $OrigUrl;
		}

		if ($matches[1] == 'youtube' || $matches[1] == 'youtu') {
			$encodedURL = urlencode($OrigUrl);
			$OrigUrl = 'https://www.youtube.com/oembed/?url=' . $encodedURL;

			return $OrigUrl;
		}

		if ($matches[1] == 'vimeo') {
			$encodedURL = urlencode($OrigUrl);
			$OrigUrl = 'https://vimeo.com/api/oembed.json?url=' . $encodedURL;
		}

		return $OrigUrl;
	}
}
