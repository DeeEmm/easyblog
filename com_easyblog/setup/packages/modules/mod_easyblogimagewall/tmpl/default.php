<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($posts) { ?>
<div id="eb" class="eb-mod mod-easyblogimagewall<?php echo $modules->getWrapperClass();?>">
	<?php $i = 1; ?>
	<div class="ezb-grids">
		<?php foreach ($posts as $post) { ?>
			<div class="ezb-grid" style="width: <?php echo 100 / $params->get('columns', 1);?>%">
				<a class="ezb-card" href="<?php echo $post->getPermalink(); ?>" title="<?php echo $post->getTitle(); ?>" 
					style="background-image: url('<?php echo EB::image()->isImage($post->getImage()) ? $post->getImage('original', true, true, true) : EB::getPlaceholderImage(false, 'video'); ?>');">
					<span><?php echo $post->getTitle(); ?></span>
				</a>
			</div>
			<?php if ($i % $params->get('columns', 1) == 0) { ?>
				<div class="clear"></div>
			<?php } ?>
			<?php $i++; ?>
		<?php } ?>
	</div>
</div>
<?php } ?>

