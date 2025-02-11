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
<div class="eb-post-related">
	<h4 class="eb-section-heading reset-heading"><?php echo JText::_('COM_EASYBLOG_RELATED_POSTS');?></h4>

	<div class="eb-entry-related clearfix <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
		<?php foreach ($posts as $post) { ?>
		<div>
			<?php if ($showCover) { ?>
				<?php if ($post->isImage) { ?>
					<a href="<?php echo $post->getPermalink();?>" class="eb-related-thumb" style="background-image: url('<?php echo $post->cover->url;?>') !important;"></a>
				<?php } else { ?>
					<?php if ($post->isEmbedCover()) { ?>
						<div class="o-aspect-ratio" style="--aspect-ratio: <?php echo $coverAspectRatio; ?>;">
							<?php echo $post->cover->videoEmbed; ?>
						</div>
					<?php } else { ?>
						<?php echo $post->cover->video; ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>

			<h3 class="eb-related-title">
				<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
			</h3>

			<div class="text-muted">
				<a class="eb-related-category text-inherit" href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo $post->getPrimaryCategory()->getTitle();?></a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
