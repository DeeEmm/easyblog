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
<form name="adminForm" id="adminForm" action="index.php" method="post" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.lists', 'filter_editor_state', [
			'' => 'Select Template Section',
			'base' => 'COM_EB_FOUNDRY',
			'templates' => 'COM_EASYBLOG'
		], $currentFilter, ['minWidth' => 280]); ?>
	</div>
	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_FILENAME'); ?>
					</th>
					<th width="40%" class="center">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_OVERRIDDEN_LOCATION'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_PREVIEW'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_MODIFIED'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($files) { ?>
					<?php $i = 0; ?>
					<?php foreach ($files as $file) { ?>
					<tr>
						<td width="1%" class="center">
							<?php echo $this->fd->html('table.id', $i, base64_encode($file->relative)); ?>
						</td>
						<td>
							<a href="index.php?option=com_easyblog&view=spools&layout=editfile&file=<?php echo urlencode($file->relative);?><?php echo $file->base ? '&base=1' : ''; ?>">
								<?php echo $file->name; ?>
							</a>
							<?php if ($file->base) { ?>
							&nbsp;
							<?php echo $this->fd->html('label.standard', 'COM_EB_FOUNDRY', 'gray'); ?>
							<?php } ?>
							<div class="t-mt--sm">
								<?php echo $file->desc;?>
							</div>
						</td>
						<td width="40%" class="center">
							<?php echo $file->override ? str_ireplace(JPATH_ROOT, '', $file->overridePath) : '&mdash;'; ?>
						</td>
						<td width="10%" class="center">
							<?php if ($file->relative == '/template.php' || $file->base) { ?>
								&mdash;
							<?php } else { ?>
								<?php echo $this->fd->html('button.link', null, '<i class="fdi fa fa-eye"></i>', 'default', 'sm', ['attributes' => 'data-eb-action data-mail-preview="' . urlencode($file->relative) . '"']); ?>
							<?php } ?>
						</td>
						<td width="10%" class="center">
							<?php echo $this->html('grid.published', $file, 'files', 'override', [], [], true); ?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="spools" />
	<input type="hidden" name="layout" value="editor" />
	<input type="hidden" name="controller" value="spools" />
</form>
