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
			'all' => 'COM_EASYBLOG_SELECT_STATE',
			'published' => 'COM_EASYBLOG_PUBLISHED',
			'unpublished' => 'COM_EASYBLOG_UNPUBLISHED'
		], $currentFilter); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>

					<th><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?></th>

					<th width="5%" class="center"><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE'); ?></th>
					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_COLUMN_CREATED', 'created', $order, $orderDirection); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_COLUMN_AUTHOR', 'user_id', $order, $orderDirection); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_POLLS_CHOICES'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($polls) { ?>
					<?php $i = 0; ?>
					<?php foreach ($polls as $poll) { ?>
						<tr>
							<td width="1%" class="center nowrap">
								<?php echo $this->fd->html('table.id', $i++, $poll->id, true, false); ?>
							</td>
							<td>
								<a href="javascript:void(0);" data-eb-action data-eb-poll-item data-id="<?php echo $poll->id; ?>">
									<?php echo $this->escape($poll->title); ?>
								</a>
							</td>
							<td width="5%" class="center">
								<?php echo $this->html('grid.published', $poll, 'polls', 'state'); ?>
							</td>
							<td width="10%" class="center">
								<?php echo EB::date($poll->created, true)->format();?>
							</td>
							<td width="10%" class="center">
								<span>
									<?php echo EB::user($poll->user_id)->getName(); ?>
								</span>
							</td>
							<td width="10%" class="center"><?php echo $poll->getTotalItems(); ?></td>
							<td width="5%" class="center"><?php echo $poll->id; ?></td>
						</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="8" align="center" class="empty">
						<?php echo JText::_('COM_EB_POLLS_EMPTY_MESSAGE');?>
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
	<input type="hidden" name="view" value="polls" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
