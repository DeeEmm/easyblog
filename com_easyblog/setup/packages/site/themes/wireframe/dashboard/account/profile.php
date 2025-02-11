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
<div class="eb-dashboard-form-section">
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_SETTINGS'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE', 'avatar'); ?>
				<div class="col-md-8">
					<div class="media">
						<div class="media-object pull-left t-mr--lg">
							<?php echo $this->html('avatar.user', $profile, 'lg'); ?>
						</div>

						<?php if ($this->acl->get('upload_avatar')) { ?>
						<div id="avatar-upload-form" class="media-body">

							<div class="t-mb--sm">
								<input id="file-upload" name="avatar" type="file" hidden class="t-hidden" data-eb-avatar-input />
								<span class="t-hidden" data-eb-avatar-filename></span>
								<input type="button" value="<?php echo JText::_('COM_EASYBLOG_UPLOAD_BUTTON');?>" class="btn btn-default" data-eb-upload-button>
							</div>
							<div class="t-text--muted">
								<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE_UPLOAD_CONDITION', (float) $this->config->get('main_upload_image_size', 0) , EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT); ?>
							</div>


							<div><span id="upload-clear"></span></div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<hr />
			<?php } ?>

			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME'); ?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'fullname', $this->escape($this->my->name)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_WHAT_OTHERS_CALL_YOU'); ?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'nickname', $this->escape($profile->nickname)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_USERNAME');?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', '', $this->my->username, '', array('attr' => 'disabled="disabled"')); ?>
				</div>
			</div>

			<?php if ($this->config->get('main_joomlauserparams')) { ?>
				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_EMAIL');?>

					<div class="col-md-8">
						<?php echo $this->html('dashboard.text', 'email', $this->escape($this->my->email)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD');?>

					<div class="col-md-8">
						<input class="form-control" type="password" id="password" name="password" value=""  autocomplete="new-password" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_ACCOUNT_RECONFIRM_PASSWORD');?>

					<div class="col-md-8">
						<input class="form-control" type="password" id="password2" name="password2"  autocomplete="new-password" />
					</div>
				</div>

				<div class="form-group">
						<?php echo $this->html('dashboard.label', 'COM_EB_JOOMLA_TIMEZONE'); ?>

					<div class="col-md-8">
						<?php echo $this->fd->html('form.userTimezone', 'timezone', $this->my->getParam('timezone')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EB_JOOMLA_LANGUAGE'); ?>

					<div class="col-md-8">
						<?php echo $this->fd->html('form.userLanguages', 'language', $this->my->getParam('language')); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
