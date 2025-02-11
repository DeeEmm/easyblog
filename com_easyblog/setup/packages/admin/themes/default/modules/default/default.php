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

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->fd->html('filter.lists', 'published', [
					'' => 'Select Installation State',
					'installed' => 'Installed',
					'notinstalled' => 'Not Installed',
					'updating' => 'Requires Updating'
				], $published); ?>
			</div>
		</div>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $pagination->limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EB_TABLE_COLUMN_TITLE'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_INSTALLED'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_INSTALLED_VERSION'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_LATEST_VERSION'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_ELEMENT'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($modules) { ?>

					<?php $i = 0; ?>

					<?php foreach ($modules as $module) { ?>
					<tr>
						<td class="center">
							<?php echo $this->fd->html('table.id', $i, $module->id); ?>
						</td>
						<td>
							<b><?php echo $module->title; ?></b>
							<div>
								<?php echo $module->description;?>
							</div>
						</td>
						<td class="center">
							<?php if ($module->state == EB_LANGUAGES_INSTALLED) { ?>
							<span class="t-text--success">
								<b><?php echo JText::_('Installed'); ?></b>
							</span>
							<?php } ?>

							<?php if ($module->state == EB_LANGUAGES_NEEDS_UPDATING) { ?>
							<span class="t-text--danger">
								<b><?php echo JText::_('Requires Updating'); ?></b>
							</span>

							<?php } ?>

							<?php if ($module->state == EB_LANGUAGES_NOT_INSTALLED) { ?>
								<b><?php echo JText::_('Not Installed'); ?></b>
							<?php } ?>
						</td>
						<td class="center">
							<?php if (!$module->installed) { ?>
								&mdash;
							<?php } else { ?>
								<b><?php echo $module->installed;?></b>
							<?php } ?>
						</td>
						<td class="center">
							<b><?php echo $module->version;?></b>
						</td>
						<td>
							<?php echo $module->element;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action', '', 'modules', 'modules'); ?>
</form>
