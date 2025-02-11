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
<div data-eb-post-section data-url="<?php echo $post->getExternalPermalink(); ?>" data-page-title="<?php echo $this->fd->html('str.escape', $post->getPagePostTitle()); ?>" data-permalink="<?php echo $post->getPermalink(); ?>" data-post-title="<?php echo $this->fd->html('str.escape', $post->getTitle()); ?>">
	<div class="eb-adsense-head clearfix">
		<?php echo $adsense->header;?>
	</div>

	<div data-blog-post>

		<?php if ($this->config->get('main_show_reading_progress')) { ?>
		<div class="eb-reading-progress-sticky hide" data-eb-spy="affix" data-offset-top="240">
			<progress value="0" max="100" class="eb-reading-progress" data-blog-reading-progress style="<?php echo 'top:' . $this->config->get('main_reading_progress_offset') . 'px'; ?>">
				<div class="eb-reading-progress__container">
					<span class="eb-reading-progress__bar"></span>
				</div>
			</progress>
		</div>
		<?php } ?>

		<div id="entry-<?php echo $post->id; ?>" class="article-details" data-blog-posts-item data-id="<?php echo $post->id;?>" data-uid="<?php echo $post->getUid();?>">

			<div data-blog-reading-container>
				<?php if (!$preview && $post->isPending() && $post->canModerate()) { ?>
					<?php echo $this->html('post.entry.moderate', $post); ?>
				<?php } ?>

				<?php if ($post->isUnpublished()) { ?>
					<?php echo $this->html('post.entry.unpublished', $post); ?>
				<?php } ?>

				<?php if ($preview) { ?>
					<?php echo $this->html('post.entry.preview', $post); ?>
				<?php } ?>

				<?php echo $this->html('post.entry.cover', $post, $this->entryParams); ?>

				<?php if ($hasAdminTools || $preview) { ?>
				<div class="eb-entry-tools row-table">
					<?php if (!$preview) { ?>
					<div class="col-cell cell-tight">
						<?php echo $this->html('post.admin', $post, $post->getPermalink(false)); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<div class="article-header">
					<?php if ($this->entryParams->get('post_title', true)) { ?>
						<?php echo $this->html('post.entry.title', $post); ?>
					<?php } ?>

					<?php echo $post->event->afterDisplayTitle; ?>
				</div>

				<div class="article-info">
					<?php if ($this->entryParams->get('post_author', true)) { ?>
						<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_category', true)) { ?>
						<span class="category-name" title="Category: Blog">
							<?php foreach ($post->categories as $cat) { ?>
							<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
							<?php } ?>
						</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_date', true)) { ?>
					<span class="published">
						<time class="" datetime="<?php echo $post->getCreationDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
							<?php echo $post->getDisplayDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
						</time>
					</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_hits', true)) { ?>
					<span class="hits">
						<span class="fdi fa fa-eye-o" aria-hidden="true"></span>
						<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $post->hits;?>"> <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
					</span>
					<?php } ?>

					<?php if ($this->config->get('main_comment') && $post->totalComments !== false && $this->entryParams->get('post_comment_counter', true) && $post->allowcomment) { ?>
					<span class="">
						<?php if ($this->config->get('comment_disqus')) { ?>
							<span><?php echo $post->totalComments; ?></span>
						<?php } else { ?>
							<span>
							<a href="<?php echo EBFactory::getURI(true);?>#comments"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->totalComments, true); ?></a>
							</span>
						<?php } ?>
					</span>
					<?php } ?>
				</div>

				<?php echo $this->html('post.entry.authors', $post, $this->entryParams); ?>

				<?php if ($this->entryParams->get('show_reading_time') || $post->isFeatured) { ?>
				<div class="eb-post-state">
					<?php if ($this->entryParams->get('show_reading_time')) { ?>
						<?php echo $this->html('post.entry.readingTime', $post); ?>
					<?php } ?>

					<?php if ($post->isFeatured) { ?>
					<div class="eb-post-state__item">
						<?php echo $this->html('post.featured', true); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ((!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) || $this->entryParams->get('post_social_buttons')) { ?>
				<div class="article-ratings-social-share d-flex justify-content-end">
					<?php if (!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) { ?>
					<div class="mr-auto align-self-center">
						<div class="article-ratings" >
							<div class="eb-entry-ratings">
								<?php echo $this->output('site/ratings/frontpage', array('post' => $post)); ?>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if ($this->entryParams->get('post_social_buttons', true)) { ?>
					<div>
						<div class="article-social-share">
							<div class="social-share-icon">
								<?php echo EB::socialbuttons()->html($post, 'entry'); ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>


				<?php echo $this->renderModule('easyblog-before-entry'); ?>


				<div itemprop="articleBody" class="eb-entry-body type-<?php echo $post->posttype; ?> clearfix">
					<div class="eb-entry-article clearfix" data-blog-content>

						<?php echo $post->event->beforeDisplayContent; ?>

						<?php echo EB::renderModule('easyblog-before-content'); ?>

						<?php echo $this->html('post.entry.content', $post, $content, [
								'showCover' => $this->entryParams->get('post_image', false),
								'showCoverPlaceholder' => $this->entryParams->get('post_image_placeholder', false),
								'requireLogin' => $requireLogin,
								'preview' => $preview
							]); ?>

						<?php echo $this->renderModule('easyblog-after-content'); ?>

						<?php if ($post->fields && $this->entryParams->get('post_fields', true)) { ?>
							<?php echo $this->html('post.fields', $post, $post->fields); ?>
						<?php } ?>
					</div>

					<?php if ($post->hasLocation() && $this->entryParams->get('post_location', true)) { ?>
						<?php echo $this->html('post.location', $post); ?>
					<?php } ?>

					<?php if ($post->copyrights && $this->entryParams->get('post_copyrights', true)) { ?>
						<?php echo $this->html('post.copyrights', $post->copyrights); ?>
					<?php } ?>

					<?php if ($this->config->get('reactions_enabled') && $this->entryParams->get('post_reactions', true)) { ?>
						<?php echo EB::reactions($post)->html();?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_tags', true)) { ?>
					<div class="eb-entry-tags">
						<?php echo $this->html('post.tags', $post->tags); ?>
					</div>
					<?php } ?>

					<?php if (!$preview) { ?>
						<?php echo EB::emotify()->html($post); ?>
					<?php } ?>


				</div>
				<hr />

				<?php if ($hasEntryTools || $preview) { ?>
				<div class="article-print-email mt-3">
					<?php if ($this->entryParams->get('post_font_resize', true)) { ?>
					<span>
						<?php echo $this->html('post.entry.fontsize', $post); ?>
					</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_reporting', true)) { ?>
					<span>
						<?php echo $this->html('post.entry.report', $post); ?>
					</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_print', true)) { ?>
					<span>
						<?php echo $this->html('post.entry.printer', $post); ?>
					</span>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($this->entryParams->get('post_subscribe_form', false) && !$preview) { ?>
					<?php echo $this->html('subscription.form', $this->my, EBLOG_SUBSCRIPTION_SITE); ?>
				<?php } ?>

				<?php if ($this->entryParams->get('post_navigation', true)) { ?>
					<?php echo $this->html('post.entry.navigation', $post, $navigation); ?>
				<?php } ?>
			</div>

			<?php if ($this->entryParams->get('post_author_box', true) && !$post->hasAuthorAlias()) { ?>
				<?php echo $this->html('post.entry.authorBox', $post, $this->entryParams); ?>
			<?php } ?>

			<?php if ($this->entryParams->get('post_related', true)) { ?>
				<?php echo $this->html('post.entry.related', $post); ?>
			<?php } ?>
		</div>

		<?php echo $adsense->beforecomments; ?>

		<?php echo $post->event->afterDisplayContent; ?>

		<?php if (!$preview && $this->config->get('main_comment') && $this->entryParams->get('post_comment_form', true)) { ?>
			<?php if ($post->allowComments() && $post->canEdit() && !$post->allowcomment) { ?>
				<div class="eb-comment-notice o-alert o-alert--warning mb-0">
					<?php echo JText::_('COM_EB_COMMENTS_LOCKED_BUT_VIEWED_BY_OWNER_ADMIN'); ?>
				</div>
			<?php } else if (!$post->allowComments()) { ?>
				<div class="eb-comment-notice o-alert o-alert--warning mb-0">
					<?php echo JText::_('COM_EB_COMMENTS_LOCKED'); ?>
				</div>
			<?php } ?>

			<a class="eb-anchor-link" name="comments" id="comments" data-allow-comment="<?php echo $post->allowcomment;?>">&nbsp;</a>
			<?php echo EB::comment()->html($post, [], '', ['isEntryView' => true]);?>
		<?php } ?>
	</div>

	<div class="eb-adsense-foot clearfix">
		<?php echo $adsense->footer;?>
	</div>
</div>

<?php echo $this->html('post.entry.schema', $post, $ratings, [
		'isPreview' => $preview,
		'showPostRatings' => $this->entryParams->get('post_ratings', true),
		'totalRatings' => $ratings->total
	]); ?>

<?php if ($previousPostId) { ?>
<hr class="eb-hr" />
<?php } ?>
