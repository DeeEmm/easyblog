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
<div class="eb-card__hd">
	<div class="o-aspect-ratio" style="--aspect-ratio: <?php echo $coverAspectRatio; ?>;">
		<?php if ($post->posttype == 'video') { ?>
			<?php foreach ($post->videos as $video) { ?>
				<?php echo $video->html;?>
			<?php } ?>
		<?php } else { ?>
			<?php if ($post->getImage() && $isImage) { ?>
				<a href="<?php echo $post->getPermalink();?>"
					style="
						background-image: url('<?php echo $cover->url; ?>');
						background-position: center;
					" alt="<?php echo $this->escape($cover->alt); ?>" >
				</a>
			<?php } else { ?>
				<?php if ($post->isEmbedCover()) { ?>
					<?php echo $cover->videoEmbed; ?>
				<?php } else { ?>
					<?php echo $cover->video; ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</div>
</div>
