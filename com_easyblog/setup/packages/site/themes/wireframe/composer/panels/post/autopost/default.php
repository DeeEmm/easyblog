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
<?php
if (($this->config->get('integrations_twitter_centralized_and_own') && $user->hasOauth('twitter') && $this->config->get('integrations_twitter')) ||
	($this->config->get('integrations_linkedin_centralized_and_own') && $user->hasOauth('linkedin') && $this->config->get('integrations_linkedin'))
) {
?>
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('social_publishing', true) ? 'is-open' : ''; ?>" data-name="social_publishing" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYSOCIAL_COMPOSER_SOCIAL_PUBLISHING'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.panel.help', 'COM_EASYBLOG_AUTOPOST_DESC'); ?>

		<div class="o-form-group">
			<div class="o-control-input">
				<div class="eb-comp-autopost">
					<?php if ($this->config->get('integrations_twitter') && $this->config->get('integrations_twitter_centralized_and_own') && $user->hasOauth('twitter')) { ?>
					<div class="o-checkbox o-checkbox--inline t-mr--lg">
						<input type="checkbox" name="autoposting[]" id="autopost-twitter" value="twitter" data-autopost-twitter
							<?php if ($user->getOauth('twitter')->isShared($post->id)) { ?>
							disabled="disabled"
							<?php } else { ?>
								<?php echo $user->getOauth('twitter')->auto ? ' checked' : '';?>
							<?php } ?>
						/>
						<label for="autopost-twitter"
							data-eb-provide="tooltip"
							data-placement="bottom"
							<?php if ($user->getOauth('twitter')->isShared($post->id)) { ?>
							data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_TWITTER_INFO_SHARED');?>"
							<?php } else { ?>
							data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_TWITTER_INFO');?>"
							<?php } ?>
						>
							<i class="fdi fab fa-twitter-square"></i>
						</label>
					</div>
					<?php } ?>

					<?php if ($this->config->get('integrations_linkedin') && $this->config->get('integrations_linkedin_centralized_and_own') && $user->hasOauth('linkedin')) { ?>
					<div class="o-checkbox o-checkbox--inline t-mr--md">
						<input type="checkbox" name="autoposting[]" id="autopost-linkedin" value="linkedin" data-autopost-linkedin
							<?php if ($user->getOauth('linkedin')->isShared($post->id)) { ?>
							disabled="disabled"
							<?php } else { ?>
								<?php echo $user->getOauth('linkedin')->auto ? ' checked' : '';?>
							<?php } ?>
						/>
						<label for="autopost-linkedin"
							data-eb-provide="tooltip"
							data-placement="bottom"
							<?php if ($user->getOauth('linkedin')->isShared($post->id)) { ?>
							data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_LINKEDIN_INFO_SHARED');?>"
							<?php } else { ?>
							data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_LINKEDIN_INFO');?>"
							<?php } ?>
						>
							<i class="fdi fa fa-linkedin-square"></i>
						</label>
					</div>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>