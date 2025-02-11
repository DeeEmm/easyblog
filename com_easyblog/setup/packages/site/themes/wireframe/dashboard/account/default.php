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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal l-stack l-spaces--sm" enctype="multipart/form-data" data-eb-dashboard-account>

	<?php echo $this->output('site/dashboard/account/profile'); ?>

	<?php if ($this->acl->get('add_entry') ) { ?>
		<?php echo $this->output('site/dashboard/account/blog'); ?>

		<?php if ($this->acl->get('update_facebook') && ($this->config->get('main_facebook_ogauthor'))) { ?>
			<?php echo $this->output('site/dashboard/account/facebook'); ?>
		<?php } ?>

		<?php if ($this->acl->get('allow_seo')) { ?>
			<?php echo $this->output('site/dashboard/account/metas'); ?>
		<?php } ?>

		<?php if ($this->config->get('integrations_google_adsense_blogger') && $this->acl->get('add_adsense') && $this->config->get('integration_google_adsense_enable')) { ?>
			<?php echo $this->output('site/dashboard/account/adsense'); ?>
		<?php } ?>

		<?php if ($this->config->get('main_feedburner') && $this->config->get('main_feedburnerblogger') && $this->acl->get('allow_feedburner') ) { ?>
			<?php echo $this->output('site/dashboard/account/feedburner'); ?>
		<?php } ?>
	<?php } ?>

	<?php if ($twoFactorMethods && count($twoFactorMethods) > 1) { ?>
		<?php echo $this->output('site/dashboard/account/twofactor'); ?>
	<?php } ?>

	<?php if ($this->config->get('gdpr_enabled') && (!$this->config->get('integrations_easysocial_editprofile') || !EB::easysocial()->exists())) { ?>
		<?php echo $this->output('site/dashboard/account/gdpr'); ?>
	<?php } ?>

	<div class="form-actions">
		<div class="pull-right">
			<button class="btn btn-primary"><?php echo JText::_('COM_EASYBLOG_SAVE_PROFILE'); ?></button>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', 'profile.save'); ?>
</form>
