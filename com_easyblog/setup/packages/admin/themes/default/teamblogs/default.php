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

		<?php if (!$browse) { ?>
		<?php echo $this->fd->html('filter.published', 'filter_state', $filterState, ['selectText' => 'COM_EASYBLOG_GRID_SELECT_STATE']); ?>
		<?php } ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle" data-table-grid>
			<thead>
				<?php if( !$browse ){ ?>
				<th class="center" width="1%">
					<?php echo $this->fd->html('table.checkAll'); ?>
				</th>
				<?php }?>

				<th style="text-align: left;">
					<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_TEAMBLOGS_TEAM_NAME', 'a.title', $order, $orderDirection); ?>
				</th>

				<?php if (!$browse) { ?>
				<th width="1%" class="center nowrap">
					<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
				</th>

				<th width="15%" class="center">
					<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ACCESS'); ?>
				</th>

				<th width="10%" class="center">
					<?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' ); ?>
				</th>
				<?php } ?>

				<th width="5%" class="center">
					<?php echo $this->fd->html('table.sort', 'COM_EASYBLOG_ID', 'a.id', $order, $orderDirection); ?>
				</th>
			</thead>
			<tbody>
				<?php if( $teams ){ ?>
					<?php $i = 0; ?>
					<?php foreach ($teams as $team) { ?>
					<tr>
						<?php if( !$browse ){ ?>
						<td width="1%" class="center nowrap">
							<?php echo $this->fd->html('table.id', $i, $team->id); ?>
						</td>
						<?php } ?>

						<td>
							<?php if ($browse) { ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $team->id;?>','<?php echo addslashes($this->escape($team->title));?>');">
							<?php } else {?>
								<a href="index.php?option=com_easyblog&view=teamblogs&layout=form&id=<?php echo $team->id;?>">
							<?php } ?><?php echo $team->title;?></a>
						</td>

						<?php if (!$browse) { ?>
						<td class="center nowrap">
							<?php echo $this->html('grid.published', $team, 'teamblogs', 'published'); ?>
						</td>

						<td class="center">
							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER) { ?>
								<?php echo JText::_('COM_EASYBLOG_TEAM_MEMBER_ONLY');?>
							<?php } ?>

							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED) { ?>
								<?php echo JText::_('COM_EASYBLOG_ALL_REGISTERED_USERS');?>
							<?php } ?>

							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_EVERYONE) { ?>
								<?php echo JText::_('COM_EASYBLOG_EVERYONE'); ?>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo $team->getMembersCount();?>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $team->id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6" class="empty">
							<?php echo JText::_('COM_EASYBLOG_NO_TEAM_BLOGS_CREATED_YET');?>
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

	<?php if ($browse) { ?>
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browseFunction" value="<?php echo $browsefunction;?>" />
	<?php } ?>

	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="view" value="teamblogs" />
	<?php echo $this->fd->html('form.ordering', 'filter_order', $order); ?>
	<?php echo $this->fd->html('form.orderingDirection', 'filter_order_Dir', ""); ?>
</form>
