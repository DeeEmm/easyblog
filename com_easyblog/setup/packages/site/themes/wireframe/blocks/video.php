<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$containerAttrs = '';

if (!empty($width) || !empty($height)) {

	$containerAttrs = ' style="';

	if ($width) {
		$containerAttrs .= 'width:' . $width . ';';
	}

	if ($height) {
		$containerAttrs .= 'height:' . $height . ';';
	}

	$containerAttrs .= '"';
}

$viewportAttrs = '';

if (!is_null($ratio)) {
	$viewportAttrs = ' style="padding-top: ' . $ratio . '"';
}

$videoType = 'video/x-flv';

if (strpos($url, '.mp4') !== false
	|| strpos($url, '.MP4') !== false
	|| strpos($url, '.3gp') !== false
	|| strpos($url, '.3GP') !== false
	|| strpos($url, '.mov') !== false
	|| strpos($url, '.MOV') !== false
	|| strpos($url, '.webm') !== false) {
	$videoType = 'video/mp4';
}

// Set autoplay for iPhone/iPad in order to render first frame of the video.
// https://stackoverflow.com/a/39231763/3152298
if (EB::responsive()->isIphone() || EB::responsive()->isIpad()) {
	$autoplay = true;
}

?>

<?php if (!$isCover) { ?>
<div class="eb-video<?php echo $responsive ? '' : ' is-responsive'; ?>"<?php echo $containerAttrs; ?>>
	<div class="eb-video-viewport"<?php echo $viewportAttrs; ?>>
<?php } ?>
		<video id="<?php echo $uid; ?>" class="video-js vjs-default-skin vjs-big-play-centered" width="100%" height="100%" preload="auto">
			<source src="<?php echo $url;?>?cache_bust=true" type="<?php echo $videoType; ?>" />
		</video>
<?php if (!$isCover) { ?>
	</div>
</div>
<?php } ?>

<script>
EasyBlog.require()
.library('videojs')
.done(function() {
	videojs('<?php echo $uid;?>', {
			"controls": true,
			"autoplay": <?php echo $autoplay ? 'true' : 'false';?>,
			"loop": <?php echo $loop ? 'true' : 'false';?>
		}, function(){
			<?php if ($muted) { ?>
			this.muted(true);
			<?php } ?>
		});
});
</script>

