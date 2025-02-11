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
<form method="post" action="<?php echo JRoute::_('index.php');?>" enctype="multipart/form-data">
	<div class="eb-dashboard-form-section">
		<?php echo $this->html('snackbar.standard', 'COM_EB_DASHBOARD_BLOCK_TEMPLATES_EDIT'); ?>

		<div class="eb-dashboard-form-section__form">
			<div class="form-horizontal clear">
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EB_DASHBOARD_BLOCK_TEMPLATES_NAME'); ?></label>
					<div class="col-md-7">
						<input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->escape($template->title);?>" placeholder="<?php JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME_REQUIRED'); ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DESCRIPTION');?></label>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.textarea', 'description', $template->description); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?></label>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'published', $template->published); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-left">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates');?>" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?></a>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" data-submit-button>
				<?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $template->id;?>" />
	<?php echo $this->fd->html('form.action', 'blocks.updateTemplate'); ?>
</form>
