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
	<?php echo $this->html('snackbar.standard', 'COM_EASYBLOG_DASHBOARD_TWITTER_SETTINGS');?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>

				<div class="col-md-8">
					<?php if ($twitter->id && $twitter->request_token && $twitter->access_token) { ?>
					<label>
						<a href="<?php echo EBR::_('index.php?option=com_easyblog&task=oauth.revoke&client=' . EBLOG_OAUTH_TWITTER);?>" class="btn btn-default btn-sm">
							<i class="fdi fa fa-times"></i>&nbsp; <?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?>
						</a>
						<div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_REVOKE')?></div>
					</label>
					<?php } else { ?>
					<label class="t-mb--md"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></label>
					<div class="mtm">
						<a href="javascript:void(0);" class="btn btn-eb-twitter" data-oauth-signup data-client="twitter">
							<i class="fdi fab fa-twitter"></i>&nbsp; Twitter
						</a>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_OAUTH_MESSAGE'); ?>

				<div class="col-md-8">
					<textarea id="integrations_twitter_message" name="integrations_twitter_message" class="form-control"><?php echo (empty($twitter->message)) ? $this->config->get('main_twitter_message', JText::_('COM_EASYBLOG_EASYBLOG_TWITTER_AUTOPOST_MESSAGE') ) : $twitter->message; ?></textarea>
					<div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_CHAR_LIMIT')?></div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_OAUTH_ENABLED_BY_DEFAULT'); ?>

				<div class="col-md-8">
					<?php echo $this->fd->html('form.toggler', 'integrations_twitter_auto', $twitter->auto); ?>
				</div>
			</div>
		</div>
	</div>
</div>
