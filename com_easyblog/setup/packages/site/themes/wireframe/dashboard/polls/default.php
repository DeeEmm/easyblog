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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=polls');?>" class="eb-dashboard-entries <?php echo !$polls ? 'is-empty' : '';?>" data-eb-dashboard-polls>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EB_DASHBOARD_HEADING_POLLS', $createButton),
		!$this->acl->get('polls_manage') ? '' : $this->fd->html('form.dropdown', 'task', '', call_user_func(function() {
									$options = [];
									$options['polls.publish'] = 'COM_EASYBLOG_PUBLISH';
									$options['polls.unpublish'] = 'COM_EASYBLOG_UNPUBLISH';
									$options['polls.delete'] = 'COM_EASYBLOG_DELETE';

									return $options;
								}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'polls-search', $search),
		[
			$this->html('dashboard.filters', $filter, [
				'all' => 'COM_EASYBLOG_FILTER_SELECT_FILTER',
				'published' => 'COM_EASYBLOG_FILTER_PUBLISHED',
				'unpublished' => 'COM_EASYBLOG_FILTER_UNPUBLISHED'
			])
		]
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EB_DASHBOARD_EMPTY_POLLS', 'COM_EB_DASHBOARD_EMPTY_POLLS_HINT', [
		'icon' => 'fdi fa fa-poll-h'
	]); ?>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<?php if ($this->acl->get('polls_manage')) { ?>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<?php } ?>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center center narrow-hide">
					<?php echo JText::_('COM_EB_DASHBOARD_POLL_CHOICES_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center center narrow-hide">
					<?php echo JText::_('COM_EB_DASHBOARD_POLL_VOTES_COLUMN_TITLE');?>
				</td>
				<td width="15%" class="text-center center narrow-hide">
					<?php echo JText::_('COM_EB_DASHBOARD_POLL_DATE_COLUMN_TITLE');?>
				</td>
				<td width="15%" class="text-center center xnarrow-hide">
					<?php echo JText::_('COM_EB_DASHBOARD_POLL_RESULTS_COLUMN_TITLE');?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($polls as $poll) { ?>
				<?php echo $this->output('site/dashboard/polls/item/default', ['poll' => $poll]); ?>
			<?php } ?>
		</tbody>


	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>

	<?php echo $this->fd->html('form.action'); ?>
</form>
