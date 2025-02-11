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
<?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) ||
		  ($this->config->get('layout_composer_creationdate')) ||
		  ($this->config->get('layout_composer_publishingdate')) ||
		  ($this->config->get('layout_composer_unpublishdate')) ||
		  ($this->config->get('layout_composer_autopostdate') && EB::oauth()->isAutopostEnabled()) ||
		  ($this->config->get('main_copyrights')) ||
		  ($this->config->get('main_password_protect') && !$post->isFeatured()) ||
		  ($this->acl->get('contribute_frontpage')) ||
		  ($this->acl->get('change_setting_subscription') && $this->config->get('main_subscription')) ||
		  ($this->config->get('layout_composer_comment') && $this->config->get('main_comment') && $this->acl->get('change_setting_comment'))
		) { ?>
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('post_properties', true) ? 'is-open' : ''; ?>" data-name="post_properties" data-eb-composer-block-section>

	<?php echo $this->html('composer.panel.header', 'COM_EASYSOCIAL_COMPOSER_GENERAL'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php if ($this->config->get('layout_composer_privacy') && $this->acl->get('enable_privacy')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_VISIBILITY', 'access', true); ?>

			<?php echo $this->html('composer.field.privacy', 'access', $post->access, ['author' => $post->created_by]); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_author_alias', false)) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_ALIAS', 'author_alias', true); ?>

			<?php echo $this->html('composer.field.text', 'author_alias', $post->author_alias, [
				'placeholder' => 'COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_ALIAS_PAGE_TITLE_PLACEHOLDER'
			]); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_creationdate')) { ?>
			<div class="o-form-group">
				<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_CREATION_DATE', 'created', true); ?>

				<?php echo $this->html('composer.field.calendar', 'created', $post->getFormDateValue('created'), EB::date($post->getFormDateValue('created'))->format(EB_DATE_INPUT_FORMAT), [
					'placeholder' => EASYBLOG_DATE_PLACEHOLDER
				]); ?>
			</div>

		<?php } else { ?>
			<?php echo $this->fd->html('form.hidden', 'created', $post->getFormDateValue('created')); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_publishingdate')) { ?>
			<div class="o-form-group">
				<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_PUBLISH_DATE', 'publish_up', true); ?>

				<?php echo $this->html('composer.field.calendar', 'publish_up', $post->getFormDateValue('publish_up'), EB::date($post->getFormDateValue('publish_up'))->format(EB_DATE_INPUT_FORMAT), [
					'placeholder' => EASYBLOG_DATE_PLACEHOLDER,
					'emptyText' => 'COM_EASYBLOG_COMPOSER_IMMEDIATELY'
				]); ?>
			</div>

		<?php } else { ?>
			<?php echo $this->fd->html('form.hidden', 'publish_up', $post->getFormDateValue('publish_up')); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_unpublishdate')) { ?>
			<div class="o-form-group">
				<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_UNPUBLISH_DATE', 'publish_down', true); ?>

				<?php echo $this->html('composer.field.calendar', 'publish_down', $post->publish_down !== EASYBLOG_NO_DATE ? $post->getFormDateValue('publish_down') : '', $post->publish_down !== EASYBLOG_NO_DATE ? EB::date($post->getFormDateValue('publish_down'))->format(EB_DATE_INPUT_FORMAT) : '', [
					'placeholder' => EASYBLOG_DATE_PLACEHOLDER,
					'emptyText' => 'COM_EASYBLOG_COMPOSER_NEVER'
				]); ?>
			</div>
		<?php } else { ?>
			<?php echo $this->fd->html('form.hidden', 'publish_down', $post->publish_down !== EASYBLOG_NO_DATE ? $post->getFormDateValue('publish_down') : ''); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_autopostdate') && EB::oauth()->isAutopostEnabled()) { ?>
			<div class="o-form-group">
				<?php echo $this->html('composer.field.label', 'COM_EB_COMPOSER_AUTOPOSTING_DATE', 'autopost_date', true); ?>

				<?php echo $this->html('composer.field.calendar', 'autopost_date', $post->autopost_date !== EASYBLOG_NO_DATE ? $post->getFormDateValue('autopost_date') : '', $post->autopost_date !== EASYBLOG_NO_DATE ? EB::date($post->getFormDateValue('autopost_date'))->format(EB_DATE_INPUT_FORMAT) : '', [
					'placeholder' => EASYBLOG_DATE_PLACEHOLDER,
					'emptyText' => 'COM_EB_COMPOSER_IMMEDIATELY_AUTOPOST'
				]); ?>
			</div>
		<?php } else { ?>
			<?php echo $this->fd->html('form.hidden', 'autopost_date', $post->autopost_date !== EASYBLOG_NO_DATE ? $post->getFormDateValue('autopost_date') : ''); ?>
		<?php } ?>

		<?php if ($this->config->get('main_password_protect') && !$post->isFeatured()) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_PASSWORD_PROTECTION', 'blogpassword', true); ?>

			<?php echo $this->html('composer.field.password', 'blogpassword', $post->blogpassword, [
				'attributes' => 'autocomplete="off"',
				'mask' => false
			]); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_POST_LANGUAGE', 'eb_language', true); ?>

			<?php echo $this->html('composer.field.language', 'eb_language', $post->language); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_copyrights')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COPYRIGHTS', 'copyrights', true); ?>

			<?php echo $this->html('composer.field.textarea', 'copyrights', $post->copyrights); ?>
		</div>
		<?php } ?>

		<?php if ($this->acl->get('contribute_frontpage')) { ?>
			<?php if ($this->config->get('layout_composer_frontpage')) { ?>
				<div class="o-form-group">
					<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_FRONTPAGE', 'frontpage', true); ?>

					<?php echo $this->html('composer.field.toggler', 'frontpage', $post->frontpage); ?>
				</div>
			<?php } else { ?>
				<?php echo $this->fd->html('form.hidden', 'frontpage', $post->frontpage ? 1 : 0); ?>
			<?php } ?>
		<?php } ?>

		<?php if (!$post->blogpassword && $this->config->get('layout_composer_feature') && $this->acl->get('feature_entry')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_FEATURE_POST', 'isfeatured', true); ?>

			<?php echo $this->html('composer.field.toggler', 'isfeatured', $post->isfeatured); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_comment') && $this->config->get('main_comment') && $this->acl->get('change_setting_comment')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_ALLOW_COMMENTS', 'allowcomment', true); ?>

			<?php echo $this->html('composer.field.toggler', 'allowcomment', $post->allowcomment); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_login_to_read') && $this->acl->get('change_login_to_read')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EB_COMPOSER_ENFORCE_LOGIN_TO_READ', 'login_to_read', true); ?>

			<?php echo $this->html('composer.field.toggler', 'login_to_read', $post->login_to_read); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_sitesubscription')) { ?>
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS', 'send_notification_emails', true); ?>

			<?php echo $this->html('composer.field.toggler', 'send_notification_emails', $post->send_notification_emails, [
							'disabled' => EB::isSiteAdmin() || $this->acl->get('change_setting_subscription') ? false : true,
							'disabledTitle' => 'COM_EB_COMPOSER_SETTING_NOT_ALLOWED_POPUP_TITLE',
							'disabledDesc' => 'COM_EB_COMPOSER_SETTING_NOT_ALLOWED_POPUP_DESC'
						]); ?>
		</div>
		<?php } ?>
	</div>
</div>
<?php } else { ?>
	<?php echo $this->fd->html('form.hidden', 'created', $post->getFormDateValue('created')); ?>
	<?php echo $this->fd->html('form.hidden', 'publish_up', $post->getFormDateValue('publish_up')); ?>
	<?php echo $this->fd->html('form.hidden', 'publish_down', $post->publish_down != EASYBLOG_NO_DATE ? $post->getFormDateValue('publish_down') : ''); ?>
	<?php echo $this->fd->html('form.hidden', 'frontpage', $post->frontpage ? 1 : 0); ?>
<?php } ?>
