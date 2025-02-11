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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="<?php echo !$templates ? 'is-empty' : '';?>" data-eb-dashboard-templates>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES',  call_user_func(function() {
				if (FH::isSiteAdmin() || $this->acl->get('create_post_templates')) {
					return [
						'icon' => 'fdi far fa-file-alt',
						'text' => 'COM_EASYBLOG_NEW_TEMPLATE',
						'link' => EB::composer()->getTemplateComposeUrl(),
						'style' => 'primary'
					];
				}

				return [];
			})
		),
		$this->fd->html('form.dropdown', 'templateActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS',
					'templates.publish' => 'COM_EASYBLOG_PUBLISH',
					'templates.unpublish' => 'COM_EASYBLOG_UNPUBLISH',
					'templates.delete' => (object) [
						'title' => 'COM_EASYBLOG_DELETE',
						'attr' => 'data-confirmation="site/views/templates/confirmDeleteTemplates"'
					]
				];

				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_TEMPLATE_EMPTY', 'COM_EASYBLOG_DASHBOARD_TEMPLATE_EMPTY_HINT', [
		'icon' => 'fdi fa fa-pencil-ruler',
		'button' => $this->fd->html('button.link', EB::composer()->getTemplateComposeUrl(), 'COM_EASYBLOG_NEW_TEMPLATE', 'primary')
	]); ?>

	<table class="eb-table table table-striped">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall', $disabled); ?>
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
			<?php foreach ($templates as $template) { ?>
			<tr data-eb-templates-item data-id="<?php echo $template->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $template->id, array('disabled' => $disabled)); ?>
				</td>
				<td>
					<a href="<?php echo EB::composer()->getTemplateComposeUrl(array('id' => $template->id)); ?>" class="post-title"><?php echo JText::_($template->title);?></a>

					<ul class="post-actions mt-5" data-eb-actions data-id="<?php echo $template->id;?>">
						<?php if ($this->acl->get('create_post_templates') && !$template->isBlank()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="templates.copy" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_DUPLICATE'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($template->canPublish()) { ?>
							<?php if ($template->published) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="templates.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_UNPUBLISH'); ?>
								</a>
							</li>
							<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="templates.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_PUBLISH'); ?>
								</a>
							</li>
							<?php } ?>
							<?php if (!$template->isBlank()) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/templates/confirmDeleteTemplates" data-type="dialog" class="text-danger">
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
					<?php echo $template->system;?>
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
	<input type="hidden" name="layout" value="templates" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
