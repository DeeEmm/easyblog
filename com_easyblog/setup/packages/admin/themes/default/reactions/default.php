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
		<?php echo $this->fd->html('filter.spacer'); ?>

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
					<th class="center" width="5%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_REACTION'); ?>
					</th>
					<th>
						&nbsp;
					</th>
					<th class="center" width="20%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POST'); ?>
					</th>
					<th class="center" width="15%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($reactions) { ?>
					<?php $i = 0; ?>
					<?php foreach ($reactions as $reaction) { ?>
						<tr>
							<td class="center nowrap">
								<?php echo $this->fd->html('table.id', $i++, $reaction->id); ?>
							</td>
							<td class="center">
								<i class="eb-emoji-icon eb-emoji-icon--sm eb-emoji-icon--<?php echo $reaction->type;?>"></i>
							</td>
							<td>
								<?php echo JText::sprintf('COM_EASYBLOG_REACTIONS_USER_REACTED', $reaction->user->getName(), ucfirst($reaction->type)); ?>
							</td>
							<td class="center">
								<?php echo $reaction->post->title;?>
							</td>
							<td class="center">
								<?php echo EB::date($reaction->created, true)->format(JText::_('DATE_FORMAT_LC2'));?>
							</td>
							<td class="center"><?php echo $reaction->id; ?></td>
						</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="6" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_NO_REACTIONS_HISTORY_YET');?>
					</td>
				</tr>
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

	<?php echo $this->fd->html('form.action'); ?>

	<input type="hidden" name="view" value="reactions" />
</form>
