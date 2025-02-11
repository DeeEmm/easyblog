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
<div>
	<article class="uk-article uk-margin-medium-bottom" typeof="Article" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
		<meta class="uk-margin-remove-adjacentx">

		<?php echo $this->html('post.list.cover', $post, $params); ?>

		<?php if ($params->get('post_title', true)) { ?>
		<h2 class="uk-margin-medium-top uk-margin-remove-bottom uk-article-title">
			<a class="uk-link-reset" href="<?php echo $post->getPermalink();?>"><?php echo nl2br($post->title);?></a>
		</h2>
		<?php } ?>

		<div class="uk-grid uk-margin-top">
			<?php if ($params->get('post_type', false)
					|| $params->get('post_author', true)
					|| $params->get('post_date', true)
					|| $params->get('post_category', true)
					|| $params->get('post_hits', true)
					|| ($post->getTotalComments() !== false && $params->get('post_comment_counter', true))
					|| ($this->config->get('main_ratings') && $params->get('post_ratings', true))) {
			?>
				<div class="uk-width-expand@m">
					<ul class="uk-margin-remove-bottom uk-subnav uk-subnav-divider">
						<?php if ($params->get('post_type', false)) { ?>
						<li>
							<?php echo $this->html('post.icon', $post->getType()); ?>
						</li>
						<?php } ?>

						<?php if ($params->get('post_author', true)) { ?>
						<li>
							<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
						</li>
						<?php } ?>

						<?php if ($params->get('post_date', true)) { ?>
						<li>
							<?php echo $this->html('post.date', $post, $params->get('post_date_source', 'created')); ?>
						</li>
						<?php } ?>

						<?php if ($params->get('post_category', true) && $post->categories) { ?>
							<?php echo $this->html('post.category', $post->categories); ?>
						<?php } ?>

						<?php if ($params->get('post_hits', true)) { ?>
						<li>
							<?php echo $this->html('post.hits', $post); ?>
						</li>
						<?php } ?>

						<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
						<li>
							<?php echo $this->html('post.comments', $post->getTotalComments(), $post->getCommentsPermalink(), false); ?>
						</li>
						<?php } ?>

						<?php if ($this->config->get('main_ratings') && $params->get('post_ratings', true)) { ?>
						<li>
							<div class="eb-post-rating">
								<?php echo $this->output('site/ratings/frontpage', array('post' => $post, 'locked' => $this->config->get('main_ratings_frontpage_locked'))); ?>
							</div>
						</li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>

			<div class="uk-width-auto@m">
				<?php echo $this->html('post.admin', $post, $return); ?>
			</div>
		</div>

		<?php if (!$protected) { ?>
			<div class="uk-margin-small-top" property="text" data-blog-posts-item-content>
				<?php echo $this->html('post.list.content', $post, $params, false); ?>
			</div>

			<?php if ($post->fields && $params->get('post_fields', true)) { ?>
				<?php echo $this->html('post.fields', $post, $post->fields); ?>
			<?php } ?>

			<?php if ($params->get('post_tags', true)) { ?>
				<?php echo $this->html('post.tags', $post->tags); ?>
			<?php } ?>

			<?php if ($post->copyrights && $params->get('post_copyrights', true)) { ?>
				<div class="eb-entry-copyright">
					<?php echo JText::_('COM_EASYBLOG_COPYRIGHT_HEADING');?>: &copy; <?php echo $post->copyrights;?>
				</div>
			<?php } ?>

			<?php if ($params->get('post_social_buttons', true)) { ?>
				<?php echo EB::socialbuttons()->html($post, 'listings'); ?>
			<?php } ?>

			<?php if ($post->hasReadmore() && $params->get('post_readmore', true)) { ?>
			<p class="uk-margin-medium">
				<?php echo $this->html('post.list.readmore', $post); ?>
			</p>
			<?php } ?>
		<?php } ?>

		<?php if ($protected) { ?>
		<div class="uk-margin-small-top" property="text" data-blog-posts-item-content>
			<?php echo $this->html('post.protectedPost', $post); ?>
		</div>
		<?php } ?>

	</article>
</div>
