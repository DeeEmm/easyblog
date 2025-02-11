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

		<?php echo $this->fd->html('filter.lists', 'filter_type', $filterTypes, $type); ?>

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
					<th>
						<?php echo JText::_('COM_EASYBLOG_META_TITLE'); ?>
					</th>
					<th class="center" width="5%">
						<?php echo JText::_('COM_EASYBLOG_META_INDEXING'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TYPE');?>
					</th>
					<th width="1%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID');?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($metas) { ?>
					<?php $i = 0; ?>

					<?php foreach ($metas as $row) { ?>
					<tr>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i , $row->id, $row->type === 'view' ? true : false); ?>
						</td>

						<td>
							<a href="index.php?option=com_easyblog&view=metas&layout=form&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'meta', 'indexing', ['meta.addIndexing', 'meta.removeIndexing']); ?>
						</td>

						<td class="center">
							<?php echo ucfirst($row->type); ?>
						</td>

						<td class="center">
							<?php echo $row->id;?>
						</td>

					</tr>
						<?php $i++; ?>
					<?php }?>

				<?php } else { ?>
					<tr>
						<td colspan="5" class="text-center">
							<?php echo JText::_('COM_EASYBLOG_NO_META_TAGS_INDEXED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="metas" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
