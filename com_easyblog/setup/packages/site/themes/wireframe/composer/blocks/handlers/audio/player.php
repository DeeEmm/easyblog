<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-block-audio" data-responsive="400,300,200,100">
     <div class="audiojs-track" data-audio-infobox>
        <div class="pull-right">
            <a href="" data-audio-download><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DOWNLOAD');?></a>
        </div>
        <div class="overflow-no">
            <span>
                <b data-audio-artist><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_ARTIST');?></b>
                <span data-audio-track-separator>-</span>
                <span data-audio-track></span>
            </span>
        </div>
    </div>
    <audio preload="none" />
</div>