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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_RSS', 'COM_EASYBLOG_SETTINGS_RSS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_rss', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_rss_content', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT', [
					'introtext' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_INTROTEXT',
					'fulltext' => 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_FULLTEXT'
				], '', '', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_NOTICE'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_FEED_IMPORTER', 'COM_EB_SETTINGS_FEED_IMPORTER_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'main_rss_cronlimit', 'COM_EB_SETTINGS_RSS_CRON_LIMIT', 'COM_EB_SETTINGS_RSS_CRON_LIMIT_DESC', [
					'postfix' => 'Items',
					'size' => 5
				]); ?>
			</div>
		</div>
	</div>

	<div class="space-y-md">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_FEEDBURNER', 'COM_EASYBLOG_SETTINGS_FEEDBURNER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_FEEDBURNER_RSS_URL', 'rss_url'); ?>

					<div class="col-md-7">
						<div class="form-control-static"><?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=feed&type=rss</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'main_feedburner', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_feedburnerblogger', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL', 'main_feedburner_url'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'main_feedburner_url', $this->config->get('main_feedburner_url', '')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
