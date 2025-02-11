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
<?php if(!$installed) { ?>
<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<div class="panel">
			<div class="panel-body text-center">
				<p class="mt-20 mb-20"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_ZOO_COMPONENT_NOT_FOUND'); ?></p>
			</div>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_BEFORE_YOU_MIGRATE', '', '/administrators/migrations/migrating-from-zoo'); ?>

			<div class="panel-body">
				<ul style="padding: 0 0 0 10px; margin: 0 0 30px;">
					<li>
						Backup your existing database just in case anything goes wrong.
					</li>
					<li class="mt-10">
						Please review your settings for the migration below
					</li>
				</ul>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_ZOO_APPLICATION_ID', 'app_id'); ?>

					<div class="col-md-7">
						<?php echo $apps; ?>
					</div>
				</div>

				<div class="mt-20 text-right">
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-migrate-zoo><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_PROGRESS'); ?>

			<div class="panel-body">
				<span data-progress-loading class="eb-loader-o size-sm hide"></span>
				<div data-progress-empty><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div data-progress-status style="overflow:auto; height:98%;max-height:300px;"></div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_STATISTIC'); ?>
			
			<div class="panel-body">
				<div data-progress-stat style="overflow:auto; height:98%;"></div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<input type="hidden" name="layout" value="zoo" />
