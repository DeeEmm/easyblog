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
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login" class="eb-login text-center <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<h3 class="eb-login-title reset-heading mb-15">
		<?php echo JText::_('COM_EASYBLOG_MEMBER_LOGIN');?>
	</h3>

	<p class="eb-desp">
		<?php echo JText::_($message); ?>
	</p>

	<?php echo $this->fd->html('form.floatinglabel', 'COM_EASYBLOG_USERNAME', 'username', 'text'); ?>

	<?php echo $this->fd->html('form.floatinglabel', 'COM_EASYBLOG_PASSWORD', 'password', 'password'); ?>

	<?php if (FH::hasTwoFactor()) { ?>
		<?php echo $this->fd->html('form.floatinglabel', 'JGLOBAL_SECRETKEY', 'secretkey', 'text'); ?>
	<?php } ?>

	<div class="eb-login-footer">
		<?php if(JPluginHelper::isEnabled('system', 'remember')) { ?>
		<div class="eb-login-footer__cell text-left">
			<div class="eb-checkbox">
				<input id="eb-remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('COM_EASYBLOG_REMEMBER_ME', true) ?>"/>
				<label for="eb-remember">
					<?php echo JText::_('COM_EASYBLOG_REMEMBER_ME') ?>
				</label>
			</div>
		</div>
		<?php } ?>

		<div class="eb-login-footer__cell text-right">
			<?php echo $this->fd->html('button.submit', 'COM_EASYBLOG_LOGIN_BUTTON', 'primary', 'default'); ?>
		</div>
	</div>

	<hr />

	<div class="eb-login-help row-table">
		<div class="col-cell">
			<?php echo $this->fd->html('button.link', EB::getResetPasswordLink(), 'COM_EASYBLOG_FORGOT_YOUR_PASSWORD', 'default', 'default', ['block' => true]); ?>
		</div>
		<div class="col-cell">
			<?php echo $this->fd->html('button.link', EB::getRemindUsernameLink(), 'COM_EASYBLOG_FORGOT_YOUR_USERNAME', 'default', 'default', ['block' => true]); ?>
		</div>
	</div>

	<?php if (FH::isRegistrationEnabled()) { ?>
		<?php echo $this->fd->html('button.link', EB::getRegistrationLink(), 'COM_EASYBLOG_CREATE_AN_ACCOUNT', 'success', 'default', ['block' => true]); ?>
	<?php } ?>

	<input type="hidden" value="com_users"  name="option">
	<input type="hidden" value="user.login" name="task">
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo $this->fd->html('form.token'); ?>

	<?php if ($this->config->get('integrations_jfbconnect_login')) { ?>
		<?php echo EB::jfbconnect()->getTag();?>
	<?php } ?>
</form>
