<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogInstantArticles extends EasyBlog
{
	public static function renders($content = '')
	{
		self::removesStyles($content);
		self::removeSpaces($content);
		self::processInstantContent($content);
		self::processInstantVideos($content);
		self::processInstantImages($content);
		self::processInstantGallery($content);

		return $content;
	}

	public static function removesStyles(&$content = '')
	{
		$regex = '/(<[^>]+) style=".*?"/i';
		$content = preg_replace($regex, '$1', $content);
	}

	public static function removeSpaces(&$content = '')
	{
		$content = preg_replace("#<p>(\s|&nbsp;|</?\s?br\s?/?>)*</?p>#", "", $content);
		$content = preg_replace("#(&nbsp;)#", "", $content);
	}

	public static function processInstantContent(&$content = '')
	{
		$unallowed = array('h3', 'h4', 'h5', 'h6');

		foreach ($unallowed as $item) {
			$content = preg_replace('#(<'. $item .'>)#', '<h2>', $content);
			$content = preg_replace('#(</' . $item .'>)#', '</h2>', $content);
		}

		$content = preg_replace('/<strong>(.*?)<\/strong>/', '<b>$1</b>', $content);
	}

	public static function processInstantVideos(&$content = '')
	{
		$content = EB::videos()->processInstantVideos($content);
	}

	public static function processInstantImages(&$content = '')
	{
		$content = EB::image()->processInstantImages($content);
	}

	public static function processInstantGallery(&$content = '')
	{
		$content = EB::gallery()->processInstantGallery($content);
	}

	public static function clean(&$content = '', $debug = false)
	{
		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		// $content = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $content);

		$content = preg_replace("#<p>\s*(<img [^>]*>)\s*</p>#iUum", "$1", $content);
		$content = str_replace('src="//', 'src="' . $scheme . '//', $content);
		$content = preg_replace('#(<br\ ?\/?>)#iUum', '<br>', $content);
		$content = preg_replace('#(<div class="dhr">.*<\/div>)#', '', $content);
		$content = preg_replace('#(<!--.*.-->)#', '', $content);
		$content = preg_replace('#(<em>\s*<\/em>)#', '', $content);
		$content = preg_replace('#(<p>\s*<\/p>)#iUum', '', $content);
		$content = preg_replace('#(<figure>\s*<\/figure>)#iUum', '', $content);

		$content = preg_replace('/(\>)\s*(\<)/m', '$1$2', $content);
		$content = str_replace(PHP_EOL, '', $content);
		$content = preg_replace( "/\r|\n/", '', $content);
		$content = preg_replace('/\t+/', '', $content);

		// Remove p tag from wrapping the figure tag
		$content = str_replace('<p><figure', '<figure', $content);
		$content = str_replace('</figure></p>', '</figure>', $content);

		// Remove any empty p tags <p><em></p>
		$content = str_replace('<p><em></p>', '', $content);
	}
}

class EBIA extends EasyBlogInstantArticles {}