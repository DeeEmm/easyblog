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
		<?php echo $this->fd->html('filter.lists', 'filter_version', $versions, $version, [
			'initial' => 'All Versions',
			'initialValue' => 'all'
		]); ?>

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
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_COLUMN_TITLE', 'title', $order, $orderDirection); ?>
					</th>

					<th width="5%" class="center nowrap">
						<?php echo $this->fd->html('table.sort', 'Version', 'version', $order, $orderDirection); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $scripts ){ ?>
					<?php $i = 0; ?>
					<?php foreach($scripts as $script) { ?>
						<tr
							data-item
							data-id="<?php echo $script->key;?>"
							data-title="<?php echo $script->title;?>"
						>
							<td class="center hidden-iphone" valign="top">

								<?php echo $this->fd->html('table.id', $i, $script->key); ?>
							</td>
							<td>
								<div><b><?php echo $script->title; ?></b></div>
								<div class="fd-small"><?php echo $script->description; ?></div>
							</td>
							<td class="center"><?php echo $script->version; ?></td>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="3" align="center">
						<?php echo JText::_('No Scripts found.');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="maintenance" />
	<input type="hidden" name="layout" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
