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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_BEFORE_YOU_MIGRATE', '', '/administrators/migrations/migrating-from-joomla-articles'); ?>

			<div class="panel-body">
				<ul style="padding: 0 0 0 10px; margin: 0 0 30px;">
					<li>
						Backup your existing database just in case anything goes wrong.
					</li>
					<li class="mt-15">
						To migrate all articles, please leave the options below to it's default value.
					</li>
				</ul>

				<div class="mt-20 form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY', 'catId'); ?>

					<div class="col-md-7">
						<?php echo $lists['catid'];?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR', 'authorId'); ?>

					<div class="col-md-7">
						<?php echo $lists['authorid']; ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_JOOMLA_STATE', 'state'); ?>
					
					<div class="col-md-7">
						<?php echo $lists['state']; ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_JOOMLA_EASYBLOG_CATEGORIES', 'categoryid'); ?>

					<div class="col-md-7">
						<?php echo $categories; ?>
					</div>
				</div>

				<div class="mt-20 text-right">
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-migrate-joomla><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></a>
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
				<div data-progress-status style="overflow:auto; height:98%;max-height: 300px;"></div>
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
<input type="hidden" name="layout" value="joomla" />
