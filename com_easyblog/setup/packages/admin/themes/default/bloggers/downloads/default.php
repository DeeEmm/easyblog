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
	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</th>

					<th style="text-align:left;">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_NAME'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_DOWNLOAD'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_STATUS'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_CREATED'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo  Jtext::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($requests) { ?>
				<?php $i = 0; ?>
				<?php foreach ($requests as $request) { ?>
				<tr>
					<td>
						<?php echo $this->fd->html('table.id', $i++, $request->id); ?>
					</td>

					<td>
						<a href="index.php?option=com_easyblog&view=bloggers&layout=form&id=<?php echo $request->getRequester()->id;?>"><?php echo $request->getRequester()->getName();?></a>
					</td>

					<td>
						<?php if ($request->isReady()) { ?>
							<a href="index.php?option=com_easyblog&view=bloggers&layout=downloadData&id=<?php echo $request->id;?>"><?php echo JText::_('COM_EB_DOWNLOAD');?></a>
						<?php } else { ?>
							&mdash;
						<?php } ?>
					</td>

					<td>
						<?php echo $request->getStateLabel(); ?>
					</td>

					<td>
						<?php echo EB::date($request->created)->format();?>
					</td>

					<td class="center">
						<?php echo $request->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center">
						<?php echo JText::_('COM_EB_USER_DOWNLOAD_NO_ITEMS');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="6">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="bloggers" />
	<input type="hidden" name="layout" value="downloads" />
</form>
