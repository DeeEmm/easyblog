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

		<?php echo $this->fd->html('filter.lists', 'filter_state', [
			'' => 'COM_EASYBLOG_SELECT_STATE',
			'P' => 'COM_EASYBLOG_PUBLISHED',
			'U' => 'COM_EASYBLOG_UNPUBLISHED',
			'M' => JText::sprintf('COM_EASYBLOG_AWAITING_MODERATION', $totalPending)
		],$currentFilter); ?>

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
					<th><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_COMMENT');?></th>

					<th width="25%">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_COMMENTS_BLOG_TITLE', 'b.title', $order, $orderDirection); ?>
					</th>

					<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_PUBLISHED' ); ?></th>
					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_COMMENTS_DATE', 'a.created', $order, $orderDirection); ?>
					</th>
					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_COMMENTS_AUTHOR', 'a.created_by', $order, $orderDirection); ?>
					</th>
					<th class="center" width="10%">
						<?php echo JText::_('COM_EB_TABLE_COLUMN_IP_ADDRESS'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($comments) { ?>
					<?php $i = 0; ?>
					<?php foreach ($comments as $comment) { ?>
						<tr>
							<td class="center nowrap">
								<?php echo $this->fd->html('table.id', $i++, $comment->id); ?>
							</td>
							<td>
								<a href="index.php?option=com_easyblog&view=comments&layout=form&id=<?php echo $comment->id;?>"><?php echo EBString::substr($comment->comment, 0, 60);?></a><?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
							</td>
							<td>
								<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=entry&amp;id=' . $comment->post_id; ?>" target="_blank" data-eb-action>
									<?php echo EBString::substr($comment->blog_name, 0, 50); ?><?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
								</a>
							</td>
							<td class="center">
								<?php if ($comment->isModerate) { ?>
									<?php echo $this->html('grid.moderation', 'comment'); ?>
								<?php } else { ?>
									<?php echo $this->html('grid.published', $comment, 'comment', 'published'); ?>
								<?php } ?>
							</td>

							<td class="center">
								<?php echo EB::date($comment->created, true)->format();?>
							</td>

							<td class="center">
								<span>
								<?php if ($comment->created_by) { ?>
									<?php echo $comment->getAuthor()->getName(); ?>
								<?php } ?>

								<?php if (!$comment->created_by && $comment->name) { ?>
									<?php echo $comment->name; ?>
								<?php } ?>

								<?php if (!$comment->created_by && !$comment->name) { ?>
									<?php echo JText::_('COM_EASYBLOG_GUEST'); ?>
								<?php } ?>
								</span>
							</td>
							<td class="center"><?php echo $comment->ip ? $comment->ip : '-' ; ?></td>

							<td class="center"><?php echo $comment->id; ?></td>
						</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="8" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_COMMENTS_NO_COMMENT_ON_SITE_YET');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="view" value="comments" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
