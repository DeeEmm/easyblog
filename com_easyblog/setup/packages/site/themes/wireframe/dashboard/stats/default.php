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
<?php echo $this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_OVERVIEW', [
	'icon' => 'fdi fa fa-pencil-alt',
	'text' => 'COM_EASYBLOG_NEW_POST',
	'link' => EB::composer()->getComposeUrl(),
	'style' => 'primary'
]); ?>

<div class="l-stack l-spaces--lg">
	<div class="grid grid-cols-1 md:grid-cols-4 gap-sm">
		<?php echo $this->html('dashboard.stats', 'COM_EB_POSTS', $totalPosts, 'fdi far fa-file-alt', EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries')); ?>

		<?php echo $this->html('dashboard.stats', 'COM_EB_DASHBOARD_TOTAL_HITS', $totalHits, 'fdi fa fa-chart-line', null); ?>

		<?php echo $this->html('dashboard.stats', 'COM_EASYBLOG_CATEGORIES_PAGE_TITLE', $totalCategories, 'fdi far fa-folder-open', EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories')); ?>

		<?php if ($this->acl->get('manage_pending')) { ?>
			<?php echo $this->html('dashboard.stats', 'Pending', $pending, 'fdi far fa-file', EBR::_('index.php?option=com_easyblog&view=dashboard&layout=moderate')); ?>
		<?php } ?>
	</div>

	<div class="t-font-weight--bold l-spaces--lg">
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_PAGE_HEADING');?>
	</div>

	<?php echo $this->fd->html('tabs.render', [
		$this->fd->html('tabs.item', 'recent-posts', 'COM_EB_RECENT', function() use ($latest) {
		?>
			<div class="eb-stats-listing">
				<?php if ($latest) { ?>
					<?php foreach ($latest as $post) { ?>
						<?php echo $this->output('site/dashboard/stats/post.item', ['post' => $post]); ?>
					<?php } ?>
				<?php } ?>

				<?php if (!$latest) { ?>
				<div class="text-small text-muted">
					<?php echo JText::_('COM_EASYBLOG_STATS_NO_POST_CREATED_YET');?>
				</div>
				<?php } ?>
			</div>
		<?php

		}, true),

		$this->fd->html('tabs.item', 'top-posts', 'COM_EASYBLOG_DASHBOARD_TOP_POSTS', function() use ($posts) {
		?>
			<div class="eb-stats-listing">
			<?php if ($posts) { ?>
				<?php foreach ($posts as $post) { ?>
					<?php echo $this->output('site/dashboard/stats/post.item', ['post' => $post]); ?>
				<?php } ?>
			<?php } ?>

			<?php if (!$posts) { ?>
				<div class="text-small text-muted">
					<?php echo JText::_('COM_EASYBLOG_STATS_NO_POST_CREATED_YET');?>
				</div>
			<?php } ?>
			</div>
		<?php
		}),
	], 'line', 'horizontal', ['tabContentClass' => 'p-sm']); ?>


	<?php if ($this->config->get('comment_easyblog')) { ?>
	<div class="t-font-weight--bold l-spaces--lg">
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_PAGE_HEADING_COMMENTS');?>
	</div>

	<?php echo $this->fd->html('tabs.render', [
		$this->fd->html('tabs.item', 'recent-comments', 'COM_EB_RECENT', function() use ($recentComments) {
		?>
			<div class="eb-stats-listing">
				<?php if ($recentComments) { ?>
					<?php foreach ($recentComments as $comment) { ?>
						<?php echo $this->output('site/dashboard/stats/comment.item', ['comment' => $comment]); ?>
					<?php } ?>
				<?php } ?>

				<?php if (!$recentComments) { ?>
				<div class="text-small text-muted">
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATS_NO_COMMENTS_POSTED_YET');?>
				</div>
				<?php } ?>
			</div>
		<?php

		}, true),

		$this->fd->html('tabs.item', 'most-commented', 'COM_EASYBLOG_DASHBOARD_STATISTICS_MOST_COMMENTED_POSTS', function() use ($mostCommentedPosts) {
		?>
			<div class="eb-stats-listing">
			<?php if ($mostCommentedPosts) { ?>
				<?php foreach ($mostCommentedPosts as $post) { ?>
					<?php echo $this->output('site/dashboard/stats/post.item.comment', ['post' => $post]); ?>
				<?php } ?>
			<?php } ?>
			</div>
		<?php
		}),
	], 'line', 'horizontal', ['tabContentClass' => 'p-sm']); ?>
	<?php } ?>
</div>
