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
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<?php if ($this->acl->get('update_facebook')) { ?>

				<?php if ($this->config->get('main_facebook_ogauthor')) { ?>
				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_FACEBOOK_PROFILE_URL'); ?>

					<div class="col-md-8">
						<?php echo $this->html('dashboard.text', 'facebook_profile_url', $params->get('facebook_profile_url', '')); ?>
						<p class="small">
							<?php echo JText::_('COM_EASYBLOG_FACEBOOK_PROFILE_URL_INFO'); ?>
						</p>
					</div>
				</div>
				<?php } ?>

				<?php if ($this->config->get('main_facebook_ogpage')) { ?>
				<div class="form-group">
					<?php echo $this->html('dashboard.label', 'COM_EB_FACEBOOK_PAGE_URL'); ?>

					<div class="col-md-8">
						<?php echo $this->html('dashboard.text', 'facebook_page_url', $params->get('facebook_page_url', '')); ?>
						<p class="small">
							<?php echo JText::_('COM_EB_FACEBOOK_PAGE_URL_INFO'); ?>
						</p>
					</div>
				</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>