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

class EasyBlogCrawlerKeywords
{
	// Document keywords
	private $keywords	= null;

	const PATTERN		= '/\<meta name="keywords" content=*[\"\']{0,1}([^\"\\>]*)/i';
	
	/**
	 * Ruleset to process document title
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.	 	 	 
	 */	 	
	public function process( &$contents )
	{
		preg_match( self::PATTERN , $contents , $matches );

		$this->keywords	= isset( $matches[ 1 ] ) ? $matches[ 1 ] : '';
		
		return $this->keywords;
	}

	/**
	 * Returns the keywords of the document.
	 * 
	 * @return	string		The document keywords.
	 */
	public function get()
	{
		return $this->keywords;
	}
}
