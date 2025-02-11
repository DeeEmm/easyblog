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
<div class="eb-post-listing__item" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
	<div class="eb-card">
		<?php echo $this->html('post.admin', $post, $return); ?>

		<?php echo $this->html('post.list.cover', $post, $params); ?>

		<div class="eb-card__content">
			<div class="eb-card__bd eb-card--border">
				<?php if ($post->isFeatured) { ?>
				<div>
					<?php echo $this->html('post.featured', true); ?>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_title', true)) { ?>
					<?php echo $this->html('post.list.title', $post); ?>
				<?php } ?>

				<?php if (!$protected) { ?>
					<?php if (in_array($post->getType(), ['photo', 'standard', 'twitter', 'email', 'link'])) { ?>
					<div class="eb-post-body mt-10 type-<?php echo $post->getType(); ?>" data-blog-post-content>
						<?php echo $post->getIntro();?>
					</div>
					<?php } ?>

					<?php if ($post->hasReadmore() && $params->get('post_readmore', true)) { ?>
					<div class="eb-post-more mt-20">
						<?php echo $this->html('post.list.readmore', $post); ?>
					</div>
					<?php } ?>

					<div class="eb-card__meta">
						<div class="eb-post-actions">
							<?php echo $this->html('post.list.actions', $post, $params); ?>
						</div>
					</div>
				<?php } ?>

				<?php if ($protected) { ?>
					<?php echo $this->html('post.protectedPost', $post); ?>
				<?php } ?>
			</div>

			<div class="eb-card__ft">
				<div class="eb-card__ft-content eb-card--border">
					<div class="t-d--flex">
						<div class="t-flex-grow--1 t-min-width--0">
							<?php if ($params->get('post_date', true)) { ?>
							<div>
								<?php echo $this->html('post.date', $post, $params->get('post_date_source', 'created')); ?>
							</div>
							<?php } ?>

							<?php if ($params->get('post_category', true) && $post->categories) { ?>
							<div class="mt-5 t-text--truncate">
								<?php echo $this->html('post.category', $post->categories); ?>
							</div>
							<?php } ?>
						</div>

						<div class="t-flex-shrink--0">
							<?php if ($this->config->get('layout_avatar') && $params->get('post_author_avatar', true)) { ?>
							<div class="eb-post-avatar">
								<?php echo $this->html('post.list.authorAvatar', $post); ?>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->html('post.list.schema', $post); ?>
	</div>
</div>