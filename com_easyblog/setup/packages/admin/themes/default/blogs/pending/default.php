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
		<?php echo $this->fd->html('filter.search', $search, 'search', ['tooltip' => 'COM_EB_SEARCH_TOOLTIP_PENDING']); ?>

		<?php echo $this->fd->html('filter.custom', $categoryFilter); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-table-grid>
			<thead>
				<tr>
					<td width="1%" class="center">
						<?php echo $this->fd->html('table.checkAll'); ?>
					</td>
					<td>
						<?php echo $this->fd->html('table.sort', 'Title', 'a.title', $order, $orderDirection); ?>
					</td>
					<td width="20%">
						&nbsp;
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_CATEGORY'); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</td>
					<td width="15%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_HEADING_CREATED', 'a.created', $order, $orderDirection); ?>
					</th>
					<td width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TABLE_HEADING_ID', 'a.post_id', $order, $orderDirection); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($posts) { ?>
					<?php $i = 0; ?>
					<?php foreach ($posts as $post) { ?>
					<tr
						data-item
						data-id="<?php echo $post->uid;?>"
						data-title="<?php echo $post->title;?>"
					>
						<td>
							<?php echo $this->fd->html('table.id', $i++, $post->uid); ?>
						</td>
						<td>
							<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>"><?php echo $post->title;?></a>
						</td>
						<td class="center">
							<div>
								<a class="btn btn-primary" href="javascript:void(0);" data-eb-action data-blog-accept data-id="<?php echo $post->uid;?>">
									<?php echo JText::_('COM_EASYBLOG_APPROVE_BUTTON');?>
								</a>
								&nbsp;
								<a class="btn btn-danger" href="javascript:void(0);" data-eb-action data-blog-reject data-id="<?php echo $post->uid;?>">
									<?php echo JText::_('COM_EASYBLOG_REJECT_BUTTON');?>
								</a>
							</div>
						</td>
						<td class="center">
							<?php echo $post->getPrimaryCategory(true)->title;?>
						</td>
						<td class="center">
							<?php echo $post->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
						</td>
						<td class="center">
							<?php echo $post->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="7" class="empty">
						<?php echo JText::_('COM_EASYBLOG_PENDING_EMPTY'); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</td>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="layout" value="pending" />
	<input type="hidden" name="task" value="" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
