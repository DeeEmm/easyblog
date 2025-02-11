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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-fd-grid>

	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search); ?>

		<?php echo $this->fd->html('filter.published', 'filter_state', $filterState, ['selectText' => 'COM_EASYBLOG_GRID_SELECT_STATE']); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-table-grid>
			<thead>
				<?php if (!$browse) { ?>
					<th width="5">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
				<?php } ?>

				<th style="text-align: left;">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</th>
				<?php if (!$browse) { ?>
					<th width="15%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
					</th>
					<th width="15%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TOTAL_FIELDS'); ?>
					</th>
				<?php } ?>
				<th width="1%" class="center">
					<?php echo JText::_('COM_EASYBLOG_ID');?>
				</th>
			</thead>
			<tbody>
				<?php if ($groups) { ?>
					<?php $i = 0; ?>
					<?php foreach ($groups as $group) { ?>
					<tr>
						<?php if (!$browse) { ?>
							<td width="1%" class="center nowrap">
								<?php echo $this->fd->html('table.id', $i, $group->id);?>
							</td>
						<?php } ?>

						<td>
							<?php if ($browse) { ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $group->id;?>','<?php echo addslashes($this->escape($group->title));?>');"><?php echo $group->title;?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easyblog&view=fields&layout=groupForm&id=<?php echo $group->id;?>"><?php echo $group->title;?></a>
							<?php } ?>
						</td>

						<?php if (!$browse) { ?>
							<td class="center nowrap">
								<?php echo $this->html('grid.published', $group, 'fields', 'state', array('fields.publishgroup', 'fields.unpublishgroup')); ?>
							</td>

							<td class="center">
								<?php echo $group->getTotalFields(); ?>
							</td>
						<?php } ?>

						<td class="center">
							<?php echo $group->id; ?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="5" class="empty">
							<?php echo JText::_('COM_EASYBLOG_FIELDS_NO_FIELDGROUPS_CREATED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if ($browse) { ?>
		<input type="hidden" name="tmpl" value="component" />
	<?php } ?>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="browsefunction" value="<?php echo $browsefunction;?>" />
	<input type="hidden" name="view" value="fields" />
	<input type="hidden" name="layout" value="groups" />
</form>
