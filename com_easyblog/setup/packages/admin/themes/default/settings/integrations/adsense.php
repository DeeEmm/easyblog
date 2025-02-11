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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_INFO', '/administrators/integrations/integrating-with-google adsense'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integration_google_adsense_enable', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integration_google_adsense_script', 'COM_EB_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_LOAD_SCRIPT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integration_google_adsense_centralized', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USE_CENTRALIZED'); ?>
				<?php echo $this->fd->html('settings.toggle', 'integrations_google_adsense_blogger', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ALLOW_BLOGGER_UPDATE'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'integration_google_adsense_display', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY', [
					'both' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BOTH',
					'header' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER',
					'footer' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER',
					'beforecomments' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_COMMENT',
					'userspecified' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USER_SPECIFIED'
				], '', '', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_NOTE'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'integration_google_adsense_display_access', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS', [
					'both' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ALL',
					'members' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS',
					'guests' => 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODES', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODES_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integration_google_adsense_responsive', 'COM_EASYBLOG_SETTINGS_ADSENSE_RESPONSIVE', '', 'data-adsense-responsive'); ?>

				<div class="form-group form-responsive<?php echo !$this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-responsive-form>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_ADSENSE_RESPONSIVE_CODES', 'integration_google_adsense_responsive_code'); ?>

					<div class="col-md-7">
						<textarea name="integration_google_adsense_responsive_code" id="integration_google_adsense_responsive_code" rows="5" class="form-control" cols="35"><?php echo $this->fd->html('str.escape', $this->config->get('integration_google_adsense_responsive_code', ''));?></textarea>

						<div class="mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_ADSENSE_ONLY_CODES_BELOW');?><br />

							<pre><?php echo $this->fd->html('str.escape', '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXX" data-ad-slot="xxxx" data-ad-format="auto"></ins>');?></pre>
						</div>
					</div>
				</div>

				<div class="form-group form-standard<?php echo $this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-code-form>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE', 'integration_google_adsense_code'); ?>

					<div class="col-md-7">
						<textarea name="integration_google_adsense_code" id="integration_google_adsense_code" rows="5" class="form-control" cols="35"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>

						<div class="mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?>:<br />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
