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
<div class="article" itemprop="blogPost" itemscope="" itemtype="https://schema.org/BlogPosting" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>

	<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
	<div class="eb-post-body type-<?php echo $post->posttype; ?>">
		<?php echo $this->html('post.list.cover', $post, $params); ?>
	</div>
	<?php } ?>

	<div class="article-body">
		<?php echo $this->html('post.admin', $post, $return); ?>

		<?php if ($params->get('post_title', true)) { ?>
		<div class="article-header">
			<h2>
				<a href="<?php echo $post->getPermalink();?>" class=""><?php echo nl2br($post->title);?></a>
			</h2>
		</div>
		<?php } ?>

		<div class="article-info">
			<?php if ($params->get('post_author', true)) { ?>
				<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
			<?php } ?>

			<?php if ($params->get('post_category', true) && $post->categories) { ?>
			<span class="category-name">
				<?php echo $this->html('post.category', $post->categories); ?>
			</span>
			<?php } ?>

			<?php if ($params->get('post_date', true)) { ?>
				<?php echo $this->html('post.date', $post, $params->get('post_date_source', 'created')); ?>
			<?php } ?>
		</div>

		<?php if (!$protected) { ?>
			<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
			<div class="article-introtext" data-blog-posts-item-content>
				<?php echo $post->getIntro();?>
			</div>
			<?php } ?>

			<?php if ($post->hasReadmore() && $params->get('post_readmore', true)) { ?>
				<div class="readmore">
					<a itemprop="url" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ($protected) { ?>
			<?php echo $this->html('post.protectedPost', $post); ?>
		<?php } ?>
	</div>

	<?php if (!$protected) { ?>
	<div>
		<div class="eb-post-actions">
			<?php if ($this->config->get('main_ratings') && $params->get('post_ratings', true)) { ?>
				<div class="eb-post-rating">
					<?php echo $this->output('site/ratings/frontpage', array('post' => $post, 'locked' => $this->config->get('main_ratings_frontpage_locked'))); ?>
				</div>
			<?php } ?>

			<?php if ($params->get('post_hits', true)) { ?>
				<div class="col-cell eb-post-hits">
					<i class="fdi fa fa-eye"></i>&nbsp;
					<?php if ($this->isMobile()) { ?>
						<?php echo $post->hits;?>
					<?php } else { ?>
						<?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
					<?php } ?>
				</div>
			<?php } ?>

			<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
				<div class="col-cell eb-post-comments">
					<a href="<?php echo $post->getCommentsPermalink();?>">
						<i class="far fa-comment"></i>&nbsp;

						<?php if ($this->isMobile()) { ?>
							<?php echo $post->getTotalComments();?>
						<?php } else { ?>
							<?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->getTotalComments(), true); ?>
						<?php } ?>
					</a>
				</div>
			<?php } ?>
		</div>

		<?php if ($params->get('post_tags', true)) { ?>
			<?php echo $this->html('post.tags', $post->tags); ?>
		<?php } ?>

		<?php if ($post->copyrights && $params->get('post_copyrights', true)) { ?>
			<div class="eb-entry-copyright">
				<h4 class="eb-section-title"><?php echo JText::_('COM_EASYBLOG_COPYRIGHT_HEADING');?></h4>
				<p>&copy; <?php echo $post->copyrights;?></p>
			</div>
		<?php } ?>

		<?php if ($params->get('post_social_buttons', true)) { ?>
			<?php echo EB::socialbuttons()->html($post, 'listings'); ?>
		<?php } ?>
	</div>
	<?php } ?>

	<?php echo $this->html('post.list.schema', $post); ?>
</div>
