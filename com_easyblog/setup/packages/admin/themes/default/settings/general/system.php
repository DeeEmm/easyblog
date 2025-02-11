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
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS', 'COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS_INFO', '/administrators/configuration/system-configuration'); ?>

			<div class="panel-body">

				<?php echo $this->fd->html('settings.toggle', 'show_outdated_message', 'COM_EASYBLOG_SETTINGS_SYSTEM_SHOW_SOFTWARE_UPDATE_NOTIFICATIONS'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL', 'ajax_use_index'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'ajax_use_index', $this->config->get('ajax_use_index')); ?>
						
						<div class="mt-10">
							<p class="text-muted"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL_INFO', rtrim(JURI::root(), '/'));?></p>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_CDN_URL', 'cdn_url'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'cdn_url', $this->config->get('cdn_url', '')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'easyblog_jquery', 'COM_EASYBLOG_SETTINGS_SYSTEM_LOAD_EASYBLOG_JQUERY'); ?>
			</div>
		</div>
	</div>

	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER', 'main_orphanitem_ownership'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.user', 'main_orphanitem_ownership', $this->config->get('main_orphanitem_ownership', EB::getDefaultSAIds())); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>