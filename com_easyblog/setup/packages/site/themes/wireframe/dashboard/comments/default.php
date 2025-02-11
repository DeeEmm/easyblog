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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>" class="eb-dashboard-comments <?php echo !$comments ? 'is-empty' : '';?>" data-eb-dashboard-comments>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_COMMENTS'),
		$this->fd->html('form.dropdown', 'categoryActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS'
				];

				if ($this->acl->get('manage_comment')) {
					$options['comments.publish'] = 'COM_EASYBLOG_PUBLISH';
					$options['comments.unpublish'] = 'COM_EASYBLOG_UNPUBLISH';
				}

				if ($this->acl->get('delete_comment')) {
					$options['comments.delete'] = (object) [
						'title' => 'COM_EASYBLOG_DELETE',
						'attr' => 'data-confirmation="site/views/dashboard/confirmDeleteComment"'
					];
				}

				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'search', $search),
		[
			$this->fd->html('form.dropdown', 'filter', $filter, [
					'all' => 'COM_EASYBLOG_FILTER_SELECT_FILTER',
					'published' => 'COM_EASYBLOG_FILTER_PUBLISHED',
					'unpublished' => 'COM_EASYBLOG_FILTER_UNPUBLISHED',
					'moderate' => 'COM_EASYBLOG_FILTER_PENDING'
				], ['attr' => 'data-eb-filter-dropdown']
			)
		]
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_COMMENTS_EMPTY', 'COM_EASYBLOG_DASHBOARD_COMMENTS_EMPTY_HINT', [
		'icon' => 'fdi fa fa-comments'
	]); ?>


	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_COMMENT');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_AUTHOR');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>

		<?php if ($comments) { ?>
		<tbody>
			<?php foreach ($comments as $comment) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $comment->id); ?>
				</td>
				<td>
					<div>
						<?php echo $comment->getContent();?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $comment->id;?>" data-eb-actions>
						<?php if ($this->acl->get('manage_comment') && $comment->isPublished()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmUnpublishComment" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('manage_comment') && $comment->isUnpublished()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmPublishComment" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></a>
						</li>
						<?php } ?>

						<?php if ($comment->isPublished()) { ?>
						<li>
							<a href="<?php echo $comment->getPermalink();?>" data-eb-action target="_blank"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_VIEW_COMMENT');?></a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('delete_comment') ) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmDeleteComment" data-type="dialog" class="text-danger">
								<?php echo JText::_('COM_EASYBLOG_DELETE');?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center" width="15%">
					<?php if ($comment->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($comment->isModerated()) { ?>
						<span class="text-info"><?php echo JText::_('COM_EASYBLOG_STATE_PENDING'); ?></span>
					<?php } ?>

					<?php if (!$comment->isPublished() && !$comment->isModerated()) { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center" width="15%">
					<?php echo $comment->getAuthorName();?>
				</td>

				<td class="text-center narrow-hide" width="15%">
					<?php echo $this->fd->html('str.date', $comment->created, 'DATE_FORMAT_LC4'); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php } ?>
	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="comments" />

	<?php echo $this->fd->html('form.action', ''); ?>
</form>
