<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-lottie-responsive" data-lottie-responsive>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
	<lottie-player <?php echo $url ? 'src="' . $url . '"' : ''; ?> 
		background="transparent"
		speed="1"
		style="width: 100%; height: 100%;"
		<?php echo $loop ? 'loop' : ''; ?>
		<?php echo $autoplay ? 'autoplay' : ''; ?>
		<?php echo $hover ? 'hover' : ''; ?>
	>
	</lottie-player>

	<?php if ($isEdit && !(strpos($url, 'lottiefiles.com') !== false)) { ?>
	<input type="hidden" value="1" name="<?php echo 'lottie_files[' . basename($url) . ']'; ?>">
	<?php } ?>
</div>

<?php if ($isEdit) { ?>
<div class="o-loader-wrapper">
	<div class="o-loader o-loader--inline"></div>
</div>
<?php } ?>