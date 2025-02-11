<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');


$dir = dirname(__FILE__);
require_once($dir."/HTMLDiff.php");

/**
 * Return the difference between two HTML documents.
 * @param String $html1 The first HTML file / snippets
 * @param String $html2 The second HTML file / snippets
 * @return String An HTML document that represents the difference between the two HTML documents/
*/

function html_diff($html1, $html2)
{
	$diff = new HtmlDiff( $html1, $html2 );
	$diff->build();
	return $diff->getDifference();
}
