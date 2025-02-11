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
<form method="post" action="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=reports');?>" class="eb-dashboard-entries <?php echo !$reports ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EB_REPORT_POSTS'),
		$this->fd->html('form.dropdown', 'reportActions', '', [
			'' => 'COM_EASYBLOG_BULK_ACTIONS',
			'reports.trash' => 'COM_EASYBLOG_ADMIN_DELETE_ENTRY',
			'reports.discard' => 'COM_EB_DISCARD_BUTTON'
			], ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EB_EMPTY_REPORT_POSTS', 'COM_EB_EMPTY_REPORT_POSTS_HINT', [
		'icon' => 'fdi fa fa-exclamation-circle'
	]); ?>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EB_TABLE_COLUMN_REPORT_REASON');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_REPORTED_BY');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($reports as $report) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $report->id); ?>
				</td>

				<td>
					<div class="mb-10">
						<a href="<?php echo $report->getPermalink();?>" target="_blank"><?php echo $report->blog->title;?></a>
					</div>
					<div>
						<?php echo $this->fd->html('str.truncate', $report->reason, 250);?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $report->id;?>" data-eb-actions>
						<?php if ($report->blog->published) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmUnpublishPost" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></a>
							</li>
						<?php } ?>

						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmDeletePost" data-type="dialog" class="text-danger">
								<?php echo JText::_('COM_EASYBLOG_DELETE');?>
							</a>
						</li>
					</ul>
				</td>

				<td class="text-center" width="15%">
					<?php if ($report->created_by == 0) { ?>
						<?php echo JText::_('COM_EASYBLOG_GUEST'); ?>
					<?php } else { ?>
						<?php echo $report->getAuthor()->getName();?>
					<?php } ?>
				</td>

				<td class="text-center narrow-hide" width="15%">
					<?php echo $this->fd->html('str.date', $report->created, 'DATE_FORMAT_LC4'); ?>
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

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="sort" value="" />
	<input type="hidden" name="ordering" value="" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="reports" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
