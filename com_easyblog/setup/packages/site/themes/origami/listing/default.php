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
	<div class="eb-post">
		<div class="eb-post-side">
			<?php if ($this->config->get('layout_avatar') && $params->get('post_author_avatar', true)) { ?>
			<div class="eb-post-avatar">
				<?php echo $this->html('post.list.authorAvatar', $post); ?>
			</div>
			<?php } ?>

			<div>
				<div class="eb-post-calendar" content="<?php echo $post->getDisplayDate('created')->format(JText::_('DATE_FORMAT_LC4'));?>">
					<div class="eb-post-calendar-m">
						<?php echo $post->getDisplayDate($params->get('post_date_source', 'created'))->format('M');?>
					</div>
					<div class="eb-post-calendar-d">
						<?php echo $post->getDisplayDate($params->get('post_date_source', 'created'))->format('d');?>
					</div>
				</div>
			</div>

			<?php if ($params->get('post_type', false)) { ?>
			<div>
				<?php echo $this->html('post.icon', $post->getType()); ?>
			</div>
			<?php } ?>

			<?php if ($post->isFeatured) { ?>
				<?php echo $this->html('post.featured', true, false); ?>
			<?php } ?>
		</div>

		<div class="eb-post-content">
			<div class="eb-post-head">
				<?php echo $this->html('post.admin', $post, $return); ?>

				<?php if ($params->get('post_title', true)) { ?>
					<?php echo $this->html('post.list.title', $post); ?>
				<?php } ?>

				<?php if ($params->get('post_author', true) || $params->get('post_category', true)) { ?>
					<?php echo $this->html('post.list.meta', $post, $params); ?>
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

				<?php echo $this->html('post.list.actions', $post, $params); ?>

				<?php if ($params->get('post_tags', true)) { ?>
					<?php echo $this->html('post.tags', $post->tags); ?>
				<?php } ?>

				<?php if ($post->copyrights && $params->get('post_copyrights', true)) { ?>
					<?php echo $this->html('post.copyrights', $post->copyrights); ?>
				<?php } ?>

				<?php if ($params->get('post_social_buttons', true)) { ?>
					<?php echo $this->html('post.socialShare', $post, 'listings'); ?>
				<?php } ?>
			<?php } ?>

			<?php if ($protected) { ?>
				<?php echo $this->html('post.protectedPost', $post); ?>
			<?php } ?>
		</div>

		<?php echo $this->html('post.list.schema', $post); ?>
	</div>
</div>
