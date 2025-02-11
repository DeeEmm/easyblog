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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>" class="<?php echo !$teams ? 'is-empty' : '';?>" data-eb-dashboard-teams>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_TEAMBLOGS', call_user_func(function() {
				if (!$this->acl->get('create_team_blog') && !FH::isSiteAdmin()) {
					return [];
				}

				return [
					'icon' => 'fdi fa fa-users',
					'text' => 'COM_EASYBLOG_DASHBOARD_TEAMBLOGS_CREATE',
					'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogForm', false),
					'style' => 'primary'
				];
			})
		),
		$this->fd->html('form.dropdown', 'teamActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS'
				];

				if (FH::isSiteAdmin()) {
					$options['teamblogs.publish'] = 'COM_EASYBLOG_PUBLISH';
					$options['teamblogs.unpublish'] = 'COM_EASYBLOG_UNPUBLISH';
					$options['teamblogs.delete'] = (object) ['title' => 'COM_EASYBLOG_DELETE', 'attr' => 'data-confirmation="site/views/teamblog/confirmDelete"'];
				}

				if (!FH::isSiteAdmin()) {
					$options['teamblogs.leave'] = (object) ['title' => 'COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM', 'attr' => 'data-confirmation="site/views/teamblog/confirmLeave"'];
				}

				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_NO_TEAMS_AVAILABLE', 'COM_EASYBLOG_DASHBOARD_NO_TEAMS_AVAILABLE_HINT', [
		'icon' => 'fdi fa fa-user-friends'
	]); ?>

	<table class="eb-table table table-striped ">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td width="10%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_MEMBERS');?>
				</td>
				<td width="20%" class="narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ACCESS');?>
				</td>
			</tr>
		</thead>

		<?php if ($teams) { ?>
		<tbody>
			<?php foreach ($teams as $team) { ?>
			<tr data-eb-teamblogs-item data-id="<?php echo $team->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $team->id); ?>
				</td>
				<td>
					<a href="<?php echo $team->getEditPermalink();?>" class="post-title"><?php echo $team->getTitle();?></a>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $team->id;?>">
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/teamblog/viewMembers" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOG_VIEW_MEMBERS'); ?>
							</a>
						</li>

						<?php if ($team->isTeamAdmin()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/teamblog/inviteMembers" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEABMLOG_INVITE_MEMBERS'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if (FH::isSiteAdmin()) { ?>
							<?php if ($team->isPublished()) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="teamblogs.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?>
								</a>
							</li>
							<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="teamblogs.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
								</a>
							</li>
							<?php } ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/teamblog/confirmDelete" data-type="dialog" class="text-danger">
									<?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
								</a>
							</li>
						<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/teamblog/confirmLeave" data-type="dialog" class="text-danger">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM'); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center" width="10%">
					<?php if ($team->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo $team->getMembersCount();?>
				</td>
				<td class="narrow-hide" width="20%">
					<?php echo $team->getAccess();?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php } ?>

	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="teamblogs" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
