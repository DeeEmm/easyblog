<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod_easyblogsubscribe<?php echo $modules->getWrapperClass();?>" data-eb-module-subscribe data-eb-subscribe-uid="<?php echo $uid; ?>">
<?php if ($params->get('type' , 'link') == 'link') { ?>
	<a href="javascript:void(0);" data-blog-subscribe data-id="<?php echo $id;?>" data-type="<?php echo $type; ?>" class="btn btn-primary btn-block <?php echo $subscribed ? 'hide' : ''; ?>"><?php echo JText::_('MOD_SUBSCRIBE_MESSAGE_' . strtoupper($type));?></a>

	<a href="javascript:void(0);" data-blog-unsubscribe data-subscription-id="<?php echo $subscribed;?>" data-type="<?php echo $type; ?>" data-return="<?php echo $return;?>" class="btn btn-danger btn-block <?php echo $subscribed ? '' : 'hide'; ?>"><?php echo JText::_('MOD_UNSUBSCRIBE_MESSAGE_' . strtoupper($type));?></a>
<?php } else { ?>

	<form name="subscribe-blog" id="subscribe-blog-module" method="post" class="eb-mod-form">
		<div class="eb-mod-form-item form-group">
			<label for="eb-subscribe-fullname">
				<?php echo JText::_('MOD_EASYBLOGSUBSCRIBE_YOUR_NAME'); ?>
			</label>
			<input type="text" name="esfullname" class="form-control" id="eb-subscribe-fullname" data-eb-subscribe-name />
		</div>

		<div class="eb-mod-form-item form-group">
			<label for="eb-subscribe-email">
				<?php echo JText::_('MOD_EASYBLOGSUBSCRIBE_YOUR_EMAIL'); ?>
			</label>
			<input type="text" name="email" class="form-control" id="eb-subscribe-email" data-eb-subscribe-mail />
		</div>

		<?php if ($registration && $my->guest) { ?>
		<div class="form-group">
			<label for="eb-subscribe-username">
				<?php echo JText::_('MOD_EASYBLOGSUBSCRIBE_YOUR_USERNAME'); ?>
			</label>
			<input type="text" name="esusername" class="form-control" id="eb-subscribe-username" data-eb-subscribe-username />
		</div>

		<div class="eb-mod-form-item form-group" data-eb-subscribe-register>
			<div class="eb-checkbox">
				<input type="checkbox" id="eb-subscribe-register-<?php echo $module->id; ?>" name="eb-subscribe-register" value="1">
				<label for="eb-subscribe-register-<?php echo $module->id; ?>">
					<?php echo JText::_('MOD_EASYBLOGSUBSCRIBE_REGISTER_AS_SITE_MEMBER'); ?>
				</label>
			</div>
		</div>
		<?php } ?>

		<?php if ($modules->config->get('main_subscription_agreement')) { ?>
		<div class="eb-mod-form-item form-group" data-eb-subscribe-terms>
			<div class="eb-checkbox">
				<input type="checkbox" id="agreementModule-<?php echo $module->id; ?>" name="agreement" value="1">
				<label for="agreementModule-<?php echo $module->id; ?>">
					<?php echo JText::_($modules->config->get('main_subscription_agreement_message')); ?>
				</label>
			</div>
		</div>
		<?php } ?>

		<div class="eb-mod-form-action">
			<a href="javascript:void(0);" class="btn btn-primary" data-subscribe-link><?php echo JText::_('MOD_SUBSCRIBE_MESSAGE_' . strtoupper($type));?></a>
		</div>
	</form>

	<script type="text/javascript">
	EasyBlog.ready(function($){

		var uid = "<?php echo $uid; ?>";
		var subscribeModuleWrapper = $('[data-eb-module-subscribe]' + '[data-eb-subscribe-uid="' + uid + '"]');
		var subscribeInlineFormButton = subscribeModuleWrapper.find('[data-subscribe-link]');

		$(subscribeInlineFormButton).on("click", function() {
			var type = '<?php echo $type; ?>';
			var id = '<?php echo $id; ?>';

			var nameInput = subscribeModuleWrapper.find('[data-eb-subscribe-name]');
			var mailInput = subscribeModuleWrapper.find('[data-eb-subscribe-mail]');
			var usernameInput = subscribeModuleWrapper.find('[data-eb-subscribe-username]');

			var name = $.trim(nameInput.val());
			var mail = $.trim(mailInput.val());
			var username = $.trim(usernameInput.val());

			var hasError = false;

			$('.form-group').removeClass('has-error');

			if (name == "") {
				nameInput.parents('.form-group').addClass("has-error");
				hasError = true;
			}

			if (mail == "") {
				mailInput.parents('.form-group').addClass("has-error");
				hasError = true;
			}

			<?php if ($modules->config->get('main_subscription_agreement')) { ?>

				var termsWrapper = subscribeModuleWrapper.find('[data-eb-subscribe-terms]');
				var termsInput = termsWrapper.find('input[type=checkbox]');
				var agreed = $(termsInput).is(':checked');

				if (!agreed) {
					termsWrapper.addClass('has-error');
					hasError = true;
				}
			<?php } ?>

			if (hasError) {
				return false;
			}

			// Determine whether the user want to register through this subscription process or not
			var registerWrapper = subscribeModuleWrapper.find('[data-eb-subscribe-register]');
			var registerInput = registerWrapper.find('input[type=checkbox]');
			var agreedRegister = registerInput.is(':checked');

			EasyBlog.dialog({
				content: EasyBlog.ajax('site/views/subscription/subscribe', {
					"type": type,
					"id": id,
					"name": name,
					"email": mail,
					"username": username,
					"userId": "<?php echo $my->id;?>",
					"register": agreedRegister ? 1 : 0

				})
			});
		});
	});
	</script>
<?php } ?>
</div>
