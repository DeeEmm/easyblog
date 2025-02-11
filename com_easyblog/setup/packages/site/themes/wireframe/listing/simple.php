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
<div class="eb-post-listing__item" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?> data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
	<div class="eb-post-simple">
		<?php if ($params->get('post_image', true)) { ?>
			<?php echo $this->html('post.list.cover', $post, $params); ?>
		<?php } ?>

		<?php if ($params->get('post_title', true)) { ?>
		<div class="eb-post-simple__title">
			<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>
		</div>
		<?php } ?>

		<div class="eb-post-simple__meta eb-post-simple__meta--text">
			<?php if ($params->get('post_author_avatar', false)) { ?>
				<?php echo $this->html('avatar.user', $post->getAuthor(), 'sm'); ?>
			<?php } ?>

			<?php if ($params->get('post_author', true)) { ?>
			<div class="eb-post-simple__author">
				<a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName(); ?></a>
			</div>
			<?php } ?>

			<?php if ($params->get('post_category', true)) { ?>
			<div class="eb-post-simple__category">
				<a href="<?php echo $post->getPrimaryCategory()->getPermalink(); ?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>
			</div>
			<?php } ?>
		</div>

		<?php if ($params->get('show_intro', true)) { ?>
		<div class="eb-post-simple__body" data-blog-posts-item-content>
			<div class="eb-blog-grid__body">
				<?php if (!$protected) { ?>
					<?php if ($this->config->get('layout_dropcaps')) { ?>
					<p class="has-drop-cap">
					<?php } ?>

						<?php echo $post->getIntro(true, $params->get('post_content_limit', 350), 'intro', null, [
							'forceTruncateByChars' => true,
							'forceCharsLimit' => $params->get('post_content_limit', 350)
						]); ?>

					<?php if ($this->config->get('layout_dropcaps')) { ?>
					</p>
					<?php } ?>
				<?php } ?>

				<?php if ($protected) { ?>
					<?php echo $this->html('post.protectedPost', $post); ?>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<?php if (!$protected) { ?>
			<?php if ($params->get('post_readmore', false)) { ?>
			<div class="eb-post-more mt-20">
				<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
			</div>
			<?php } ?>

			<?php if ($params->get('post_date', true)) { ?>
			<div class="eb-post-simple__foot">
				<time class="eb-post-simple__meta-date">
					<?php echo $post->getDisplayDate($params->get('grid_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
				</time>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>