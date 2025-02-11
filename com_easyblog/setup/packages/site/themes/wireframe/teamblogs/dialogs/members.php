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
<dialog>
	<width>400</width>
	<height>400</height>
	<selectors type="json">
	{
		"{teamMembers}" : "[data-team-members]",
		"{closeButton}" : "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		init : function() {
			this.teamMembers().implement(EasyBlog.Controller.Dashboard.Teamblogs);
		},

		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS'); ?></title>
	<content>
		<div data-team-members data-team-id="<?php echo $team->id; ?>">
		<?php if ($members) { ?>
			<?php foreach ($members as $member) { ?>
				<div class="eb-stats-author row-table" data-team-member data-id="<?php echo $member->id; ?>">
					<div class="col-cell cell-tight">
						<?php if ($team->canRemoveMember($member->id)) { ?>
						<span class="eb-stats-author__remove" href="javascript:void(0);" data-remove-member title="<?php echo JText::_('COM_EASYBLOG_REMOVE_TEAM_MEMBER'); ?>">
							<i class="fdi fa fa-times"></i>
						</span>
						<?php } ?>

						<?php echo $this->html('avatar.user', $member); ?>
					</div>
					<div class="col-cell">
						<b>
							<a href="<?php echo $member->getPermalink();?>"><?php echo $member->getName();?></a>
						</b>
						<span data-member-is-admin class="<?php echo $member->isAdmin ? '' : 'hide' ?>"><?php echo '(' . JText::_('COM_EASYBLOG_TEAMBLOG_IS_ADMIN') . ')';?></span>
						<div class="">
							<?php echo $this->getNouns('COM_EASYBLOG_AUTHOR_POST_COUNT', $member->postCount, true); ?>
							<?php if (FH::isSiteAdmin()) { ?>
								<div class="eb-stats-author__set-admin">
									<a href="javascript:void(0);" class="text-success <?php echo !$member->isAdmin ? '' : 'hide';?>" data-set-admin>
										<?php echo JText::_('Give admin right');?>
									</a>
									<a href="javascript:void(0);" class="text-danger <?php echo $member->isAdmin ? '' : 'hide';?>" data-remove-admin>
										<?php echo JText::_('Remove admin right');?>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
