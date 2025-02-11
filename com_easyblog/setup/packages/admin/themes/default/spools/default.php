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

	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search); ?>

		<?php echo $this->fd->html('filter.lists', 'filter_state', [
			'' => 'COM_EASYBLOG_SELECT_STATE',
			'P' => 'COM_EASYBLOG_SENT',
			'U' => 'COM_EASYBLOG_PENDING'
		], $currentFilter); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<?php if ($cronLastExecuted) { ?>
		<div class="t-mt--lg">
			<?php echo $this->fd->html('alert.standard', JText::sprintf('COM_EB_CRON_LAST_EXECUTED', $cronLastExecuted), 'success', [
				'icon' => 'fdi fa fa-check-circle'
			]); ?>
		</div>
		<?php } ?>

		<?php if (!$cronLastExecuted) { ?>
		<div class="t-mt--lg">
			<?php echo $this->fd->html('alert.standard', 'COM_EASYBLOG_SPOOLS_TIPS', 'warning', [
				'icon' => 'fdi fa fa-exclamation-circle',
				'button' => $this->fd->html('button.link', 'https://stackideas.com/docs/easyblog/administrators/cronjobs', 'COM_EASYBLOG_SETUP_CRON', 'default', 'default', [
					'icon' => 'fdi fa fa-external-link-alt',
					'class' => 't-ml--lg'
				])
			]); ?>
		</div>
		<?php } ?>

		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>

					<th><?php echo JText::_('COM_EASYBLOG_SUBJECT'); ?></th>

					<th width="30%">
						<?php echo JText::_('COM_EASYBLOG_RECIPIENT'); ?>
					</th>

					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_STATE'); ?>
					</th>

					<th width="20%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_CREATED'); ?>
					</th>

					<th width="1%" class="center"><?php echo JText::_('COM_EASYBLOG_ID'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($mails) { ?>
					<?php $i = 0; ?>
					<?php foreach ($mails as $row) {?>
					<tr>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i++, $row->id); ?>
						</td>
						<td>
							<a href="javascript:void(0);" data-mailer-preview data-id="<?php echo $row->id;?>"><?php echo JText::_($row->subject);?></a>
						</td>
						<td>
							<?php echo $row->recipient;?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $row, 'spools', 'status'); ?>
						</td>
						<td class="center">
							<?php echo $this->fd->html('str.date', $row->created, 'DATE_FORMAT_LC2', true); ?>
						</td>
						<td class="center">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7" align="center" class="empty">
							<?php echo JText::_('COM_EASYBLOG_NO_MAILS');?>
						</td>
					</tr>
				<?php } ?>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="spools" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
