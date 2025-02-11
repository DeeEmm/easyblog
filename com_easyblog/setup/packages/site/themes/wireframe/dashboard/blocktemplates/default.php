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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="<?php echo !$blockTemplates ? 'is-empty' : '';?>" data-eb-dashboard-block-templates>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EB_DASHBOARD_HEADING_BLOCK_TEMPLATES'),
		$this->fd->html('form.dropdown', 'templateActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS',
					'blocks.publishTemplate' => 'COM_EASYBLOG_PUBLISH',
					'blocks.unpublishTemplate' => 'COM_EASYBLOG_UNPUBLISH',
					'blocks.deleteTemplate' => (object) [
						'title' => 'COM_EASYBLOG_DELETE',
						'attr' => 'data-confirmation="site/views/blocks/confirmDeleteBlockTemplates"'
					]
				];

				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EB_DASHBOARD_BLOCK_TEMPLATE_EMPTY', 'COM_EB_DASHBOARD_BLOCK_TEMPLATE_EMPTY_HINT', [
		'icon' => 'fdi fa fa-cube'
	]); ?>

	<table class="eb-table table table-striped">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall', false); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<?php if (FH::isSiteAdmin()) { ?>
				<td width="20%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_AUTHOR');?>
				</td>

				<td width="10%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_GLOBAL');?>
				</td>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($blockTemplates as $template) { ?>
			<tr data-eb-templates-item data-id="<?php echo $template->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $template->id, array('disabled' => false)); ?>
				</td>
				<td>
					<a class="post-title" href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplatesForm&id=' . $template->id);?>"><?php echo JText::_($template->title);?></a>

					<ul class="post-actions mt-5" data-eb-actions data-id="<?php echo $template->id;?>">
						<?php if ($this->acl->get('create_block_templates')) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="blocks.copyTemplate" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_DUPLICATE'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($template->canPublish()) { ?>
							<?php if ($template->published) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="blocks.unpublishTemplate" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_UNPUBLISH'); ?>
								</a>
							</li>
							<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="blocks.publishTemplate" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_PUBLISH'); ?>
								</a>
							</li>
							<?php } ?>
							<?php if ($template->canDelete()) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/blocks/confirmDeleteBlockTemplates" data-type="dialog" class="text-danger">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_DELETE'); ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center" width="10%">
					<?php if ($template->published) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<?php if (FH::isSiteAdmin()) { ?>
				<td class="text-center" width="20%">
					<?php echo $template->getAuthor()->getName();?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo $template->global;?>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>

	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="blockTemplates" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
