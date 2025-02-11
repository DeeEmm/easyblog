EasyBlog.ready(function($) {
	var wrapper = $('[data-subscribe-cta]');
	var subscribeButton = wrapper.find('[data-subscribe-button]');
	var errorWrapper = wrapper.find('[data-subscribe-cta-error]');
	var alertWrapperCloned = errorWrapper.find('[data-alert-wrapper]').clone();

	var isDoubleOptIn = <?php echo EB::subscription()->isDoubleOptIn() ? 1 : 0; ?>;
	var isSubscribing = false;

	var showError = function(message, wrapper) {
		wrapper.find('[data-fd-alert-message]').html(message);

		wrapper.removeClass('t-hidden');
	};

	var hideError = function(wrapper) {
		errorWrapper.find('[data-message]').html('');
		errorWrapper.addClass('t-hidden');
	};

	wrapper.find('[data-fd-dismiss=alert]').on('click', function(event) {
		var el = $(this);

		event.preventDefault();
		event.stopPropagation();

		// Hide the error back
		errorWrapper.addClass('t-hidden');
	});

	subscribeButton.on('click', function() {
		if (isSubscribing) {
			return;
		}

		isSubscribing = true;

		hideError(errorWrapper);

		var button = $(this);
		var email = wrapper.find('[data-subscribe-email]');
		var name = wrapper.find('[data-subscribe-name]');

		// Show the loader
		button.addClass('is-loading');

		var options = {
			"type" : "<?php echo $type;?>",
			"uid": "<?php echo $uid;?>",
			"email": email.val(),
			"name": name.val(),
			"userId": "<?php echo $this->my->id; ?>"
		};

		EasyBlog.ajax('site/views/subscription/subscribe', options)
			.done(function(contents, id) {
				EasyBlog.dialog({
					content: contents
				});

				wrapper.remove();

			}).fail(function(message) {
				showError(message, errorWrapper);
			}).always(function() {
				button.removeClass('is-loading');
				isSubscribing = false;
			});
	});
});