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

class EasyBlogCrawlerOEmbed
{
	/**
	 * Ruleset to process document opengraph tags
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.
	 */
	public function process($parser, &$contents, $uri, $absoluteUrl, $originalUrl)
	{
		$oembed = new stdClass();
		$oembed->error = false;
		$oembed->errorMsg = JText::_('COM_EB_COMPOSER_BLOCK_EMBED_ERROR');

		// Metacafe videos
		if (stristr($uri, 'metacafe.com') !== false) {
			return $this->metacafe($parser);
		}

		// Ted videos
		if (stristr($uri, 'ted.com') !== false) {
			return $this->ted($oembed, $contents);
		}

		if (stristr( $uri , 'pastebin.com' ) !== false) {
			return $this->pastebin($oembed, $absoluteUrl);
		}

		if (stristr($uri, 'twitter.com') !== false) {
			return $this->twitter($oembed, $absoluteUrl);
		}

		if ($uri == 'https://gist.github.com') {
			return $this->gist($oembed, $absoluteUrl);
		}

		if (stristr( $uri , 'soundcloud.com' ) !== false) {
			return $this->soundCloud($oembed, $absoluteUrl);
		}

		if( stristr( $uri , 'mixcloud.com' ) !== false )
		{
			return $this->mixCloud( $parser , $oembed , $absoluteUrl );
		}

		if( stristr( $uri , 'spotify.com' ) !== false )
		{
			return $this->spotify( $oembed , $originalUrl );
		}

		if (stristr($uri, 'codepen.io') !== false) {
			return $this->jsonParser($parser, $contents, $oembed, $originalUrl);
		}

		if (stristr($uri, 'behance.net') !== false) {
			return $this->jsonParser($parser, $contents, $oembed, $originalUrl);
		}

		if (stristr($uri, 'youtube.com') !== false) {
			return $this->youtube($oembed, $originalUrl);
		}

		if (stristr($uri, 'dailymotion.com') !== false) {
			return $this->dailymotion($oembed, $originalUrl);
		}

		if (stristr($uri, 'www.slideshare.net') !== false) {
			return $this->slideshare($oembed, $originalUrl);
		}

		if (stristr($uri, 'vimeo.com') !== false) {
			return $this->vimeo($oembed, $originalUrl);
		}

		if (stristr($uri, 'tiktok.com') !== false) {
			return $this->tiktok($oembed, $originalUrl);
		}

		// get site url
		$siteuri = JURI::getInstance()->toString(['host']);

		// need to find a way to properly detect easysocial from this site.
		if (stristr($uri, $siteuri) !== false) {
			$juri = JUri::getInstance($originalUrl);
			$router = JFactory::getApplication()->getRouter();
			$query = $router->parse($juri);

			// check if this really from easysocial video
			if ((isset($query['option']) && $query['option'] == 'com_easysocial') && (isset($query['view']) && $query['view'] == 'videos')) {
				return $this->easysocial($oembed, $originalUrl, $query);
			}
		}

		if (stristr($uri, 'twitch.tv') !== false) {
			return $this->twitch($oembed, $originalUrl);
		}

		// Get a list of oembed nodes
		$nodes = $parser->find('link[type=application/json+oembed]');

		foreach ($nodes as $node) {

			// Get the oembed url
			if (!isset($node->attr['href'])) {
				continue;
			}

			// Get the oembed url from the doc
			$url = $node->attr['href'];

			// Load up the connector first.
			$connector = FH::connector($url);
			$contents = $connector->execute()->getResult();

			// We are retrieving json data
			$oembed = json_decode($contents);

			// Test if thumbnail_url is set so we can standardize this
			if (isset($oembed->thumbnail_url)) {
				$oembed->thumbnail = $oembed->thumbnail_url;
			}
		}

		return $oembed;
	}

