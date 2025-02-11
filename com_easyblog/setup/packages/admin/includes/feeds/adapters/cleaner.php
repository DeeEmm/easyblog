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

class EasyBlogFeedAdapterCleaner extends EasyBlog
{
	public function cleanup($contents)
	{
	    // Cleanup the contents by ensuring that there's no whitespaces or any funky chars before the xml tag
	    $pattern = '/(.*?)<\?xml version/is';
	    $replace = '<?xml version';

	    $contents = preg_replace($pattern, $replace, $contents, 1);

	    // If there's a missing xml definition because some sites are just messed up, manually prepend them
	    if (strpos($contents, '<?xml version') === false) {
	    	$contents = '<?xml version="1.0" encoding="utf-8"?>' . $contents;
		}

		return $contents;
	}
}
