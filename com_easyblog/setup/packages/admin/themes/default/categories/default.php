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

$originalOrders = [];
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-fd-grid>

	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search); ?>

		<?php if (!$browse) { ?>
		<?php echo $this->fd->html('filter.published', 'filter_state', $filterState, ['selectText' => 'COM_EASYBLOG_GRID_SELECT_STATE']); ?>
		<?php } ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<?php if (!$browse) { ?>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<?php } ?>

					<th>
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_CATEGORIES_CATEGORY_TITLE', 'title', $order, $orderDirection); ?>
					</th>

					<?php if (!$browse) { ?>
					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_DEFAULT'); ?>
					</th>

					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PUBLISHED'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_CHILD_COUNT'); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_LANGUAGE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'Order', 'lft', $order, $orderDirection, ['class' => 'mr-10']); ?>

						<?php echo $this->fd->html('table.saveOrder', 'category.saveorder'); ?>
					</th>

					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_CATEGORIES_AUTHOR', 'created_by', $order, $orderDirection); ?>
					</th>
					<?php } ?>

					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $categories ){ ?>
					<?php $i = 0; ?>
					<?php foreach ($categories as $row) { ?>
					<tr>
						<?php if (!$browse) { ?>
						<td>
							<?php echo $this->fd->html('table.id', $i, $row->id); ?>
						</td>
						<?php } ?>

						<td align="left">
							<?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
							<span class="editlinktip hasTip">
							<?php if( $browse ){ ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');"><?php echo $row->title;?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easyblog&view=categories&layout=form&id=<?php echo $row->id;?>"><?php echo JText::_($row->title); ?></a>
							<?php } ?>
							</span>
						</td>

						<?php if (!$browse) { ?>
						<td class="center">
							<?php echo $this->html('grid.featured', $row, 'category', 'default', 'category.makeDefault'); ?>
						</td>

						<td class="center">
							<?php echo $this->html('grid.published', $row, 'category', 'published'); ?>
						</td>

						<td class="center">
							<?php echo $this->fd->html('label.standard', $row->count === 0 ? "0" : $row->count, 'gray'); ?>
						</td>

						<td class="center">
							<?php echo $this->fd->html('label.standard', $row->child_count === 0 ? "0" : $row->child_count, 'gray'); ?>
						</td>

						<td class="center">
							<?php if (!$row->language || $row->language == '*') { ?>
								<?php echo JText::_('COM_EASYBLOG_LANGUAGE_ALL');?>
							<?php } else { ?>
								<?php echo $row->language;?>
							<?php } ?>
						</td>

						<td class="order center">
							<?php $orderkey = array_search($row->id, $ordering[$row->parent_id]); ?>
							<?php $originalOrders[] = $orderkey + 1; ?>

							<?php echo $this->fd->html('table.order', $pagination, 'order', $orderkey, $saveOrder, [
									'rowIndex' => $i,
									'showOrderUpIcon' => isset($ordering[$row->parent_id][$orderkey - 1]),
									'showOrderDownIcon' => isset($ordering[$row->parent_id][$orderkey + 1]),
									'orderUpTask' => 'category.orderup',
									'orderDownTask' => 'category.orderdown',
									'accessControl' => $ordering,
									'total' => $pagination->total
								]); ?>
						</td>

						<td class="center">
							<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser( $row->created_by )->name; ?></a>
						</td>
						<?php } ?>

						<td align="center">
							<?php echo $row->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="12" align="center">
						<?php echo JText::_('COM_EASYBLOG_NO_CATEGORY_CREATED_YET');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>

	<?php if ($browse) { ?>
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browseFunction" value="<?php echo $browsefunction;?>" />
	<?php } ?>
	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="view" value="categories" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
	<input type="hidden" name="original_order_values" value="<?php echo implode(',', $originalOrders); ?>" />
</form>
