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
<div data-subscribe-cta>
	<div class="t-hidden" data-subscribe-cta-error>
		<?php echo $this->fd->html('alert.standard', '', 'danger'); ?>
	</div>

	<div class="eb-subscribe-form <?php echo $this->config->get('layout_post_subscribe_style') == 'dark' ? 'eb-subscribe-form--dark' : ''; ?>">
		<div class="eb-subscribe-form__inner">
			<div class="eb-subscribe-form__title"><?php echo JText::_('COM_EB_SUBSCRIPTION_FORM_TITLE'); ?></div>

			<div>
				<p><?php echo JText::_('COM_EB_SUBSCRIPTION_FORM_DESC'); ?></p>

				<div class="form-group">
					<label for="subscription_name" class="sr-only"><?php echo JText::_('COM_EB_PLACEHOLDER_YOUR_NAME'); ?></label>
					<?php echo $this->fd->html('form.text', 'subscription_name', $user->name, 'subscription_name', ['attr' => 'data-subscribe-name', 'placeholder' => 'COM_EB_PLACEHOLDER_YOUR_NAME']); ?>
				</div>

				<div class="form-group">
					<label for="subscription_email" class="sr-only"><?php echo JText::_('COM_EB_PLACEHOLDER_EMAIL_ADDRESS'); ?></label>
					<?php echo $this->fd->html('form.email', 'subscription_email', $user->email, 'subscription_email', ['attr' => 'data-subscribe-email', 'placeholder' => 'COM_EB_PLACEHOLDER_EMAIL_ADDRESS']); ?>
				</div>
			</div>

			<button class="btn btn-primary btn-block" data-subscribe-button><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG'); ?></button>
		</div>
	</div>
</div>
