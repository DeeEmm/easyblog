
<?php
	$initOneSignal = true;
	if (class_exists('Komento')) {
		$kmtConfig = Komento::config();
		if ($kmtConfig->get('onesignal_enabled')) {
			$initOneSignal = false;
		}
	}
?>
<?php if ($initOneSignal) { ?>

EasyBlog.require()
.script('https://cdn.onesignal.com/sdks/OneSignalSDK.js')
.done(function($) {

	var OneSignal = window.OneSignal || [];
	OneSignal.push(["init", {
		appId: "<?php echo $this->config->get('onesignal_app_id');?>",

		<?php if ($subdomain) { ?>
		subdomainName: '<?php echo $subdomain;?>',
		<?php } ?>

		<?php if ($this->config->get('onesignal_safari_id')) { ?>
		safari_web_id: "<?php echo $this->config->get('onesignal_safari_id');?>",
		<?php } ?>
		autoRegister: true,
		notifyButton: {
			enable: false
		},
		welcomeNotification: {
			<?php if ($this->config->get('onesignal_show_welcome')) { ?>
				"title": "<?php echo JText::_('COM_EASYBLOG_ONESIGNAL_WELCOME_TITLE', true);?>",
				"message": "<?php echo JText::_('COM_EASYBLOG_ONESIGNAL_WELCOME_MESSAGE', true);?>"
			<?php } else { ?>
				disable: true
			<?php } ?>
		},

		// Popup
		promptOptions: {
			actionMessage: "<?php echo JText::_('COM_EASYBLOG_ONESIGNAL_PERMISSION_POPUP', true);?>",
			acceptButtonText: "<?php echo JText::_('COM_EASYBLOG_ONESIGNAL_BUTTON_ALLOW', true);?>",
			cancelButtonText: "<?php echo JText::_('COM_EASYBLOG_ONESIGNAL_BUTTON_CANCEL', true);?>"
		}
	}]);


	OneSignal.push(function() {

		OneSignal.getTags(function(tags) {

			<?php if (isset($this->my) && $this->my->id) { ?>
				OneSignal.push(['sendTags', {
					"id": "<?php echo $this->my->id;?>",
					"type": "user"
				}]);
			<?php } else { ?>
				if (tags.id == undefined) {
					OneSignal.push(['sendTags', {
						"id": "0",
						"type": "guest"
					}]);
				}
			<?php } ?>

		});
	});
});

<?php } ?>
