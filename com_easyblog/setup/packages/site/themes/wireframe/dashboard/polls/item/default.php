<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<tr data-dashboard-poll-item data-id="<?php echo $poll->id; ?>">
	<?php if ($this->acl->get('polls_manage')) { ?>
	<td width="1%">
		<?php echo $this->html('dashboard.checkbox', 'cid[]', $poll->id); ?>
	</td>
	<?php } ?>
	<td>
		<label class="post-title" data-dashboard-poll-title><?php echo $this->escape($poll->title) ;?></label>

		<div class="post-meta">
			<span>
				<?php echo EB::user($poll->user_id)->getName(); ?>
			</span>
		</div>
		<?php if ($this->acl->get('polls_manage')) { ?>
		<ul class="post-actions">
			<li>
				<a href="javascript:void(0);" class="t-text--danger" data-dashboard-poll-delete data-eb-action>
					<?php echo JText::_('COM_EB_DASHBOARD_POLL_DELETE_BUTTON'); ?>
				</a>
			</li>
		</ul>
		<?php } ?>
		<?php if ($this->isMobile()) { ?>
			<div class="mt-10">
				<div class="t-flex">
					<b><?php echo JText::_('COM_EB_DASHBOARD_POLL_CHOICES_COLUMN_TITLE');?>:</b> <?php echo $poll->getTotalItems(); ?>

				</div>
				<div class="t-flex">
					<b><?php echo JText::_('COM_EB_DASHBOARD_POLL_VOTES_COLUMN_TITLE');?>:</b> <?php echo $poll->getTotalVotes();?>
				</div>

			</div>

		<?php } ?>
	</td>

	<td width="10%" class="text-center narrow-hide">
		<span>
			<?php echo $poll->getTotalItems(); ?>
		</span>
	</td>

	<td width="10%" class="text-center narrow-hide">
		<span>
			<?php echo $poll->getTotalVotes();?>
		</span>
	</td>

	<td width="15%" class="text-center narrow-hide">
		<span>
			<?php echo $poll->created;?>
		</span>
	</td>

	<td width="15%" class="text-center">
		<a href="javascript:void(0)" data-dashboard-poll-view-result>
			<?php echo JText::_('COM_EB_DASHBOARD_POLL_VIEW_RESULTS'); ?>
		</a>
	</td>
</tr>
