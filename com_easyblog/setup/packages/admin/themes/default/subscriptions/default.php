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

		<?php echo $this->fd->html('filter.lists', 'filter', [
			EBLOG_SUBSCRIPTION_BLOGGER => 'COM_EASYBLOG_BLOGGER_OPTION',
			EBLOG_SUBSCRIPTION_ENTRY => 'COM_EASYBLOG_BLOG_POST_OPTION',
			EBLOG_SUBSCRIPTION_CATEGORY => 'COM_EASYBLOG_CATEGORY_OPTION',
			EBLOG_SUBSCRIPTION_SITE => 'COM_EASYBLOG_SITE_OPTION',
			EBLOG_SUBSCRIPTION_TEAMBLOG => 'COM_EASYBLOG_TEAM_OPTION'
		], $currentFilter); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-table-grid>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TYPE'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_USER'); ?>
					</th>
					<th width="15%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE'); ?>
					</th>
					<th width="1%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if( $subscriptions ){ ?>
				<?php $i = 0; ?>
				<?php foreach ($subscriptions as $row) { ?>
				<tr>
					<td class="center">
						<?php echo $this->fd->html('table.id', $i++, $row->id); ?>
					</td>
					<td>
						<?php if ($currentFilter == 'site') { ?>
							<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_ENTIRE_SITE'); ?>
						<?php } else { ?>
							<?php echo $row->bname;?><?php echo ($currentFilter == 'blogger') ? ' (' . $row->busername. ')' : ''; ?>
						<?php } ?>
					</td>

					<td>
						<a href="index.php?option=com_easyblog&view=subscriptions&layout=form&id=<?php echo $row->id;?>"><?php echo $row->email;?></a>
						(<?php echo $row->fullname;?>)
					</td>

					<td class="center">
						<?php echo $row->created; ?>
					</td>

					<td class="center">
						<?php echo $row->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_NO_SUBSCRIPTION_FOUND');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="subscriptions" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ''); ?>
</form>
