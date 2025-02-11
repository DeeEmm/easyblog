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
<div class="eb-delete-info">
	<?php echo $this->html('snackbar.standard', 'COM_EB_GDPR_DELETE_INFO_HEADER'); ?>

	<p class="t-mt--md"><?php echo JText::_('COM_EB_GDPR_DELETE_INFO_DESCRIPTION');?></p>

	<ul style="">
		<?php if ($userId) { ?>
			<li><?php echo JText::_('COM_EB_GDPR_TAB_POST_TITLE'); ?></li>
			<li><?php echo JText::_('COM_EB_GDPR_TAB_TAG_TITLE'); ?></li>
		<?php } ?>

		<li><?php echo JText::_('COM_EB_GDPR_TAB_COMMENT_TITLE'); ?></li>
		<li><?php echo JText::_('COM_EB_GDPR_TAB_SUBSCRIPTION_TITLE'); ?></li>
	</ul>

	<div class="t-mt--lg t-mb--lg t-text--danger">
		<?php echo JText::_('COM_EB_GDPR_DELETE_INFO_CONFIRMATION_NOTICE');?>
	</div>

	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="download">

		<?php if ($userId) { ?>
		<div id="form-login-password" style="padding-bottom: 5px;">
			<label for="password"><?php echo JText::_('COM_EB_GDPR_DELETE_INFO_PASSWORD') ?></label><br />
			<input id="password" type="password" name="password" class="form-control half" size="18" alt="password" />
		</div>
		<?php } ?>

		<br />
		<?php echo $this->fd->html('button.link', 'javascript:void(0);', 'COM_EASYBLOG_DELETE', 'danger'); ?>

		<?php echo $this->fd->html('form.hidden', 'key', $data); ?>
		<?php echo $this->fd->html('form.action', 'profile.deleteInfo'); ?>
	</form>
</div>
