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
		<?php echo $this->fd->html('filter.search', $search, 'search', ['tooltip' => 'COM_EB_SEARCH_TOOLTIP_DRAFTS']); ?>

		<?php echo $this->fd->html('filter.custom', $categoryFilter); ?>

		<?php echo $this->fd->html('filter.custom', $authorFilter); ?>

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
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_BLOGS_BLOG_TITLE', 'a.title', $order, $orderDirection); ?>
					</th>
					<th width="20%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_CATEGORY'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</th>
					<th width="20%" class="nowrap center hidden-phone">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_DATE', 'a.created', $order, $orderDirection); ?>
					</th>

					<th width="5%" class="nowrap center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_COLUMN_ID', 'a.id', $order, $orderDirection); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($drafts) { ?>
					<?php $i = 0; ?>
					<?php foreach ($drafts as $draft) { ?>
					<tr>
						<td>
							<?php echo $this->fd->html('table.id', $i++, $draft->revision->id); ?>
						</td>
						<td>
							<div style="max-width: 450px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
								<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $draft->id . '.' . $draft->revision_id));?>">
									<?php echo ($draft->title) ? $draft->title : $draft->revision->getTitle(); ?> (<?php echo JText::sprintf('COM_EASYBLOG_DRAFTS_REVISION_NUMBER', $draft->revisionOrdering); ?>)
								</a>
							</div>
						</td>
						<td class="center">
							<?php echo ($draft->getPrimaryCategory()) ? $draft->getPrimaryCategory()->getTitle() : '-'; ?>
						</td>
						<td class="center">
							<?php echo $draft->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $draft->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
						</td>
						<td class="center">
							<?php echo $draft->revision->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="6" class="empty">
						<?php echo JText::_('COM_EASYBLOG_DRAFTS_EMPTY'); ?>
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
	</td>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="layout" value="drafts" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', $orderDirection); ?>
</form>
