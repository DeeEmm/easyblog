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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>" class="<?php echo !$posts ? 'is-empty' : '';?>" data-eb-dashboard-moderate>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_MODERATE'),
		$this->fd->html('form.dropdown', 'moderateActions', '', [
				'' => 'COM_EASYBLOG_BULK_ACTIONS',
				'moderate.approve' => 'COM_EASYBLOG_APPROVE',
				'moderate.reject' => 'COM_EASYBLOG_REJECT'
			], ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_PENDING_POSTS', 'COM_EASYBLOG_DASHBOARD_PENDING_POSTS_HINT', [
		'icon' => 'fdi fa fa-align-left'
	]); ?>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="15%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_AUTHOR');?>
				</td>
				<td width="15%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id); ?>
				</td>
				<td>
					<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>" class="post-title"><?php echo $post->getTitle();?></a>

					<div class="post-meta">
						<span>
							<?php foreach ($post->categories as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fdi fa fa-language"></i>&nbsp; <?php echo $post->language;?>
						</span>
						<?php } ?>

						<ul class="post-actions" data-eb-actions data-id="<?php echo $post->getUid();?>">
							<li>
								<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>" data-eb-action target="_blank">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_REVIEW_POST'); ?>
								</a>
							</li>

							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmApproveBlog" data-type="dialog">
									<span class="text-success"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_APPROVE_POST'); ?></span>
								</a>
							</li>

							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmRejectBlog" data-type="dialog">
									<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_REJECT_POST'); ?></span>
								</a>
							</li>
						</ul>

						<?php if ($this->isMobile()) { ?>
						<div><?php echo $post->getCreationDate(true)->format(JText::_('Y-m-d H:i'));?></div>
						<?php } ?>
					</div>
				</td>
				<td class="text-center" width="15%">
					<?php echo $post->getAuthorName();?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo $post->getCreationDate()->format(JText::_('Y-m-d H:i'));?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="moderate" />

	<?php echo $this->fd->html('form.action'); ?>
</form>
