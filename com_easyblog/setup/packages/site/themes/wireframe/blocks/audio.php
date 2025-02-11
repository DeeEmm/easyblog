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
?>
<script>
EasyBlog.require()
.library('audiojs')
.done(function($) {
    $.audiojs.events.ready(function(){
        var element = $('#<?php echo $uid;?>');
        var settings = {
        	"autoplay": <?php echo $autoplay ? 'true' : 'false'; ?>,
            "loop": <?php echo isset($loop) && $loop ? 'true' : 'false';?>
        };
        $.audiojs.create(element, settings);
    });
});
</script>
<div class="eb-block-audio" data-responsive="400,300,200,100">
	<?php if ($showDownload || $showArtist || ($showTrack && $track)) { ?>
	 <div class="audiojs-track" data-audio-infobox>
		<div class="pull-right">
			<?php if ($showDownload) { ?>
			<a href="<?php echo $url;?>" target="_blank" data-audio-download><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DOWNLOAD');?></a>
			<?php } ?>
		</div>
		<div class="overflow-no">
			<span>
				<?php if ($showArtist) { ?>
				<b data-audio-artist><?php echo $artist ? $artist : JText::_('COM_EASYBLOG_BLOCKS_AUDIO_ARTIST');?></b>
				<span data-audio-track-separator>-</span>
				<?php } ?>

				<?php if ($showTrack && $track) { ?>
				<span data-audio-track><?php echo $track;?></span>
				<?php } ?>
			</span>
		</div>
	</div>
	<?php } ?>
	<audio id="<?php echo $uid;?>" src="<?php echo $url;?>" preload="auto" <?php echo $autoplay ? 'autoplay="1"' : '';?> />
</div>