	/**
	 * Processes videos from TED
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function ted(&$oembed, $contents)
	{
		$oembed = json_decode($contents);

		return $oembed;
	}

	public function jsonParser($parser, $contents, &$oembed, $absoluteUrl)
	{
		$obj = json_decode($contents);

		$oembed = $obj;

		return $oembed;
	}

	public function metacafe($parser)
	{
	}

	public function pastebin(&$oembed, $absoluteUrl)
	{
		$segment = str_ireplace('http://pastebin.com/', '', $absoluteUrl);

		$oembed->html = '<iframe src="http://pastebin.com/embed_iframe.php?i=' . $segment . '" style="border:none;width:100%"></iframe>';

		return $oembed;
	}

	public function twitch(&$oembed, $absoluteUrl)
	{
		$siteUrl = str_replace('https://', '', rtrim(JURI::root(), '/'));

		if (!FH::isHttps()) {
			$oembed->error = true;
			$oembed->errorMsg = JText::_('COM_EB_POST_COVER_HTTPS_REQUIRED');
			return $oembed;
		}

		$src = $this->getTwitchSrc($absoluteUrl);

		if (!$src) {
			$oembed->error = true;
			$oembed->errorMsg = JText::_('COM_EB_COMPOSER_BLOCK_EMBED_ERROR');

			return $oembed;
		}

		$src .= 'parent=' . $siteUrl . '&autoplay=false';

		$oembed->html = '<iframe src="' . $src . '" frameborder="0" allowfullscreen="1" width="100%" height="400"></iframe>';

		return $oembed;
	}

	public function getTwitchSrc($url)
	{
		preg_match('/^https:\/\/www\.twitch\.tv\/(.*)\/video\/(.*)$/', $url , $matches);

		if ($matches) {
			return 'https://player.twitch.tv/?video=v' . $matches[2] . '&';
		}

		preg_match('/^https:\/\/www\.twitch\.tv\/([^?\/]+)$/', $url , $matches);

		if ($matches) {
			return 'https://player.twitch.tv/?channel=' . $matches[1] . '&';
		}

		preg_match('/^https:\/\/www\.twitch\.tv\/(.*)\/clip\/(.*)$/', $url , $matches);

		if ($matches) {
			return 'https://clips.twitch.tv/embed?clip=' . $matches[2] . '&';
		}

		return false;
	}

	public function twitter(&$oembed, $absoluteUrl)
	{
		$url = 'https://api.twitter.com/1/statuses/oembed.json?url=' . $absoluteUrl;

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		return $oembed;
	}

	public function gist(&$oembed, $absoluteUrl)
	{
		$oembed->html = '<script src="' . $absoluteUrl . '.js"></script>';

		return $oembed;
	}

	public function mixCloud( $parser , &$oembed , $absoluteUrl )
	{
		$url 	= 'http://www.mixcloud.com/oembed/?url=' . urlencode($absoluteUrl) . '&format=json';

		// Load up the connector first.
		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function soundCloud(&$oembed, $absoluteUrl)
	{
		$url = 'https://soundcloud.com/oembed?format=json&url=' . urlencode( $absoluteUrl );

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function spotify(&$oembed, $absoluteUrl)
	{
		$url = 'https://open.spotify.com/oembed?url=' . urlencode($absoluteUrl);

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function youtube(&$oembed, $absoluteUrl)
	{
		$url = $absoluteUrl;

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// Unauthorized mean it doesn't allow 3rd party to embed this video.
		if ($contents == 'Unauthorized') {
			$oembed->error = true;
			$oembed->errorMsg = JText::_('COM_EB_COMPOSER_BLOCK_EMBED_ERROR');
			return $oembed;
		}

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		// add cookie-less html
		if (isset($oembed->html) && $oembed->html) {
			$oembed->html_nocookie = str_replace('youtube.com/', 'youtube-nocookie.com/', $oembed->html);
		}

		return $oembed;
	}


	public function easysocial(&$oembed, $absoluteUrl, $query)
	{
		if (!EB::easysocial()->exists()) {
			$oembed->error = true;
			$oembed->errorMsg = JText::_('COM_EB_COMPOSER_BLOCK_EMBED_ERROR');
			return $oembed;
		}

		$id = $query['id'];
		$video = ES::video(null, null, $id);

		if (!$video->id) {
			$oembed->error = true;
			$oembed->errorMsg = JText::_('COM_EB_POST_COVER_EMBEDDED_ES_VIDEO_INVALID');
			return $oembed;
		}

		$embedUrl = ESR::videos(array('layout' => 'item', 'id' => $id, 'format' => 'embed', 'external' => true));
		$oembed->html = '<iframe width="200" height="113" src="' . $embedUrl . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

		return $oembed;
	}

	public function vimeo(&$oembed, $absoluteUrl)
	{
		// the Vimeo Oembed URL already get updated before come this function.
		$url = $absoluteUrl;

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function dailymotion(&$oembed, $absoluteUrl)
	{
		$url = 'https://www.dailymotion.com/services/oembed/?url=' . urlencode($absoluteUrl);

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function slideshare(&$oembed, $absoluteUrl)
	{
		$url = 'http://www.slideshare.net/api/oembed/2?url=' . urlencode($absoluteUrl);

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	/**
	 * Retrieving the oembed data from TikTok
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function tiktok(&$oembed, $absoluteUrl)
	{
		$url = 'https://www.tiktok.com/oembed?url=' . $absoluteUrl;

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		$oembed = json_decode($contents);

		return $oembed;
	}
}