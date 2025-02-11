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
			<?php echo $this->html('post.entry.progress', $post); ?>
		<?php } ?>

		<div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf" data-blog-posts-item data-id="<?php echo $post->id;?>" data-uid="<?php echo $post->getUid();?>">

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

				<?php if ($hasEntryTools || $hasAdminTools || $preview) { ?>
				<div class="eb-entry-tools row-table">
					<div class="col-cell">
						<div class="eb-entry-helper">
							<?php if ($this->entryParams->get('post_font_resize', true)) { ?>
								<?php echo $this->html('post.entry.fontsize', $post); ?>
							<?php } ?>

							<?php if ($this->entryParams->get('post_reporting', true)) { ?>
								<?php echo $this->html('post.entry.report', $post); ?>
							<?php } ?>

							<?php if ($this->entryParams->get('post_print', true)) { ?>
								<?php echo $this->html('post.entry.printer', $post); ?>
							<?php } ?>
						</div>
					</div>

					<?php if (!$preview) { ?>
					<div class="col-cell cell-tight">
						<?php echo $this->html('post.admin', $post, $post->getPermalink(false)); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php echo $this->renderModule('easyblog-before-entry'); ?>

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

				<div class="eb-entry-head">
					<?php if ($this->entryParams->get('post_title', true)) { ?>
						<?php echo $this->html('post.entry.title', $post); ?>
					<?php } ?>

					<?php echo $post->event->afterDisplayTitle; ?>

					<?php echo $this->html('post.entry.meta', $post, $this->entryParams); ?>

					<?php echo $this->html('post.entry.authors', $post, $this->entryParams); ?>
				</div>

				<div class="eb-entry-body type-<?php echo $post->posttype; ?> clearfix">
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

					<?php if (!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) { ?>
					<div class="eb-entry-ratings">
						<?php echo $this->html('post.ratings', $post); ?>
					</div>
					<?php } ?>

					<?php if ($this->entryParams->get('post_social_buttons', true)) { ?>
					<div class="mb-20">
						<?php echo $this->html('post.socialShare', $post, 'entry'); ?>
					</div>
					<?php } ?>

					<?php if ($this->config->get('reactions_enabled') && $this->entryParams->get('post_reactions', true)) { ?>
					<div class="eb-entry-reactions">
						<?php echo $this->html('post.reactions', $post); ?>
					</div>
					<?php } ?>

					<?php if ($this->entryParams->get('post_tags', true)) { ?>
					<div class="eb-entry-tags">
						<?php echo $this->html('post.tags', $post->tags); ?>
					</div>
					<?php } ?>

					<?php if (!$preview) { ?>
						<?php echo EB::emotify()->html($post); ?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_subscribe_form', false) && !$preview) { ?>
						<?php echo $this->html('subscription.form', $this->my, EBLOG_SUBSCRIPTION_SITE); ?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_navigation', true)) { ?>
						<?php echo $this->html('post.entry.navigation', $post, $navigation); ?>
					<?php } ?>
				</div>
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
			<?php echo $this->html('post.entry.comments', $post); ?>
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
