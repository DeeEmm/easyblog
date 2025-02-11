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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-fd-grid>
	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th class="title" style="text-align: left;" width="30%">
						<?php echo JText::_('COM_EASYBLOG_LINK'); ?>
					</th>
					<th class="center" width="20%">
						<?php echo JText::_('COM_EASYBLOG_ACTIONS'); ?>
					</th>
					<th class="title" style="text-align: center;" width="5%"><?php echo JText::_('COM_EASYBLOG_TYPE'); ?></th>
					<th class="title" style="text-align: center;" width="10%"><?php echo JText::_('COM_EASYBLOG_IP_ADDRESS'); ?></th>
					<th class="center" width="10%">
						<?php echo JText::_('COM_EASYBLOG_REPORTED_BY'); ?>
					</th>
					<th class="center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_REPORTED_DATE', 'a.created', $order, $orderDirection); ?>
					</th>
					<th class="center" width="1%">
						<?php echo JText::_('ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if( $reports ){ ?>
				<?php $i = 0; ?>
				<?php foreach ($reports as $report) { ?>
				<tr>
					<td style="text-align:center;">
						<?php echo $this->fd->html('table.id', $i++, $report->id); ?>
					</td>
					<td>
						<div class="mb-10">
							<a href="<?php echo JURI::root();?>index.php?option=com_easyblog&view=entry&id=<?php echo $report->obj_id;?>" target="_blank"><?php echo $report->blog->title;?></a>
						</div>
						<div>
							<?php echo $this->fd->html('str.truncate', $report->reason, 250);?>
						</div>
					</td>
					<td class="center">
						<a href="javascript:void(0);" class="btn btn-default" data-id="<?php echo $report->id;?>" data-unpublish-post>
							<?php echo JText::_('COM_EASYBLOG_UNPUBLISH_POST'); ?>
						</a>
						<a href="javascript:void(0);" class="btn btn-danger" data-id="<?php echo $report->id;?>" data-delete-post>
							<?php echo JText::_('COM_EASYBLOG_DELETE_POST'); ?>
						</a>
					</td>
					<td class="center">
						<?php echo $this->getType($report->obj_type); ?>
					</td>
					<td class="center">
						<?php if ($report->ip) { ?>
							<?php echo $report->ip; ?>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYBLOG_UNAVAILABLE'); ?>
						<?php } ?>
					</td>
					<td style="text-align:center;">
						<?php if ($report->created_by == 0) { ?>
							<?php echo JText::_('COM_EASYBLOG_GUEST'); ?>
						<?php } else { ?>
							<?php echo $report->getAuthor()->getName();?>
						<?php } ?>
					</td>
					<td class="center"><?php echo $report->created;?></td>
					<td class="center"><?php echo $report->id;?></td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="8" class="empty">
						<?php echo JText::_('COM_EASYBLOG_NO_REPORTS_YET');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="reports" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>
