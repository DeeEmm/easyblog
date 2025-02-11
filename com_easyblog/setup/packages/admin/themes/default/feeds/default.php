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

		<?php echo $this->fd->html('filter.published', 'filter_state', $filterState, ['selectText' => 'COM_EASYBLOG_GRID_SELECT_STATE']); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<div class="mt-md">
			<?php echo $this->fd->html('alert.standard', 'COM_EASYBLOG_FEEDS_CRON_INFO', 'info', [
				'dismissible' => false,
				'button' => '&nbsp;&nbsp;' . $this->fd->html('button.link', 'https://stackideas.com/docs/easyblog/administrators/configuration/feeds-importer', 'COM_EASYBLOG_READMORE_HERE', 'default', 'sm')
			]); ?>
		</div>

		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%" class="center nowrap">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_FEEDS_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						&nbsp;
					</th>
					<th width="25%" style="text-align: left;">
						<?php echo JText::_('COM_EASYBLOG_FEEDS_URL'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PUBLISHED'); ?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYBLOG_FEEDS_LAST_IMPORT'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($feeds) { ?>
					<?php $i = 0; ?>

					<?php foreach ($feeds as $row) { ?>
					<tr>
						<td>
							<?php echo $this->fd->html('table.id', $i++, $row->id); ?>
						</td>
						<td align="left">
							<a href="index.php?option=com_easyblog&view=feeds&layout=form&id=<?php echo $row->id;?>" class=""><?php echo $row->title; ?></a>
						</td>
						<td class="center">
							<?php echo $this->fd->html('button.link', null, 'COM_EASYBLOG_FEEDS_TEST_IMPORT', 'default', 'sm', ['attributes' => 'data-feed-import data-id="' . $row->id . '"']); ?>
						</td>
						<td>
							<a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->url; ?></a>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $row, 'feeds', 'published'); ?>
						</td>
						<td class="center">
							<?php echo $row->import_text;?>
						</td>
						<td class="center nowrap">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6" class="empty">
							<?php echo JText::_('COM_EASYBLOG_FEEDS_NO_FEEDS_YET');?>
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
	<input type="hidden" name="view" value="feeds" />
</form>
