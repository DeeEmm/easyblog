<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogString
{
	public $config = null;
	
	public function __construct()
	{
		$this->config = EB::config();
	}

	/**
	 * convert absolute url to relative url
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function abs2rel($url, $includeBase = false)
	{
		// remove domain name

		$oriUrl = $url;

		$root = rtrim(JURI::root(), '/');
		$root = preg_replace("(^https?://)", "//", $root);
		// $root = preg_replace("(^//www\.)", "//", $root);

		$url = preg_replace("(^https?://)", "//", $url);
		// $url = preg_replace("(^//www\.)", "//", $url);

		if (strpos($url, $root) !== false) {

			$url = str_replace($root, '', $url);

			$pos = strpos($url, '//');
			// make sure we dont trim the needed slash. e.g //external.domain.com/images/123.jpg
			if ($pos !== 0) {
				// make sure no leading slash
				$url = ltrim($url, '/');
			}

			if ($includeBase) {
				$base = JUri::root(true) . '/';
				$url = $base. $url;
			}

		} else {
			// do nothing
			$url = $oriUrl;
		}

		return $url;
	}


	/**
	 * Trim any linefeed and non-breaking space.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function trimEmptySpace($text)
	{
		$text = preg_replace('/[\n\r]/', '', $text);
		// $text = preg_replace('~\x{00a0}~siu', '', $text);
		$text = EBString::str_ireplace('\xc2\xa0', '', $text);
		$text = trim($text);

		return $text;
	}

	/**
	 * Normalizes an URL and ensure that it contains the protocol
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeUrl($url)
	{
		$url = trim($url);
		$regex = '/^(http|https|ftp):\/\/*?/i';

		$matched = preg_match($regex, $url, $matches);

		if ($matches) {
			return $url;
		}

		return 'http://' . $url;
	}

	/**
	 * Strip off known extension tags
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function stripKnownTags($text)
	{
		// Remove JFBConnect codes.
		$pattern = '/\{JFBCLike(.*)\}/i';
		$text = preg_replace($pattern, '', $text);

		return $text;
	}

	/**
	 * Retrieves the language code
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLangCode()
	{
		$lang 		= JFactory::getLanguage();
		$locale 	= $lang->getLocale();
		$langCode    = null;

		if(empty($locale))
		{
			$langCode    = 'en-GB';
		}
		else
		{
			$langTag    = $locale[0];
			$langData    = explode('.', $langTag);
			$langCode   = EBString::str_ireplace('_', '-', $langData[0]);
		}
		return $langCode;
	}

	public function getNoun($var, $count, $includeCount = false)
	{
		$zeroAsPlural = $this->config->get('layout_zero_as_plural');

		$count = (int) $count;

		$var = ($count===1 || $count===-1 || ($count===0 && !$zeroAsPlural)) ? $var . '_SINGULAR' : $var . '_PLURAL';

		if ($includeCount) {
			return JText::sprintf($var, $count);
		}

		return JText::_($var);
	}

	/**
	 * Removes the first <img> tag in a given content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeFirstImage($contents)
	{
		//try to search for the 1st img in the blog
		$pattern = '#<img[^>]*>#i';
		preg_match($pattern, $contents, $matches);

		if (!$matches) {
			return $contents;
		}

		$image = $matches[0];
		$contents = EBString::str_ireplace($image, '', $contents);

		return $contents;
	}

	/**
	 * Captures the first <img> tag in a given content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getImage($contents)
	{
		$image = false;
		$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';

		preg_match($pattern, $contents, $matches);

		if (!$matches) {
			return $image;
		}

		$image = isset($matches[1]) ? $matches[1] : '';

		if (stristr($image, 'https:') === false && stristr($image, 'http:') === false) {

			if (stristr($image, '//') === false) {
				$image = rtrim(JURI::root(), '/') . '/' . ltrim($image);
			} else {
				$uri = JURI::getInstance();

				$scheme = $uri->toString(array('scheme'));

				$scheme = str_replace('://', ':', $scheme);

				$image = $scheme . $image;
			}
		}

		return $image;
	}

	/**
	 * This would inject a rel=nofollow attribute into anchor links.
	 *
	 * @access	public
	 * @param 	string	$content 	The content subject.
	 * @return 	string 				The content which is fixed.
	 */
	public static function addNoFollow($content)
	{
		/*
		'#<a\s+.*?href=[\'"]([^\'"]+)[\'"]\s*(?:title=[\'"]([^\'"]+)[\'"])?.*?>((?:(?!</a>).)*)</a>#i'
		'#<a\s*(?:href=[\'"]([^\'"]+)[\'"])?\s*(?:title=[\'"]([^\'"]+)[\'"])?.*?>((?:(?!</a>).)*)</a>#i'
		*/

		$pattern = '#<a\s*(?:href=[\'"]([^\'"]+)[\'"])?\s*(?:title=[\'"]([^\'"]+)[\'"])?.*?>((?:(?!</a>).)*)</a>#i';
		preg_match_all($pattern, $content, $matches);

		if ($matches && $matches[0]) {
			$anchors = $matches[0];

			foreach($anchors as $anchor) {

				$isInternal = false;

				// match the url
				$urlPattern = '/href=[\'"](.*)[\'"]/i';

				preg_match($urlPattern, $anchor, $matches);

				if ($matches) {
					$url = $matches[1];

					if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
						$isInternal = true;
					}
				}

				if ($isInternal) {
					continue;
				}

				// now we need to process the replacement here.
				// @rule: Try to replace any rel tag that already exist.
				$pattern = '/rel=[^>]*"/i';

				preg_match($pattern, $anchor, $matches);

				if ($matches) {
					foreach ($matches as $match) {
						// We need to make sure the rel does not have "follow" attribute
						// or the link already contain "nofollow" attribute
						if (EBString::stristr($match, 'follow') !== false || $match == 'rel="follow"' || $match == 'rel="nofollow"') {
							continue;
						}

						$result = str_ireplace('rel="', 'rel="nofollow ', $anchor);
						$content = str_ireplace($anchor, $result, $content);
					}
				} else {
					$result = str_ireplace('<a', '<a rel="nofollow"', $anchor);
					$content = str_ireplace($anchor, $result, $content);
				}

			}
		}

		return $content;
	}

	/**
	 * A pior php 4.3.0 version of
	 * html_entity_decode
	 */
	public static function unhtmlentities($string)
	{
		// replace numeric entities
		$string = preg_replace_callback('~&#x([0-9a-f]+);~i', function($m) { return chr(hexdec($m[1])); }, $string);
		$string = preg_replace_callback('~&#([0-9]+);~', function($m) { return chr($m[1]); }, $string);
		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}




	public static function linkTweets( $source )
	{
		// Link hashes
		$pattern	= '/\#(\w*)/i';
		$replace	= '<a target="_blank" href="http://twitter.com/#!/search?q=$1" rel="nofollow">$0</a>';
		$source		= preg_replace( $pattern , $replace , $source );

		// Link authors
		$pattern	= '/\@(\w*)/i';
		$replace	= '<a target="_blank" href="http://twitter.com/$1" rel="nofollow">$0</a>';
		$source		= preg_replace( $pattern , $replace , $source );

		return  $source;
	}

	public static function url2link( $string )
	{
		$newString  = $string;

		preg_match('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms', $newString, $matches);

		$patterns   = array('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
							"/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i",
							"/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i");

		$replace    = array('[bbcode-url]',
							"<a target=\"_blank\" href=\"$1\" rel=\"nofollow\">$1</a>",
							"<a target=\"_blank\" href=\"http://$2\" rel=\"nofollow\">$2</a>");

		$newString	= preg_replace($patterns, $replace, $newString);

		//now convert back again.
		if(count($matches) > 0)
		{
			$patterns   = array('/\[bbcode\-url\]/ms');
			$replace    = array($matches[0]);
			$newString	= preg_replace($patterns, $replace, $newString);
		}

		return $newString;
	}

	/**
	 * Ensure that the link contains a valid http link
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function htmlAnchorLink($url, $string)
	{
		if (!$string) {
			return $string;
		}

		//
		if( EBString::strpos( $url , 'http://' ) === false && EBString::strpos( $url , 'https://' ) === false )
		{
			$url 	= 'http://' . $url;
		}

		$pattern 	= "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
		$newString 	= preg_replace($pattern, '<a href="$1" target="_blank" rel="nofollow">' . $string. '</a>', $url);

		//this is not a link
		if ($newString == $url) {
			return $string;
		}

		return $newString;
	}

	/**
	 * Escapes a string
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function escape($string)
	{
		return FH::escape($string);
	}

	public static function tidyHTMLContent($content)
	{
		require_once(dirname(__FILE__) . '/helpers/htmlawed.php');

		// $htmLawedConfig = array( 'cdata' => 1,
		// 						 'clean_ms_char' => 1,
		// 						 'comment' => 1,
		// 						 'safe' => 1,
		// 						 'tidy' => 1,
		// 						 'valid_xhtml' =>1,
		// 						 'deny_attribute' => $denyAttr,
		// 						 'keep_bad' => 6,
		// 						 'anti_link_spam' => array('`.`','')
		// 					);

		//return htmLawed( $content, $htmLawedConfig);

		return htmLawed($content);
	}

	/* reference: http://publicmind.in/blog/url-encoding/ */
	public static function encodeURL( $url )
	{
		$reserved = array(
		":" => '!%3A!ui',
		"/" => '!%2F!ui',
		"?" => '!%3F!ui',
		"#" => '!%23!ui',
		"[" => '!%5B!ui',
		"]" => '!%5D!ui',
		"@" => '!%40!ui',
		"!" => '!%21!ui',
		"$" => '!%24!ui',
		"&" => '!%26!ui',
		"'" => '!%27!ui',
		"(" => '!%28!ui',
		")" => '!%29!ui',
		"*" => '!%2A!ui',
		"+" => '!%2B!ui',
		"," => '!%2C!ui',
		";" => '!%3B!ui',
		"=" => '!%3D!ui',
		"%" => '!%25!ui',
		);

		$url = str_replace(array('%09','%0A','%0B','%0D'),'',$url); // removes nasty whitespace
		$url = rawurlencode($url);
		$url = preg_replace(array_values($reserved), array_keys($reserved), $url);
		return $url;
	}

	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

		/* lets do another checking here since the link might not have scheme info. e.g. //www.home.com/abc.jpg */
		if (parse_url($rel, PHP_URL_HOST) != '') return $rel;

		/* queries and anchors */
		if (@$rel[0]=='#' || @$rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
		   $scheme, $host, $path */
		extract(parse_url($base));

		/* remove non-directory element from path */
		$path = preg_replace('#/[^/]*$#', '', $path);

		/* destroy path if relative url points to root */
		if ( @$rel[0] == '/') $path = '';

		/* dirty absolute URL */
		$abs = "$host$path/$rel";
		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}

	/**
	 * @author   "Sebastián Grignoli" <grignoli@framework2.com.ar>
	 * @package  forceUTF8
	 * @version  1.1
	 * @link     http://www.framework2.com.ar/dzone/forceUTF8-es/
	 * @example  http://www.framework2.com.ar/dzone/forceUTF8-es/
	  */
	public static function forceUTF8($text)
	{
		if(is_array($text))
		{
		  foreach($text as $k => $v)
		  {
			$text[$k] = EasyBlogStringHelper::forceUTF8($v);
		  }
		  return $text;
		}

		$max = strlen($text);
		$buf = "";
		for($i = 0; $i < $max; $i++){
			$c1 = $text[$i];
			if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
			  $c2 = $i+1 >= $max? "\x00" : $text[$i+1];
			  $c3 = $i+2 >= $max? "\x00" : $text[$i+2];
			  $c4 = $i+3 >= $max? "\x00" : $text[$i+3];
				if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2;
						$i++;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} else { //doesn't look like UTF8, but should be converted
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = (($c1 & "\x3f") | "\x80");
						$buf .= $cc1 . $cc2;
				}
			} elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
					$cc1 = (chr(ord($c1) / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$buf .= $cc1 . $cc2;
			} else { // it doesn't need convesion
				$buf .= $c1;
			}
		}
		return $buf;
	}

	public static function forceLatin1($text)
	{
	  if(is_array($text)) {
		foreach($text as $k => $v) {
		  $text[$k] = EasyBlogStringHelper::forceLatin1($v);
		}
		return $text;
	  }
	  return utf8_decode( EasyBlogStringHelper::forceUTF8($text) );
	}

	public static function fixUTF8($text)
	{
	  if(is_array($text)) {
		foreach($text as $k => $v) {
		  $text[$k] = EasyBlogStringHelper::fixUTF8($v);
		}
		return $text;
	  }

	  $last = "";
	  while($last <> $text){
		$last = $text;
		$text = EasyBlogStringHelper::forceUTF8( utf8_decode( EasyBlogStringHelper::forceUTF8($text) ) );
	  }
	  return $text;
	}


	/**
	 * Returns an array of blocked words.
	 *
	 * @access	public
	 * @param 	null
	 * @return 	array
	 */
	public function getBlockedWords()
	{
		static $words = null;

		if( is_null( $words ) )
		{
			$config = EB::config();
			$words = trim($config->get('main_blocked_words', ''), ',');

			if(!empty($words)) {
				$words = explode(',', $words);
			}

			if (empty( $words ) ) {
				$words = [];
			}
		}

		return $words;
	}

	/**
	 * Determines if the text provided contains any blocked words
	 *
	 * @access	public
	 * @param	string	$text	The text to lookup for
	 * @return	boolean			True if contains blocked words, false otherwise.
	 *
	 */
	public function hasBlockedWords( $text )
	{
		$words		= self::getBlockedWords();

		if( empty( $words ) || !$words )
		{
			return false;
		}

		foreach( $words as $word )
		{
			if( preg_match('/\b'.$word.'\b/i', $text) )
			{
				// Immediately exit the method since we now know that there's at least
				// 1 blocked word.
				return $word;
			}
		}

		return false;
	}

	/**
	 * Converts color code into RGB values
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function hexToRGB($hex)
	{
		$hex = str_ireplace('#', '', $hex);
		$rgb = array();
		$rgb['r'] = hexdec(substr($hex, 0, 2));
		$rgb['g'] = hexdec(substr($hex, 2, 2));
		$rgb['b'] = hexdec(substr($hex, 4, 2));

		$str = $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'];
		return $str;
	}

	/**
	 * Darken a given color
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function darken($rgb, $darker = 2)
	{
		$hash = (strpos($rgb, '#') !== false) ? '#' : '';
		$rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
		if(strlen($rgb) != 6) return $hash.'000000';
		$darker = ($darker > 1) ? $darker : 1;

		list($R16,$G16,$B16) = str_split($rgb,2);

		$R = sprintf("%02X", floor(hexdec($R16)/$darker));
		$G = sprintf("%02X", floor(hexdec($G16)/$darker));
		$B = sprintf("%02X", floor(hexdec($B16)/$darker));

		return $hash.$R.$G.$B;
	}

	/**
	 * Determines if the given string is a valid url
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isHyperlink($string)
	{
		// http://
		$result = EBString::substr($string, 0, 7);

		if ($result == 'http://') {
			return true;
		}

		// https://
		$result = EBString::substr($string, 0, 8);

		if ($result == 'https://') {
			return true;
		}

		// //path
		$result = EBString::substr($string, 0, 2);

		if ($result == '//') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the domain is valid
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isValidDomain($url)
	{
		// $regex = '/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/';
		$regex = '/([a-zA-Z0-9\-_]+\.)?[a-zA-Z0-9\-_]+\.[a-zA-Z]{2,5}/';
		preg_match($regex, $url, $matches);

		if (!$matches) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the domain is valid
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isValidEmail($data, $strict = false)
	{
		$regex = $strict?
			'/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' :
			'/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]+)$/i'
		;

		if (preg_match($regex, trim($data), $matches)) {
			return array($matches[1], $matches[2]);
		}

		return false;
	}

	public function cleanHtml($content='')
	{
		$pattern = array(
			'/<p><br _mce_bogus="1"><\/p>/i',
			'/<p><br mce_bogus="1"><\/p>/i',
			'/<br _mce_bogus="1">/i',
			'/<br mce_bogus="1">/i',
			'/<p><br><\/p>/i'
		);

		$replace = array('','','','','');
		$content = preg_replace($pattern, $replace, $content);

		return $content;
	}

	/**
	 * Given a set of content, filter the content by normalizing the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function filterHtml($content = '')
	{
		static $filter;
		static $filterType;

		// If filter hasn't been initialized before, do it now.
		if (!isset($filter)) {

			jimport('joomla.filter.filterinput');

			// Get tags & attributes that should be stripped.
			$filterTags = EasyBlogAcl::getFilterTags();
			$filterAttrs = EasyBlogAcl::getFilterAttributes();
			$filterType = 'html';

			// Create filter instance.
			$filter = JFilterInput::getInstance($filterTags, $filterAttrs, 1, 1, 0);
			$filter->tagBlacklist  = $filterTags;
			$filter->attrBlacklist = $filterAttrs;

			// Disable filtering if there's nothing to filter
			if (count($filterTags) < 1 && count($filterAttrs) < 1) {
				$filter = false;
			}
		}

		// If we can skip filtering, just return content.
		if ($filter == false) {
			return $content;
		}

		// Strip blacklisted tags & attributes.
		return $filter->clean($content, $filterType);
	}

	/**
	 * Search for an image tag in a given content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function searchImage($content, $limit = 1)
	{
		$pattern = '#<img[^>]*>#i';

		preg_match_all($pattern, $content, $matches);

		if ($matches[0]) {

			if ($limit == 1) {
				return $matches[0][0];
			}

			return $matches[0];
		}

		return array();
	}

	/**
	 * Method to remove known emoji
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeEmoji($string)
	{
		// Based on unicode table https://unicode-table.com/en
		$pattern = '/[\x{2300}-\x{2426}]|[\x{2B00}-\x{2BFF}]|[\x{2580}-\x{2740}]|[\x{10000}-\x{10FFFF}]/u';

		return preg_replace($pattern, '', $string);
	}

	/**
	 * Process image to be used in AMP Article
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function processAMP($content)
	{
		// style attribute is not allowed in AMP
		$output = preg_replace('/(<[^>]+) style=".*?"|data-style=".*?"/i', '$1', $content);

		// target=_blank is not allowed in AMP
		$output = preg_replace('/(<a.*?)[ ]?target="_blank"(.*?)/', '$1$2', $output);

		// Just in case people using target=blank
		$output = preg_replace('/(<a.*?)[ ]?target="blank"(.*?)/', '$1$2', $output);

		// Some of rel attribute not allowed in AMP
		$output = preg_replace('/(<a.*?)[ ]?rel=".*?"/', '$1$2', $output);

		return $output;
	}

	/**
	 * Method to count the words in a string
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function countWord($string, $recalculate = false)
	{
		// Only proceeed this if re-calculate the content word
		// Add extra space for the sentence between the HTML tag before stripped it #1669
		if ($recalculate) {
			$string = preg_replace('#\<(.+?)\>#', '<$1> ', $string);
		}

		// Ensure that the tags is stripped
		$string = strip_tags($string);

		// ori:
		// $words = preg_split("/([\s]+)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$words = array();
		$test = preg_split("~[\p{Z}\p{Pc}]+~u", $string,  -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		if ($test) {
			foreach ($test as $tword) {

				if (preg_match('/[\w]/', $tword)) {
					$words[] = $tword;
				} else {

					$uniwords = array();

					// for Japanese, Chinese and Korean characters, we need to check further if we need to split by each character or not to
					// determine the word counts.
					if ($this->isChineseString($tword) || $this->isJapaneseString($tword) || $this->isKoreanString($tword)) {
						$uniwords = preg_split("//u", $tword, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
					} else {
						$uniwords[] = $tword;
					}

					if ($uniwords) {
						$words = array_merge($words, $uniwords);
					}
				}
			}
		}

		$length = 0;
		foreach ($words as $word) {

			// Since we added extra space for each of the HTML tag
			// It should skip it those standalone symbol and empty space in the content
			// Because those symbol should stick with the word e.g. comma, dot and etc. #1669
			$excludeSymbols = array(',', '.', '?', '!', ':', ';', '');

			// Ensure that trim all these &nbsp; in the content
			$word = trim($word, " \n\r\xC2\xA0");

			// Skip it if contain those excluded symbols and white space
			if (in_array($word, $excludeSymbols)) {
				continue;
			}

			$length++;
		}

		return $length;
	}

	/**
	 * Method to check if string is japanese characters
	 *
	 * @since	5.3.4
	 * @access	public
	 */
	public function isJapaneseString($str)
	{
		if (preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $str)) {
			return true;
		}

		return false;
	}

	/**
	 * Method to check if string is chinese characters
	 *
	 * @since	5.3.4
	 * @access	public
	 */
	public function isChineseString($str)
	{
		if (preg_match('/\\p{Han}/u', $str)) {
			return true;
		}

		return false;
	}

	/**
	 * Method to check if string is korean characters
	 *
	 * @since	5.3.4
	 * @access	public
	 */
	public function isKoreanString($str)
	{
		if (preg_match('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', $str)) {
			return true;
		}

		return false;
	}


	/**
	 * Method to add initial slashes on relative url
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function relAddSlashes($string)
	{
		// $pattern = '/\<img.+src=[\"|\'](?!https?:\/\/)([^\/].+?)[\"|\']/';
		$pattern = '/(?:href|src)=[\"|\'](?!https?:\/\/)([^\/].+?)[\"|\']/';

		preg_match_all($pattern, $string, $matches);

		if ($matches && $matches[1]) {
			$images = $matches[1];

			$images = array_unique($images);

			// add the subdomain if the site under subfolder
			$root = rtrim(JURI::root(), '/');
			$root = preg_replace("(^https?://)", "//", $root);

			foreach ($images as $image) {

				$replaceContent = '/' . $image;

				// Ensure that the image URL doesn't contain the root domain then only do replacement
				if (strpos($image, $root) === false) {
					$replaceContent = $root . '/' . $image;
				}

				$string = str_replace($image, $replaceContent, $string);
			}
		}

		return $string;
	}

	/**
	 * Method to fix unclosed html tag and quotes
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function fixUnclosedTags($content)
	{
		// Wrap with div first so that dom element can include any <script> tag into the content. #1577
		$content = '<div>' . $content . '</div>';

		// Suppress any warnings from libxml
		libxml_use_internal_errors(true);

		$dom = new \DOMDocument;
		$dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);

		// Strip wrapping <html> and <body> tags
		$mock = new \DOMDocument;
		$body = $dom->getElementsByTagName('body')->item(0);

		foreach ($body->childNodes as $child) {;
			$mock->appendChild($mock->importNode($child, true));
		}

		$content = trim($mock->saveHTML());

		// Remove the encoding code which will caused some Javascript parse JSON issue
		$content = str_replace('&#13;', '', $content);

		// Remove first and last div
		$content = substr($content, 5); // <div>
		$content = substr($content, 0, -6); // </div>

		return $content;
	}

	/**
	 * Method to truncate the string while maintaining the HTML integrity of the string
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function truncateWithHtml($text, $max = 250, $ending = '', $exact = false)
	{
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

		$total_length = EBString::strlen($ending);
		$open_tags = array();
		$truncate = '';

		foreach ($lines as $line_matchings) {

			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {

				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {

					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);

					if ($pos !== false) {
						unset($open_tags[$pos]);
					}

				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {

					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, EBString::strtolower($tag_matchings[1]));
				}

				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}

			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = EBString::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));

			if ($total_length + $content_length > $max) {

				// the number of characters which are left
				$left = $max - $total_length;
				$entities_length = 0;

				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {

					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1] + 1 - $entities_length <= $left) {
							$left--;
							$entities_length += EBString::strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}

				$truncate .= EBString::substr($line_matchings[2], 0, $left + $entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}

			// if the maximum length is reached, get off the loop
			if ($total_length >= $max) {
				break;
			}
		}

		// If the words shouldn't be cut in the middle...
		if (!$exact) {

			// ...search the last occurance of a space...
			$spacepos = EBString::strrpos($truncate, ' ');

			// ...and cut the text in this position
			if (isset($spacepos)) {

				// lets further test if the about truncate string has a html tag or not.
				$remainingString = EBString::substr($truncate, $spacepos + 1);
				$remainingString = trim($remainingString);

				// check if string contain any html closing/opening tag before we proceed. #463
				$closingTagV1 = EBString::strpos($remainingString, '>');
				$closingTagV2 = EBString::strpos($remainingString, '/>');

				// Everything is safe. Let's truncate it.
				if ((!$closingTagV1 && !$closingTagV2) || ($closingTagV1 === 0 && $closingTagV2 === 0)) {
					$truncate = EBString::substr($truncate, 0, $spacepos);
				}
			}
		}

		// add the defined ending to the text
		$truncate .= $ending;

		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}

		return $truncate;
	}

	/**
	 * Method to determine if the string ends with the certain words
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function endsWith($words, $string)
	{
		$count = strlen($words);

		return substr($string, -$count) === $words;
	}


	/**
	 * Method to patch the contents from previous upgrades
	 *
	 * @since	6.0.6
	 * @access	public
	 */
	public function patchContent($text)
	{
		// Replace the 6.x column block new HTML DOM from previous version
		$text = $this->replaceColumnBlockHTMLCode($text);

		$search = ['<i class="fa fa-chevron-right">', '<i class="fa fa-chevron-left">'];
		$replace = ['<i class="fdi fa fa-chevron-right">', '<i class="fdi fa fa-chevron-left">'];

		$newText = str_replace($search, $replace, $text);
		return $newText;
	}

	/**
	 * Replace the old to new columns block HTML DOM from previous upgrades
	 *
	 * @since	6.0.7
	 * @access	public
	 */
	public function replaceColumnBlockHTMLCode($text)
	{
		// Easyblog 5.x version using this DOM e.g. <div class="col col-md-1" data-size="1">
		// Easyblog 6.x version using this DOM e.g. <div class="col-12 col-md-1" data-size="1">

		// $search = [
		// 	'<div class="col col-md-1" data-size="1">',
		// 	'<div class="col col-md-2" data-size="2">',
		// 	'<div class="col col-md-3" data-size="3">',
		// 	'<div class="col col-md-4" data-size="4">',
		// 	'<div class="col col-md-5" data-size="5">',
		// 	'<div class="col col-md-6" data-size="6">',
		// 	'<div class="col col-md-7" data-size="7">',
		// 	'<div class="col col-md-8" data-size="8">',
		// 	'<div class="col col-md-9" data-size="9">',
		// 	'<div class="col col-md-10" data-size="10">',
		// 	'<div class="col col-md-11" data-size="11">',
		// 	'<div class="col col-md-12" data-size="12">'
		// ];

		// $replace = [
		// 	'<div class="col-12 col-md-1" data-size="1">',
		// 	'<div class="col-12 col-md-2" data-size="2">',
		// 	'<div class="col-12 col-md-3" data-size="3">',
		// 	'<div class="col-12 col-md-4" data-size="4">',
		// 	'<div class="col-12 col-md-5" data-size="5">',
		// 	'<div class="col-12 col-md-6" data-size="6">',
		// 	'<div class="col-12 col-md-7" data-size="7">',
		// 	'<div class="col-12 col-md-8" data-size="8">',
		// 	'<div class="col-12 col-md-9" data-size="9">',
		// 	'<div class="col-12 col-md-10" data-size="10">',
		// 	'<div class="col-12 col-md-11" data-size="11">',
		// 	'<div class="col-12 col-md-12" data-size="12">'
		// ];

		// $text = str_replace($search, $replace, $text);

		$pattern = [
			'/\<div data\-size\=\"(\d)\"\ class\=\"col col\-md\-(\d)\"\>/ims',
			'/\<div class\=\"col col\-md\-(\d)\" data\-size\=\"(\d)\"\>/ims',
		];

		$replace = [
			'<div data-size="$1" class="col-12 col-md-$2">',
			'<div class="col-12 col-md-$1" data-size="$2">'
		];

		$text = preg_replace($pattern, $replace, $text);

		return $text;
	}
}
