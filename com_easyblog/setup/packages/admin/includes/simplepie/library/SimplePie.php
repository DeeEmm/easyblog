<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

require_once(dirname(__FILE__) . '/SimplePie/Misc.php');
require_once(dirname(__FILE__) . '/SimplePie/Sanitize.php');
require_once(dirname(__FILE__) . '/SimplePie/Registry.php');
require_once(dirname(__FILE__) . '/SimplePie/XML/Declaration/Parser.php');
require_once(dirname(__FILE__) . '/SimplePie/Parser.php');
require_once(dirname(__FILE__) . '/SimplePie/Item.php');
require_once(dirname(__FILE__) . '/SimplePie/Parse/Date.php');
require_once(dirname(__FILE__) . '/SimplePie/IRI.php');
require_once(dirname(__FILE__) . '/SimplePie/Restriction.php');
require_once(dirname(__FILE__) . '/SimplePie/Enclosure.php');
require_once(dirname(__FILE__) . '/SimplePie/Author.php');

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2017, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @version 1.5
 * @copyright 2004-2017 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

if (!class_exists('SimplePie')) {

define('SIMPLEPIE_NAME', 'SimplePie');
define('SIMPLEPIE_VERSION', '1.5');
define('SIMPLEPIE_BUILD', gmdate('YmdHis', SimplePie_Misc::get_build()));
define('SIMPLEPIE_URL', 'http://simplepie.org');
define('SIMPLEPIE_USERAGENT', SIMPLEPIE_NAME . '/' . SIMPLEPIE_VERSION . ' (Feed Parser; ' . SIMPLEPIE_URL . '; Allow like Gecko) Build/' . SIMPLEPIE_BUILD);
define('SIMPLEPIE_LINKBACK', '<a href="' . SIMPLEPIE_URL . '" title="' . SIMPLEPIE_NAME . ' ' . SIMPLEPIE_VERSION . '">' . SIMPLEPIE_NAME . '</a>');
define('SIMPLEPIE_LOCATOR_NONE', 0);
define('SIMPLEPIE_LOCATOR_AUTODISCOVERY', 1);
define('SIMPLEPIE_LOCATOR_LOCAL_EXTENSION', 2);
define('SIMPLEPIE_LOCATOR_LOCAL_BODY', 4);
define('SIMPLEPIE_LOCATOR_REMOTE_EXTENSION', 8);
define('SIMPLEPIE_LOCATOR_REMOTE_BODY', 16);
define('SIMPLEPIE_LOCATOR_ALL', 31);
define('SIMPLEPIE_TYPE_NONE', 0);
define('SIMPLEPIE_TYPE_RSS_090', 1);
define('SIMPLEPIE_TYPE_RSS_091_NETSCAPE', 2);
define('SIMPLEPIE_TYPE_RSS_091_USERLAND', 4);
define('SIMPLEPIE_TYPE_RSS_091', 6);
define('SIMPLEPIE_TYPE_RSS_092', 8);
define('SIMPLEPIE_TYPE_RSS_093', 16);
define('SIMPLEPIE_TYPE_RSS_094', 32);
define('SIMPLEPIE_TYPE_RSS_10', 64);
define('SIMPLEPIE_TYPE_RSS_20', 128);
define('SIMPLEPIE_TYPE_RSS_RDF', 65);
define('SIMPLEPIE_TYPE_RSS_SYNDICATION', 190);
define('SIMPLEPIE_TYPE_RSS_ALL', 255);
define('SIMPLEPIE_TYPE_ATOM_03', 256);
define('SIMPLEPIE_TYPE_ATOM_10', 512);
define('SIMPLEPIE_TYPE_ATOM_ALL', 768);
define('SIMPLEPIE_TYPE_ALL', 1023);
define('SIMPLEPIE_CONSTRUCT_NONE', 0);
define('SIMPLEPIE_CONSTRUCT_TEXT', 1);
define('SIMPLEPIE_CONSTRUCT_HTML', 2);
define('SIMPLEPIE_CONSTRUCT_XHTML', 4);
define('SIMPLEPIE_CONSTRUCT_BASE64', 8);
define('SIMPLEPIE_CONSTRUCT_IRI', 16);
define('SIMPLEPIE_CONSTRUCT_MAYBE_HTML', 32);
define('SIMPLEPIE_CONSTRUCT_ALL', 63);
define('SIMPLEPIE_SAME_CASE', 1);
define('SIMPLEPIE_LOWERCASE', 2);
define('SIMPLEPIE_UPPERCASE', 4);
define('SIMPLEPIE_PCRE_HTML_ATTRIBUTE', '((?:[\x09\x0A\x0B\x0C\x0D\x20]+[^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3D\x3E]*(?:[\x09\x0A\x0B\x0C\x0D\x20]*=[\x09\x0A\x0B\x0C\x0D\x20]*(?:"(?:[^"]*)"|\'(?:[^\']*)\'|(?:[^\x09\x0A\x0B\x0C\x0D\x20\x22\x27\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x3E]*)?))?)*)[\x09\x0A\x0B\x0C\x0D\x20]*');
define('SIMPLEPIE_PCRE_XML_ATTRIBUTE', '((?:\s+(?:(?:[^\s:]+:)?[^\s:]+)\s*=\s*(?:"(?:[^"]*)"|\'(?:[^\']*)\'))*)\s*');
define('SIMPLEPIE_NAMESPACE_XML', 'http://www.w3.org/XML/1998/namespace');
define('SIMPLEPIE_NAMESPACE_ATOM_10', 'http://www.w3.org/2005/Atom');
define('SIMPLEPIE_NAMESPACE_ATOM_03', 'http://purl.org/atom/ns#');
define('SIMPLEPIE_NAMESPACE_RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('SIMPLEPIE_NAMESPACE_RSS_090', 'http://my.netscape.com/rdf/simple/0.9/');
define('SIMPLEPIE_NAMESPACE_RSS_10', 'http://purl.org/rss/1.0/');
define('SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT', 'http://purl.org/rss/1.0/modules/content/');
define('SIMPLEPIE_NAMESPACE_RSS_20', '');
define('SIMPLEPIE_NAMESPACE_DC_10', 'http://purl.org/dc/elements/1.0/');
define('SIMPLEPIE_NAMESPACE_DC_11', 'http://purl.org/dc/elements/1.1/');
define('SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
define('SIMPLEPIE_NAMESPACE_GEORSS', 'http://www.georss.org/georss');
define('SIMPLEPIE_NAMESPACE_MEDIARSS', 'http://search.yahoo.com/mrss/');
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG', 'http://search.yahoo.com/mrss');
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG2', 'http://video.search.yahoo.com/mrss');
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG3', 'http://video.search.yahoo.com/mrss/');
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG4', 'http://www.rssboard.org/media-rss');
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG5', 'http://www.rssboard.org/media-rss/');
define('SIMPLEPIE_NAMESPACE_ITUNES', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
define('SIMPLEPIE_NAMESPACE_XHTML', 'http://www.w3.org/1999/xhtml');
define('SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY', 'http://www.iana.org/assignments/relation/');
define('SIMPLEPIE_FILE_SOURCE_NONE', 0);
define('SIMPLEPIE_FILE_SOURCE_REMOTE', 1);
define('SIMPLEPIE_FILE_SOURCE_LOCAL', 2);
define('SIMPLEPIE_FILE_SOURCE_FSOCKOPEN', 4);
define('SIMPLEPIE_FILE_SOURCE_CURL', 8);
define('SIMPLEPIE_FILE_SOURCE_FILE_GET_CONTENTS', 16);

class SimplePie
{
	public $data = array();
	public $error;
	public $sanitize;
	public $useragent = SIMPLEPIE_USERAGENT;
	public $feed_url;
	public $permanent_url = null;
	public $file;
	public $raw_data;
	public $timeout = 10;
	public $curl_options = array();
	public $force_fsockopen = false;
	public $force_feed = false;
	public $cache = true;
	public $force_cache_fallback = false;
	public $cache_duration = 3600;
	public $autodiscovery_cache_duration = 604800; // 7 Days.
	public $cache_location = './cache';
	public $cache_name_function = 'md5';
	public $order_by_date = true;
	public $input_encoding = false;
	public $autodiscovery = SIMPLEPIE_LOCATOR_ALL;
	public $registry;
	public $max_checked_feeds = 10;
	public $all_discovered_feeds = array();
	public $image_handler = '';
	public $multifeed_url = array();
	public $multifeed_objects = array();
	public $config_settings = null;
	public $item_limit = 0;
	public $check_modified = false;
	public $strip_attributes = array('bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc');
	public $add_attributes = array('audio' => array('preload' => 'none'), 'iframe' => array('sandbox' => 'allow-scripts allow-same-origin'), 'video' => array('preload' => 'none'));
	public $strip_htmltags = array('base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style');
	public $enable_exceptions = false;

	public function __construct()
	{
		if (version_compare(PHP_VERSION, '5.3', '<')) {
			trigger_error('Please upgrade to PHP 5.3 or newer.');
			die();
		}

		// Other objects, instances created here so we can set options on them
		$this->sanitize = new SimplePie_Sanitize();
		$this->registry = new SimplePie_Registry();

		if (func_num_args() > 0){
			$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
			trigger_error('Passing parameters to the constructor is no longer supported. Please use set_feed_url(), set_cache_location(), and set_cache_duration() directly.', $level);

			$args = func_get_args();
			switch (count($args)) {
				case 3:
					$this->set_cache_duration($args[2]);
				case 2:
					$this->set_cache_location($args[1]);
				case 1:
					$this->set_feed_url($args[0]);
					$this->init();
			}
		}
	}

	public function __toString()
	{
		return md5(serialize($this->data));
	}

	public function __destruct()
	{
		if ((version_compare(PHP_VERSION, '5.3', '<') || !gc_enabled()) && !ini_get('zend.ze1_compatibility_mode')) {

			if (!empty($this->data['items'])) {

				foreach ($this->data['items'] as $item) {
					$item->__destruct();
				}
				unset($item, $this->data['items']);
			}

			if (!empty($this->data['ordered_items'])) {

				foreach ($this->data['ordered_items'] as $item) {
					$item->__destruct();
				}
				unset($item, $this->data['ordered_items']);
			}
		}
	}

	/**
	 * Set the raw XML data to parse
	 *
	 * Allows you to use a string of RSS/Atom data instead of a remote feed.
	 *
	 * If you have a feed available as a string in PHP, you can tell SimplePie
	 * to parse that data string instead of a remote feed. Any set feed URL
	 * takes precedence.
	 *
	 * @since 1.0
	 * @param string $data RSS or Atom data as a string.
	 */
	public function set_raw_data($data)
	{
		$this->raw_data = $data;
	}

	/**
	 * Set the URL of the feed you want to parse
	 *
	 * This allows you to enter the URL of the feed you want to parse, or the
	 * website you want to try to use auto-discovery on. This takes priority
	 * over any set raw data.
	 *
	 * You can set multiple feeds to mash together by passing an array instead
	 * of a string for the $url. Remember that with each additional feed comes
	 * additional processing and resources.
	 *
	 * @since 1.0
	 * @param string|array $url This is the URL (or array of URLs) that you want to parse.
	 */
	public function set_feed_url($url)
	{
		$this->multifeed_url = array();

		if (is_array($url)) {
			foreach ($url as $value) {
				$this->multifeed_url[] = $this->registry->call('Misc', 'fix_protocol', array($value, 1));
			}
		} else {
			$this->feed_url = $this->registry->call('Misc', 'fix_protocol', array($url, 1));
			$this->permanent_url = $this->feed_url;
		}
	}

	/**
	 * Set the length of time (in seconds) that the contents of a feed will be
	 * cached
	 *
	 * @param int $seconds The feed content cache duration
	 */
	public function set_cache_duration($seconds = 3600)
	{
		$this->cache_duration = (int) $seconds;
	}

	/**
	 * Set the file system location where the cached files should be stored
	 *
	 * @param string $location The file system location.
	 */
	public function set_cache_location($location = './cache')
	{
		$this->cache_location = (string) $location;
	}

	public function remove_div($enable = true)
	{
		$this->sanitize->remove_div($enable);
	}

	public function strip_htmltags($tags = '', $encode = null)
	{
		$this->sanitize->strip_htmltags($tags);
	}

	public function strip_attributes($attribs = '')
	{
		if ($attribs === '') {
			$attribs = $this->strip_attributes;
		}
		$this->sanitize->strip_attributes($attribs);
	}

	public function add_attributes($attribs = '')
	{
		if ($attribs === '') {
			$attribs = $this->add_attributes;
		}
		$this->sanitize->add_attributes($attribs);
	}

	/**
	 * Set the output encoding
	 * @param string $encoding
	 */
	public function set_output_encoding($encoding = 'UTF-8')
	{
		$this->sanitize->set_output_encoding($encoding);
	}

	public function strip_comments($strip = false)
	{
		$this->sanitize->strip_comments($strip);
	}

	/**
	 * Set element/attribute key/value pairs of HTML attributes
	 * containing URLs that need to be resolved relative to the feed
	 *
	 * Defaults to |a|@href, |area|@href, |blockquote|@cite, |del|@cite,
	 * |form|@action, |img|@longdesc, |img|@src, |input|@src, |ins|@cite,
	 * |q|@cite
	 *
	 * @since 1.0
	 * @param array|null $element_attribute Element/attribute key/value pairs, null for default
	 */
	public function set_url_replacements($element_attribute = null)
	{
		$this->sanitize->set_url_replacements($element_attribute);
	}

	/**
	 * Set the handler to enable the display of cached images.
	 *
	 * @param str $page Web-accessible path to the handler_image.php file.
	 * @param str $qs The query string that the value should be passed to.
	 */
	public function set_image_handler($page = false, $qs = 'i')
	{
		if ($page !== false) {
			$this->sanitize->set_image_handler($page . '?' . $qs . '=');
		} else {
			$this->image_handler = '';
		}
	}

	/**
	 * Enable throwing exceptions
	 *
	 * @param boolean $enable Should we throw exceptions, or use the old-style error property?
	 */
	public function enable_exceptions($enable = true)
	{
		$this->enable_exceptions = $enable;
	}

	/**
	 * Initialize the feed object
	 *
	 * This is what makes everything happen. Period. This is where all of the
	 * configuration options get processed, feeds are fetched, cached, and
	 * parsed, and all of that other good stuff.
	 *
	 * @return boolean True if successful, false otherwise
	 */
	public function init()
	{
		// Check absolute bare minimum requirements.
		if (!extension_loaded('xml') || !extension_loaded('pcre')) {
			$this->error = 'XML or PCRE extensions not loaded!';
			return false;

		} elseif (!extension_loaded('xmlreader')) {
			// Then check the xml extension is sane (i.e., libxml 2.7.x issue on PHP < 5.2.9 and libxml 2.7.0 to 2.7.2 on any version) if we don't have xmlreader.
			static $xml_is_sane = null;

			if ($xml_is_sane === null) {
				$parser_check = xml_parser_create();
				xml_parse_into_struct($parser_check, '<foo>&amp;</foo>', $values);
				xml_parser_free($parser_check);
				$xml_is_sane = isset($values[0]['value']);
			}

			if (!$xml_is_sane) {
				return false;
			}
		}

		if (method_exists($this->sanitize, 'set_registry')) {
			$this->sanitize->set_registry($this->registry);
		}

		// Pass whatever was set with config options over to the sanitizer.
		// Pass the classes in for legacy support; new classes should use the registry instead
		$this->sanitize->pass_cache_data($this->cache, $this->cache_location, $this->cache_name_function, $this->registry->get_class('Cache'));
		$this->sanitize->pass_file_data($this->registry->get_class('File'), $this->timeout, $this->useragent, $this->force_fsockopen, $this->curl_options);

		if (!empty($this->multifeed_url)) {
			$i = 0;
			$success = 0;

			$this->multifeed_objects = array();
			$this->error = array();

			foreach ($this->multifeed_url as $url) {
				$this->multifeed_objects[$i] = clone $this;
				$this->multifeed_objects[$i]->set_feed_url($url);

				$single_success = $this->multifeed_objects[$i]->init();
				$success |= $single_success;

				if (!$single_success) {
					$this->error[$i] = $this->multifeed_objects[$i]->error();
				}
				$i++;
			}

			return (bool) $success;

		} elseif ($this->feed_url === null && $this->raw_data === null) {
			return false;
		}

		$this->error = null;
		$this->data = array();
		$this->check_modified = false;
		$this->multifeed_objects = array();
		$cache = false;

		// Empty response check
		if (empty($this->raw_data)){
			$this->error = "A feed could not be found at `$this->feed_url`. Empty body.";
			$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));
			return false;
		}

		// Set up array of possible encodings
		$encodings = array();

		// First check to see if input has been overridden.
		if ($this->input_encoding !== false) {
			$encodings[] = strtoupper($this->input_encoding);
		}

		$application_types = array('application/xml', 'application/xml-dtd', 'application/xml-external-parsed-entity');
		$text_types = array('text/xml', 'text/xml-external-parsed-entity');

		// Fallback to XML 1.0 Appendix F.1/UTF-8/ISO-8859-1
		$encodings = array_merge($encodings, $this->registry->call('Misc', 'xml_encoding', array($this->raw_data, &$this->registry)));
		$encodings[] = 'UTF-8';
		$encodings[] = 'ISO-8859-1';

		// There's no point in trying an encoding twice
		$encodings = array_unique($encodings);

		// Loop through each possible encoding, till we return something, or run out of possibilities
		foreach ($encodings as $encoding) {

			// Change the encoding to UTF-8 (as we always use UTF-8 internally)
			if ($utf8_data = $this->registry->call('Misc', 'change_encoding', array($this->raw_data, $encoding, 'UTF-8'))) {
				// Create new parser
				$parser = $this->registry->create('Parser');

				// If it's parsed fine
				if ($parser->parse($utf8_data, 'UTF-8', $this->permanent_url)) {

					$this->data = $parser->get_data();

					if (!($this->get_type() & ~SIMPLEPIE_TYPE_NONE)) {
						$this->error = "A feed could not be found at `$this->feed_url`. This does not appear to be a valid RSS or Atom feed.";
						$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));
						return false;
					}

					if (isset($headers)) {
						$this->data['headers'] = $headers;
					}

					$this->data['build'] = SIMPLEPIE_BUILD;

					// Cache the file if caching is enabled
					if ($cache && !$cache->save($this)) {
						trigger_error("$this->cache_location is not writeable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
					}

					return true;
				}
			}
		}

		if (isset($parser)) {
			// We have an error, just set SimplePie_Misc::error to it and quit
			$this->error = $this->feed_url;
			$this->error .= sprintf(' is invalid XML, likely due to invalid characters. XML error: %s at line %d, column %d', $parser->get_error_string(), $parser->get_current_line(), $parser->get_current_column());
		} else {
			$this->error = 'The data could not be converted to UTF-8.';

			if (!extension_loaded('mbstring') && !extension_loaded('iconv') && !class_exists('\UConverter')) {
				$this->error .= ' You MUST have either the iconv, mbstring or intl (PHP 5.5+) extension installed and enabled.';
			} else {
				$missingExtensions = array();
				if (!extension_loaded('iconv')) {
					$missingExtensions[] = 'iconv';
				}
				if (!extension_loaded('mbstring')) {
					$missingExtensions[] = 'mbstring';
				}
				if (!class_exists('\UConverter')) {
					$missingExtensions[] = 'intl (PHP 5.5+)';
				}
				$this->error .= ' Try installing/enabling the ' . implode(' or ', $missingExtensions) . ' extension.';
			}
		}

		$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));

		return false;
	}

	/**
	 * Get the error message for the occured error
	 *
	 * @return string|array Error message, or array of messages for multifeeds
	 */
	public function error()
	{
		return $this->error;
	}

	/**
	 * Get the raw XML
	 *
	 * This is the same as the old `$feed->enable_xml_dump(true)`, but returns
	 * the data instead of printing it.
	 *
	 * @return string|boolean Raw XML data, false if the cache is used
	 */
	public function get_raw_data()
	{
		return $this->raw_data;
	}

	/**
	 * Get the type of the feed
	 *
	 * This returns a SIMPLEPIE_TYPE_* constant, which can be tested against
	 * using {@link http://php.net/language.operators.bitwise bitwise operators}
	 *
	 * @since 0.8 (usage changed to using constants in 1.0)
	 * @see SIMPLEPIE_TYPE_NONE Unknown.
	 * @see SIMPLEPIE_TYPE_RSS_090 RSS 0.90.
	 * @see SIMPLEPIE_TYPE_RSS_091_NETSCAPE RSS 0.91 (Netscape).
	 * @see SIMPLEPIE_TYPE_RSS_091_USERLAND RSS 0.91 (Userland).
	 * @see SIMPLEPIE_TYPE_RSS_091 RSS 0.91.
	 * @see SIMPLEPIE_TYPE_RSS_092 RSS 0.92.
	 * @see SIMPLEPIE_TYPE_RSS_093 RSS 0.93.
	 * @see SIMPLEPIE_TYPE_RSS_094 RSS 0.94.
	 * @see SIMPLEPIE_TYPE_RSS_10 RSS 1.0.
	 * @see SIMPLEPIE_TYPE_RSS_20 RSS 2.0.x.
	 * @see SIMPLEPIE_TYPE_RSS_RDF RDF-based RSS.
	 * @see SIMPLEPIE_TYPE_RSS_SYNDICATION Non-RDF-based RSS (truly intended as syndication format).
	 * @see SIMPLEPIE_TYPE_RSS_ALL Any version of RSS.
	 * @see SIMPLEPIE_TYPE_ATOM_03 Atom 0.3.
	 * @see SIMPLEPIE_TYPE_ATOM_10 Atom 1.0.
	 * @see SIMPLEPIE_TYPE_ATOM_ALL Any version of Atom.
	 * @see SIMPLEPIE_TYPE_ALL Any known/supported feed type.
	 * @return int SIMPLEPIE_TYPE_* constant
	 */
	public function get_type()
	{
		if (!isset($this->data['type']))
		{
			$this->data['type'] = SIMPLEPIE_TYPE_ALL;
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_ATOM_10;
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_ATOM_03;
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF']))
			{
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['channel'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['image'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['item'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['textinput']))
				{
					$this->data['type'] &= SIMPLEPIE_TYPE_RSS_10;
				}
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['channel'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['image'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['item'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['textinput']))
				{
					$this->data['type'] &= SIMPLEPIE_TYPE_RSS_090;
				}
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_RSS_ALL;
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version']))
				{
					switch (trim($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version']))
					{
						case '0.91':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091;
							if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_20]['skiphours']['hour'][0]['data']))
							{
								switch (trim($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_20]['skiphours']['hour'][0]['data']))
								{
									case '0':
										$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091_NETSCAPE;
										break;

									case '24':
										$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091_USERLAND;
										break;
								}
							}
							break;

						case '0.92':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_092;
							break;

						case '0.93':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_093;
							break;

						case '0.94':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_094;
							break;

						case '2.0':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_20;
							break;
					}
				}
			}
			else
			{
				$this->data['type'] = SIMPLEPIE_TYPE_NONE;
			}
		}
		return $this->data['type'];
	}

	/**
	 * Get the URL for the feed
	 *
	 * When the 'permanent' mode is enabled, returns the original feed URL,
	 * except in the case of an `HTTP 301 Moved Permanently` status response,
	 * in which case the location of the first redirection is returned.
	 *
	 * When the 'permanent' mode is disabled (default),
	 * may or may not be different from the URL passed to {@see set_feed_url()},
	 * depending on whether auto-discovery was used.
	 *
	 * @since Preview Release (previously called `get_feed_url()` since SimplePie 0.8.)
	 * @todo Support <itunes:new-feed-url>
	 * @todo Also, |atom:link|@rel=self
	 * @param bool $permanent Permanent mode to return only the original URL or the first redirection
	 * iff it is a 301 redirection
	 * @return string|null
	 */
	public function subscribe_url($permanent = false)
	{
		if ($permanent) {
			if ($this->permanent_url !== null) {
				// sanitize encodes ampersands which are required when used in a url.
				return str_replace('&amp;', '&',
				                   $this->sanitize($this->permanent_url,
				                                   SIMPLEPIE_CONSTRUCT_IRI));
			}
		} else {
			if ($this->feed_url !== null) {
				return str_replace('&amp;', '&',
				                   $this->sanitize($this->feed_url,
				                                   SIMPLEPIE_CONSTRUCT_IRI));
			}
		}
		return null;
	}

	/**
	 * Get data for an feed-level element
	 *
	 * This method allows you to get access to ANY element/attribute that is a
	 * sub-element of the opening feed tag.
	 *
	 * The return value is an indexed array of elements matching the given
	 * namespace and tag name. Each element has `attribs`, `data` and `child`
	 * subkeys. For `attribs` and `child`, these contain namespace subkeys.
	 * `attribs` then has one level of associative name => value data (where
	 * `value` is a string) after the namespace. `child` has tag-indexed keys
	 * after the namespace, each member of which is an indexed array matching
	 * this same format.
	 *
	 * For example:
	 * <pre>
	 * // This is probably a bad example because we already support
	 * // <media:content> natively, but it shows you how to parse through
	 * // the nodes.
	 * $group = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'group');
	 * $content = $group[0]['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content'];
	 * $file = $content[0]['attribs']['']['url'];
	 * echo $file;
	 * </pre>
	 *
	 * @since 1.0
	 * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
	 * @param string $namespace The URL of the XML namespace of the elements you're trying to access
	 * @param string $tag Tag name
	 * @return array
	 */
	public function get_feed_tags($namespace, $tag)
	{
		$type = $this->get_type();

		if ($type & SIMPLEPIE_TYPE_ATOM_10) {
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag])) {
				return $this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag];
			}
		}

		if ($type & SIMPLEPIE_TYPE_ATOM_03) {
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag])) {
				return $this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag];
			}
		}

		if ($type & SIMPLEPIE_TYPE_RSS_RDF) {
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag])) {
				return $this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag];
			}
		}

		if ($type & SIMPLEPIE_TYPE_RSS_SYNDICATION) {
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag])) {
				return $this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag];
			}
		}
		return null;
	}

	/**
	 * Get data for an channel-level element
	 *
	 * This method allows you to get access to ANY element/attribute in the
	 * channel/header section of the feed.
	 *
	 * See {@see SimplePie::get_feed_tags()} for a description of the return value
	 *
	 * @since 1.0
	 * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
	 * @param string $namespace The URL of the XML namespace of the elements you're trying to access
	 * @param string $tag Tag name
	 * @return array
	 */
	public function get_channel_tags($namespace, $tag)
	{
		$type = $this->get_type();
		if ($type & SIMPLEPIE_TYPE_ATOM_ALL)
		{
			if ($return = $this->get_feed_tags($namespace, $tag))
			{
				return $return;
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_10)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
					return $channel[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_090)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
					return $channel[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_SYNDICATION)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
					return $channel[0]['child'][$namespace][$tag];
				}
			}
		}
		return null;
	}

	/**
	 * Get the base URL value from the feed
	 *
	 * Uses `<xml:base>` if available, otherwise uses the first link in the
	 * feed, or failing that, the URL of the feed itself.
	 *
	 * @see get_link
	 * @see subscribe_url
	 *
	 * @param array $element
	 * @return string
	 */
	public function get_base($element = array())
	{
		if (!($this->get_type() & SIMPLEPIE_TYPE_RSS_SYNDICATION) && !empty($element['xml_base_explicit']) && isset($element['xml_base']))
		{
			return $element['xml_base'];
		}
		elseif ($this->get_link() !== null)
		{
			return $this->get_link();
		}
		else
		{
			return $this->subscribe_url();
		}
	}

	/**
	 * Sanitize feed data
	 *
	 * @access private
	 * @see SimplePie_Sanitize::sanitize()
	 * @param string $data Data to sanitize
	 * @param int $type One of the SIMPLEPIE_CONSTRUCT_* constants
	 * @param string $base Base URL to resolve URLs against
	 * @return string Sanitized data
	 */
	public function sanitize($data, $type, $base = '')
	{
		try {
			return $this->sanitize->sanitize($data, $type, $base);

		} catch (SimplePie_Exception $e) {

			if (!$this->enable_exceptions) {
				$this->error = $e->getMessage();
				$this->registry->call('Misc', 'error', array($this->error, E_USER_WARNING, $e->getFile(), $e->getLine()));
				return '';
			}

			throw $e;
		}
	}

	/**
	 * Get an author for the feed
	 *
	 * @since 1.1
	 * @param int $key The author that you want to return. Remember that arrays begin with 0, not 1
	 * @return SimplePie_Author|null
	 */
	public function get_author($key = 0)
	{
		$authors = $this->get_authors();
		if (isset($authors[$key])) {
			return $authors[$key];
		} else {
			return null;
		}
	}

	/**
	 * Get all authors for the feed
	 *
	 * Uses `<atom:author>`, `<author>`, `<dc:creator>` or `<itunes:author>`
	 *
	 * @since 1.1
	 * @return array|null List of {@see SimplePie_Author} objects
	 */
	public function get_authors()
	{
		$authors = array();
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'author') as $author) {

			$name = null;
			$uri = null;
			$email = null;

			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'])) {
				$name = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}

			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'])) {
				$uri = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]));
			}

			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'])) {
				$email = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}

			if ($name !== null || $email !== null || $uri !== null) {
				$authors[] = $this->registry->create('Author', array($name, $uri, $email));
			}
		}

		if ($author = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'author')) {

			$name = null;
			$url = null;
			$email = null;

			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'])) {
				$name = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}

			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'])) {
				$url = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]));
			}

			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'])) {
				$email = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}

			if ($name !== null || $email !== null || $url !== null) {
				$authors[] = $this->registry->create('Author', array($name, $url, $email));
			}
		}

		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator') as $author) {
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'creator') as $author) {
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'author') as $author) {
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		if (!empty($authors)) {
			return array_unique($authors);
		} else {
			return null;
		}
	}

	/**
	 * Get a single link for the feed
	 *
	 * @since 1.0 (previously called `get_feed_link` since Preview Release, `get_feed_permalink()` since 0.8)
	 * @param int $key The link that you want to return. Remember that arrays begin with 0, not 1
	 * @param string $rel The relationship of the link to return
	 * @return string|null Link URL
	 */
	public function get_link($key = 0, $rel = 'alternate')
	{
		$links = $this->get_links($rel);
		if (isset($links[$key])) {
			return $links[$key];
		} else {
			return null;
		}
	}

	/**
	 * Get the permalink for the item
	 *
	 * Returns the first link available with a relationship of "alternate".
	 * Identical to {@see get_link()} with key 0
	 *
	 * @see get_link
	 * @since 1.0 (previously called `get_feed_link` since Preview Release, `get_feed_permalink()` since 0.8)
	 * @internal Added for parity between the parent-level and the item/entry-level.
	 * @return string|null Link URL
	 */
	public function get_permalink()
	{
		return $this->get_link(0);
	}

	/**
	 * Get all links for the feed
	 *
	 * Uses `<atom:link>` or `<link>`
	 *
	 * @since Beta 2
	 * @param string $rel The relationship of links to return
	 * @return array|null Links found for the feed (strings)
	 */
	public function get_links($rel = 'alternate')
	{
		if (!isset($this->data['links'])) {
			$this->data['links'] = array();

			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'link')) {

				foreach ($links as $link) {

					if (isset($link['attribs']['']['href'])) {
						$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
						$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
					}
				}
			}

			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'link')) {

				foreach ($links as $link) {

					if (isset($link['attribs']['']['href'])) {
						$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
						$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
					}
				}
			}

			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'link')) {
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}

			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'link')) {
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}

			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'link')) {
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}

			$keys = array_keys($this->data['links']);

			foreach ($keys as $key) {

				if ($this->registry->call('Misc', 'is_isegment_nz_nc', array($key))) {

					if (isset($this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key])) {
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] = array_merge($this->data['links'][$key], $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key]);
						$this->data['links'][$key] =& $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key];
					} else {
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] =& $this->data['links'][$key];
					}
				} elseif (substr($key, 0, 41) === SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY) {
					$this->data['links'][substr($key, 41)] =& $this->data['links'][$key];
				}

				$this->data['links'][$key] = array_unique($this->data['links'][$key]);
			}
		}

		if (isset($this->data['links'][$rel])) {
			return $this->data['links'][$rel];
		} else if (isset($this->data['headers']['link']) && preg_match('/<([^>]+)>; rel='.preg_quote($rel).'/', $this->data['headers']['link'], $match)) {
			return array($match[1]);
		} else {
			return null;
		}
	}

	/**
	 * Get the content for the item
	 *
	 * Uses `<atom:subtitle>`, `<atom:tagline>`, `<description>`,
	 * `<dc:description>`, `<itunes:summary>` or `<itunes:subtitle>`
	 *
	 * @since 1.0 (previously called `get_feed_description()` since 0.8)
	 * @return string|null
	 */
	public function get_description()
	{
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'subtitle')) {
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'tagline')) {
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'description')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'description')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'description')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'description')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'description')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'summary')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));

		} elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'subtitle')) {
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));

		} else {
			return null;
		}
	}

	/**
	 * Get a single item from the feed
	 *
	 * This is better suited for {@link http://php.net/for for()} loops, whereas
	 * {@see get_items()} is better suited for
	 * {@link http://php.net/foreach foreach()} loops.
	 *
	 * @see get_item_quantity()
	 * @since Beta 2
	 * @param int $key The item that you want to return. Remember that arrays begin with 0, not 1
	 * @return SimplePie_Item|null
	 */
	public function get_item($key = 0)
	{
		$items = $this->get_items();
		if (isset($items[$key])) {
			return $items[$key];
		} else {
			return null;
		}
	}

	/**
	 * Get all items from the feed
	 *
	 * This is better suited for {@link http://php.net/for for()} loops, whereas
	 * {@see get_items()} is better suited for
	 * {@link http://php.net/foreach foreach()} loops.
	 *
	 * @see get_item_quantity
	 * @since Beta 2
	 * @param int $start Index to start at
	 * @param int $end Number of items to return. 0 for all items after `$start`
	 * @return SimplePie_Item[]|null List of {@see SimplePie_Item} objects
	 */
	public function get_items($start = 0, $end = 0)
	{
		if (!isset($this->data['items'])) {

			if (!empty($this->multifeed_objects)) {
				$this->data['items'] = SimplePie::merge_items($this->multifeed_objects, $start, $end, $this->item_limit);

				if (empty($this->data['items'])) {
					return array();
				}
				return $this->data['items'];
			}

			$this->data['items'] = array();

			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'entry')) {
				$keys = array_keys($items);

				foreach ($keys as $key) {
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}

			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'entry')) {
				$keys = array_keys($items);

				foreach ($keys as $key) {
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}

			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'item')) {
				$keys = array_keys($items);

				foreach ($keys as $key) {
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}

			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'item')) {
				$keys = array_keys($items);

				foreach ($keys as $key) {
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}

			if ($items = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'item')) {
				$keys = array_keys($items);

				foreach ($keys as $key) {
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
		}

		if (empty($this->data['items'])) {
			return array();
		}

		if ($this->order_by_date) {

			if (!isset($this->data['ordered_items'])) {
				$this->data['ordered_items'] = $this->data['items'];
				usort($this->data['ordered_items'], array(get_class($this), 'sort_items'));
		 	}
			$items = $this->data['ordered_items'];
		} else {
			$items = $this->data['items'];
		}

		// Slice the data as desired
		if ($end === 0) {
			return array_slice($items, $start);
		} else {
			return array_slice($items, $start, $end);
		}
	}

	/**
	 * Sorting callback for items
	 *
	 * @access private
	 * @param SimplePie $a
	 * @param SimplePie $b
	 * @return boolean
	 */
	public static function sort_items($a, $b)
	{
		$a_date = $a->get_date('U');
		$b_date = $b->get_date('U');
		if ($a_date && $b_date) {
			return $a_date > $b_date ? -1 : 1;
		}
		// Sort items without dates to the top.
		if ($a_date) {
			return 1;
		}
		if ($b_date) {
			return -1;
		}
		return 0;
	}

	/**
	 * Merge items from several feeds into one
	 *
	 * If you're merging multiple feeds together, they need to all have dates
	 * for the items or else SimplePie will refuse to sort them.
	 *
	 * @link http://simplepie.org/wiki/tutorial/sort_multiple_feeds_by_time_and_date#if_feeds_require_separate_per-feed_settings
	 * @param array $urls List of SimplePie feed objects to merge
	 * @param int $start Starting item
	 * @param int $end Number of items to return
	 * @param int $limit Maximum number of items per feed
	 * @return array
	 */
	public static function merge_items($urls, $start = 0, $end = 0, $limit = 0)
	{
		if (is_array($urls) && sizeof($urls) > 0) {
			$items = array();

			foreach ($urls as $arg) {
				if ($arg instanceof SimplePie) {
					$items = array_merge($items, $arg->get_items(0, $limit));
				} else {
					trigger_error('Arguments must be SimplePie objects', E_USER_WARNING);
				}
			}

			usort($items, array(get_class($urls[0]), 'sort_items'));

			if ($end === 0) {
				return array_slice($items, $start);
			} else {
				return array_slice($items, $start, $end);
			}
		} else {
			trigger_error('Cannot merge zero SimplePie objects', E_USER_WARNING);
			return array();
		}
	}

	/**
	 * Store PubSubHubbub links as headers
	 *
	 * There is no way to find PuSH links in the body of a microformats feed,
	 * so they are added to the headers when found, to be used later by get_links.
	 * @param SimplePie_File $file
	 * @param string $hub
	 * @param string $self
	 */
	private function store_links(&$file, $hub, $self) {
		if (isset($file->headers['link']['hub']) ||
			  (isset($file->headers['link']) &&
			   preg_match('/rel=hub/', $file->headers['link']))) {
			return;
		}

		if ($hub) {

			if (isset($file->headers['link'])) {

				if ($file->headers['link'] !== '') {
					$file->headers['link'] = ', ';
				}
			} else {
				$file->headers['link'] = '';
			}

			$file->headers['link'] .= '<'.$hub.'>; rel=hub';

			if ($self) {
				$file->headers['link'] .= ', <'.$self.'>; rel=self';
			}
		}
	}
}
}
