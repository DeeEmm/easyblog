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
					<?php echo $this->output('site/blogs/entry/entry.unpublished'); ?>
				<?php } ?>

				<?php if ($preview) { ?>
					<?php echo $this->html('post.entry.preview', $post); ?>
				<?php } ?>

				<?php if ($hasEntryTools || $hasAdminTools || $preview || $post->isFeatured()) { ?>
				<div class="eb-entry-tools row-table">
					<?php if ($post->isFeatured()) { ?>
					<div class="col-cell">
						<?php echo $this->html('post.featured', true); ?>
					</div>
					<?php } ?>

					<?php if (!$preview) { ?>
					<div class="col-cell cell-tight">
						<?php echo $this->html('post.admin', $post, $post->getPermalink(false)); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php echo $this->renderModule('easyblog-before-entry'); ?>

				<div class="eb-entry-head">

					<?php if ($this->entryParams->get('post_date', true)) { ?>
					<div class="eb-post-date">
						<time class="eb-meta-date" datetime="<?php echo $post->getCreationDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
							<?php echo $post->getDisplayDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
						</time>
					</div>
					<?php } ?>


					<?php if ($this->entryParams->get('post_title', true)) { ?>
						<?php echo $this->html('post.entry.title', $post); ?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_author', true)) { ?>
					<div class="eb-horizonline">
						<div class="eb-horizonline-inner">
							<?php echo $this->html('avatar.user', $post->getAuthor(), 'sm'); ?>

							<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
						</div>
					</div>
					<?php } ?>

					<?php echo $post->event->afterDisplayTitle; ?>

					<div class="eb-entry-meta text-muted">
						<?php if ($hasEntryTools) { ?>
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
						<?php } ?>

						<?php if ($this->entryParams->get('show_reading_time')) { ?>
							<?php echo $this->html('post.entry.readingTime', $post); ?>
						<?php } ?>

						<?php if ($this->entryParams->get('post_category', true)) { ?>
							<div class="eb-meta-category comma-seperator">
								<i class="fdi far fa-folder-open"></i>
								<?php foreach ($post->categories as $cat) { ?>
								<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
								<?php } ?>
							</div>
						<?php } ?>

						<?php if ($this->entryParams->get('post_hits', true)) { ?>
						<div class="eb-meta-views">
							<i class="fdi fa fa-eye"></i>
							<?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
						</div>
						<?php } ?>

						<?php if ($this->config->get('main_comment') && $post->totalComments !== false && $this->entryParams->get('post_comment_counter', true) && $post->allowcomment) { ?>
						<div class="eb-meta-comments">
							<?php if ($this->config->get('comment_disqus')) { ?>
								<i class="fdi fa fa-comments"></i>
								<?php echo $post->totalComments; ?>
							<?php } else { ?>
								<i class="fdi fa fa-comments"></i>
								<a href="<?php echo JRequest::getURI();?>#comments"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->totalComments, true); ?></a>
							<?php } ?>
						</div>
						<?php } ?>

						<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
						<div class="eb-meta-team">
							<?php echo $this->html('avatar.team', $post->getBlogContribution(), 'sm'); ?>

							<span>
								<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="">
									<?php echo $post->getBlogContribution()->getTitle();?>
								</a>
							</span>
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="eb-entry-body type-<?php echo $post->posttype; ?> clearfix">
					<div class="eb-entry-article clearfix" data-blog-content>

						<?php echo $post->event->beforeDisplayContent; ?>

						<?php echo EB::renderModule('easyblog-before-content'); ?>

						<?php if (in_array($post->posttype, array('photo', 'standard', 'twitter', 'email', 'link', 'video'))) { ?>
							<?php echo $this->html('post.entry.cover', $post, $this->entryParams); ?>

							<?php if(!empty($post->toc)){ echo $post->toc; } ?>

							<!--LINK TYPE FOR ENTRY VIEW-->
							<?php if ($post->getType() == 'link') { ?>
								<div class="eb-post-headline">
									<div class="eb-post-headline-source">
										<a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
									</div>
								</div>
							<?php } ?>

							<?php echo $content; ?>

							<?php if (!$preview && $requireLogin) { ?>
								<?php echo $this->html('post.entry.restricted', $post); ?>
							<?php } ?>
						<?php } else { ?>
							<?php if(!empty($post->toc)){ echo $post->toc; } ?>
						<?php } ?>

						<?php echo $this->renderModule('easyblog-after-content'); ?>

						<?php if ($post->fields && $this->entryParams->get('post_fields', true)) { ?>
							<?php echo $this->html('post.fields', $post, $post->fields); ?>
						<?php } ?>
					</div>

					<?php if ($this->entryParams->get('post_social_buttons', true)) { ?>
						<?php echo EB::socialbuttons()->html($post, 'entry'); ?>
					<?php } ?>

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

					<?php echo $this->output('site/blogs/entry/subscription.form'); ?>

					<?php echo $this->output('site/blogs/entry/navigation'); ?>
				</div>
			</div>

			<?php if ($this->entryParams->get('post_author_box', true) && !$post->hasAuthorAlias()) { ?>
				<?php echo $this->html('post.entry.authorBox', $post, $this->entryParams); ?>
			<?php } ?>

			<?php if ($this->entryParams->get('post_related', true) && $relatedPosts) { ?>
				<?php echo $this->html('post.entry.related', $relatedPosts, [
					'showCover' => $this->entryParams->get('post_related_image', true)
				]); ?>
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

<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"mainEntityOfPage": "<?php echo $post->getPermalink(true, true); ?>",
		"@type": ["BlogPosting", "Organization"],
		"name": "<?php echo FH::getSiteName(); ?>",
		"headline": "<?php echo $this->fd->html('str.escape', $post->getTitle());?>",
		"image": "<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, true);?>",
		"editor": "<?php echo $post->getAuthor()->getName();?>",
		"genre": "<?php echo $post->getPrimaryCategory()->title;?>",
		"wordcount": "<?php echo $post->getTotalWords();?>",
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo FH::getSiteName(); ?>",
			"logo": <?php echo $post->getSchemaLogo(); ?>
		},
		"datePublished": "<?php echo $post->getPublishDate(true)->format('Y-m-d');?>",
		"dateCreated": "<?php echo $post->getCreationDate(true)->format('Y-m-d');?>",
		"dateModified": "<?php echo $post->getModifiedDate()->format('Y-m-d');?>",
		"description": "<?php echo EB::jconfig()->get('MetaDesc'); ?>",
		"articleBody": "<?php echo EB::normalizeSchema($schemaContent); ?>",
		"author": {
			"@type": "Person",
			"name": "<?php echo $post->getAuthor()->getName();?>",
			"image": "<?php echo $post->creator->getAvatar();?>"
		}<?php if (!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true) && $ratings->total > 0) { ?>,
			"aggregateRating": {
				"@type": "http://schema.org/AggregateRating",
				"ratingValue": "<?php echo round($ratings->ratings / 2, 2); ?>",
				"worstRating": "0.5",
				"bestRating": "5",
				"ratingCount": "<?php echo $ratings->total; ?>"
			}
		<?php } ?>
	}
</script>

<?php if ($prevId) { ?>
<hr class="eb-hr" />
<?php } ?>
