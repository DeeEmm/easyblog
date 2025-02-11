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
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogVideoSoundcloud
{
    public function getEmbedHTML($url, $width, $height, $amp = false)
    {
        $code = file_get_contents("http://soundcloud.com/oembed?format=json&url=".$url."&maxheight=".$height."&maxwidth=".$width);
        $code = json_decode($code);

        if ($code) {
            return $code->html;
        }

        return false;
    }
}
