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

		<?php echo $this->fd->html('filter.lists', 'filter_group', $groups, $filterGroup, [
			'initial' => 'COM_EASYBLOG_BLOCKS_FILTER_GROUPS',
			'initialValue' => ''
		]); ?>

		<?php echo $this->fd->html('filter.published', 'filter_state', $filterState, ['selectText' => 'COM_EASYBLOG_GRID_SELECT_STATE']); ?>

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
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE'); ?>
					</th>
					<th width="10%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE'); ?>
					</th>
					<th class="center" width="20%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_GROUP'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($blocks) { ?>
					<?php $i = 0; ?>
					<?php foreach ($blocks as $block) { ?>
					<tr>
						<td>
							<?php echo $this->fd->html('table.id', $i, $block->id); ?>
						</td>

						<td align="left">
							<a href="index.php?option=com_easyblog&view=blocks&layout=form&id=<?php echo $block->id;?>"><?php echo $block->title;?></a>
						</td>
						<td class="center">
							<?php if ($block->published == 2) { ?>
								<a class="eb-state-scheduled badge" href="javascript:void(0);"
									data-eb-provide="tooltip"
									data-original-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_CORE_BLOCK');?>"
									data-placement="bottom"
									disabled="disabled"
								>
									<i class="fdi fa fa-check"></i>
								</a>
							<?php } else { ?>
								<?php echo $this->html('grid.published', $block, 'blocks', 'published'); ?>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo ucfirst($block->group);?>
						</td>
						<td align="center">
							<?php echo $block->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="6" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_NO_BLOCKS_INSTALLED');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="blocks" />
</form>