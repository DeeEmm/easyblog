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
	<div class="eb-post-nickel">

		<?php if ($hasAdminTools || $post->canFavourite() || $params->get('post_author', true) || $params->get('post_date', true) || $params->get('post_date', true)) { ?>
			<div class="eb-post-top row-table<?php echo !$this->config->get('layout_avatar') || !$params->get('post_author_avatar', true) ? ' no-avatar' : '';?>">
				<?php if ($this->config->get('layout_avatar') && $params->get('post_author_avatar', true)) { ?>
				<div class="col-cell cell-tight">
					<div class="eb-post-avatar t-pr--sm">
						<?php echo $this->html('post.list.authorAvatar', $post); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('post_author', true) || $params->get('post_date', true)) { ?>
				<div class="col-cell">
					<?php if ($params->get('post_author', true)) { ?>
						<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
					<?php } ?>

					<?php if ($params->get('post_date', true)) { ?>
						<?php echo $this->html('post.date', $post, $params->get('post_date_source', 'created')); ?>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($hasAdminTools || $post->canFavourite()) { ?>
					<div class="col-cell cell-tight text-right">
						<?php echo $this->html('post.admin', $post, $return); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>


		<div class="eb-post-content">
			<div class="eb-post-head">
				<?php if ($params->get('post_title', true)) { ?>
					<?php echo $this->html('post.list.title', $post); ?>
				<?php } ?>

				<?php if ($params->get('post_type', false) || $params->get('post_date', true) || $params->get('post_author', true) || $params->get('post_category', true) || $post->isFeatured || $post->isFavourited()) { ?>
				<div class="eb-post-meta text-muted">
					<?php if ($params->get('post_type', false)) { ?>
					<div>
						<?php echo $this->html('post.icon', $post->getType()); ?>
					</div>
					<?php } ?>

					<?php if ($params->get('post_category', true) && $post->categories) { ?>
					<div>
						<?php echo $this->html('post.category', $post->categories); ?>
					</div>
					<?php } ?>

					<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
					<div>
						<?php echo $this->html('post.contributor', $post->getBlogContribution(), true); ?>
					</div>
					<?php } ?>

					<?php if ($post->isFeatured) { ?>
					<div>
						<?php echo $this->html('post.featured', true); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<?php if (!$protected) { ?>
				<?php echo $this->html('post.list.content', $post, $params); ?>

				<?php if ($post->hasReadmore() && $params->get('post_readmore', true)) { ?>
				<div class="eb-post-more mt-20">
					<?php echo $this->html('post.list.readmore', $post); ?>
				</div>
				<?php } ?>

				<?php if ($post->fields && $params->get('post_fields', true)) { ?>
					<?php echo $this->html('post.fields', $post, $post->fields); ?>
				<?php } ?>

				<?php if ($post->copyrights && $params->get('post_copyrights', true)) { ?>
				<div class="t-mb--md">
					<?php echo $this->html('post.copyrights', $post->copyrights); ?>
				</div>
				<?php } ?>

				<?php if ($this->config->get('main_ratings') && $params->get('post_ratings', true)) { ?>
				<div class="eb-post-actions text-muted">
					<div class="col-cell">
						<?php echo $this->html('post.ratings', $post, $this->config->get('main_ratings_frontpage_locked')); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('post_tags', true)) { ?>
					<?php echo $this->html('post.tags', $post->tags); ?>
				<?php } ?>

				<?php if ($params->get('post_social_buttons', true)) { ?>
					<?php echo $this->html('post.socialShare', $post, 'listings'); ?>
				<?php } ?>
			<?php } ?>

			<?php if ($protected) { ?>
				<?php echo $this->html('post.protectedPost', $post); ?>
			<?php } ?>
		</div>

		<?php if (!$protected && ($params->get('post_hits', true) || $params->get('post_comment_counter', true))) { ?>
		<div class="eb-post-foot">
			<?php if ($params->get('post_hits', true)) { ?>
			<div class="col-cell">
				<?php echo $this->html('post.hits', $post, true); ?>
			</div>
			<?php } ?>

			<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
			<div class="col-cell">
				<?php echo $this->html('post.comments', $post->getTotalComments(), $post->getCommentsPermalink(), true); ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
	<?php echo $this->html('post.list.schema', $post); ?>
</div>
