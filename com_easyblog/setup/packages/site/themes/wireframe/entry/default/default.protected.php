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
		<div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf">

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

			<?php echo EB::renderModule('easyblog-before-entry'); ?>

			<div class="eb-entry-head">
				<?php if ($this->entryParams->get('post_title', true)) { ?>
					<?php echo $this->html('post.entry.title', $post); ?>
				<?php } ?>

				<?php echo $this->html('post.entry.meta', $post, $this->entryParams); ?>

				<?php echo $this->html('post.entry.authors', $post, $this->entryParams); ?>
			</div>

			<div class="eb-entry-body clearfix">
				<div class="eb-entry-article clearfix">
					<?php echo $this->html('post.protectedPost', $post); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ($previousPostId) { ?>
<hr class="eb-hr" />
<?php } ?>
