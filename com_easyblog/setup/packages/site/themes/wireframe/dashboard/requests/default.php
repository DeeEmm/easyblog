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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=requests');?>" class="<?php echo !$requests ? 'is-empty' : '';?>" data-eb-dashboard-requests>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAM_REQUESTS'),
		$this->fd->html('form.dropdown', 'favoriteActions', '', [
				'' => 'COM_EASYBLOG_BULK_ACTIONS',
				'teamblogs.approve' => 'COM_EASYBLOG_APPROVE',
				'teamblogs.reject' => 'COM_EASYBLOG_REJECT'
			], ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_REQUESTS_EMPTY', 'COM_EASYBLOG_DASHBOARD_REQUESTS_EMPTY_HINT', [
		'icon' => 'fdi fa fa-users'
	]); ?>

	<table class="eb-table table table-striped table-hover mt-20">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_USER');?>
				</td>
				<td class="text-center" width="40%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TEAM');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>

		<?php if ($requests) { ?>
		<tbody>
			<?php foreach ($requests as $request) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $request->id); ?>
				</td>
				<td>
					<div>
						<?php echo $request->user->getName();?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $request->id;?>" data-eb-actions>
						<li>
							<a href="javascript:void(0);" data-eb-action="teamblogs.approve" data-type="form"><?php echo JText::_('COM_EASYBLOG_APPROVE');?></a>
						</li>
						<li>
							<a href="javascript:void(0);" data-eb-action="teamblogs.reject" data-type="form" class="text-danger"><?php echo JText::_('COM_EASYBLOG_REJECT');?></a>
						</li>
					</ul>
				</td>
				<td class="text-center" width="40%">
					<a href="<?php echo $request->team->getPermalink();?>"><?php echo $request->team->title;?></a>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo $request->date->format(JText::_('DATE_FORMAT_LC3')); ?>
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

	<?php echo $this->fd->html('form.action'); ?>

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="requests" />
</form>
