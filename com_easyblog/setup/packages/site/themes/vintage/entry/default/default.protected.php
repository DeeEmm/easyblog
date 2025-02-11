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
<div data-eb-post-section data-url="<?php echo $post->getExternalPermalink(); ?>">
	<div itemscope itemtype="http://schema.org/BlogPosting">
		<div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf" data-id="<?php echo $post->id;?>">

			<?php if (!$preview || $post->isFeatured()) { ?>
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
						<div class="eb-post-author-avatar single">
							<a href="<?php echo $post->getAuthorPermalink();?>" class="eb-avatar">
								<img src="<?php echo $post->creator->getAvatar();?>" alt="Super User" width="22" height="22">
							</a>
						</div>

						<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
					</div>
				</div>
				<?php } ?>

				<div class="eb-entry-meta text-muted">
					<?php if ($hasEntryTools) { ?>
						<?php if ($this->entryParams->get('post_font_resize', true)) { ?>
						<div>
							<?php echo $this->html('post.entry.fontsize', $post); ?>
						</div>
						<?php } ?>

						<?php if ($this->entryParams->get('post_reporting', true)) { ?>
							<?php echo $this->html('post.entry.report', $post); ?>
						<?php } ?>

						<?php if ($this->entryParams->get('post_print', true)) { ?>
						<div>
							<?php echo $this->html('post.entry.printer', $post); ?>
						</div>
						<?php } ?>
					<?php } ?>

					<?php echo $this->html('post.entry.meta', $post, $this->entryParams); ?>

					<?php echo $this->html('post.entry.authors', $post, $this->entryParams); ?>
				</div>
			</div>

			<div class="eb-entry-body clearfix">
				<div class="eb-entry-article clearfix" itemprop="articleBody">
					<?php echo $this->html('post.protectedPost', $post); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ($previousPostId) { ?>
<hr class="eb-hr" />
<?php } ?>
