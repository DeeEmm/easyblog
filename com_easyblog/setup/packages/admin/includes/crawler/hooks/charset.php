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

class EasyBlogCrawlerCharset
{
	// Document charset
	private $charset	= null;

	public $patterns 	= array(
									// Example: <meta http-equiv="content-type" content="text/html; charset=utf-8">
									'/<meta http-equiv="[cC]ontent-[tT]ype" content="text\/html;[]charset=*[\"\']{0,1}([^\"\\>]*)"/i',

									// Example: <meta charset="utf-8">
									'/<meta charset="(.*)">/i'
								);
	
	/**
	 * Ruleset to process document charsets
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.	 	 	 
	 */	 	
	public function process( $parser , &$contents )
	{
		foreach( $this->patterns as $pattern )
		{
			preg_match( $pattern , $contents , $matches );	

			if( isset($matches[1]) )
			{
				// If the match contains =, we need to explode it.
				if( stripos( $matches[1] , '=' ) !== false )
				{
					$tmp 	= explode( '=' , $matches[ 1 ] );
					
					return strtolower( $tmp[1] );
				}
				return strtolower( $matches[ 1 ] );
			}
		}

		return false;
	}
}
