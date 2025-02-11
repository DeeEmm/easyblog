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
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATUS'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_GLOBAL'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($templates) { ?>
					<?php $i = 0; ?>
					<?php foreach ($templates as $template) { ?>
					<tr>
						<td>
							<?php echo $this->fd->html('table.id', $i, $template->id); ?>
						</td>
						<td>
							<a href="<?php echo rtrim(JURI::root(), '/'); ?>/administrator/index.php?option=com_easyblog&view=blocks&layout=editTemplate&id=<?php echo $template->id;?>"><?php echo JText::_($template->title);?>
							</a>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $template, 'templates', 'published', array('blocks.publishTemplate', 'blocks.unpublishTemplate'), array()); ?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $template, 'templates', 'global', array('blocks.setGlobalTemplate', 'blocks.removeGlobalTemplate'), array(JText::_('COM_EASYBLOG_GRID_TOOLTIP_UNSET_AS_GLOBAL'), JText::_('COM_EASYBLOG_GRID_TOOLTIP_SET_AS_GLOBAL'))); ?>
						</td>
						<td class="center">
							<?php echo $template->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $template->id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="10" class="empty">
						<?php echo JText::_('COM_EB_BLOCK_TEMPLATES_EMPTY'); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="blocks" />
	<input type="hidden" name="layout" value="templates" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
<div id="toolbar-import" class="btn-wrapper hidden" data-toolbar-import>
	<span class="btn btn-primary"><?php echo JText::_('Import');?>
	</span>
</div>


