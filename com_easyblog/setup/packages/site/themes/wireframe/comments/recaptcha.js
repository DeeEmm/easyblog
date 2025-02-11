EasyBlog.ready(function($) {

<?php if ($invisible) { ?>
window.recaptchaDfd = $.Deferred();

window.getResponse = function() {
 
	var token = grecaptcha.getResponse();
	var responseField = $('[data-eb-recaptcha-response]');

	if (token) {
		responseField.val(token);

		window.recaptchaDfd.resolve();

		return;
	}

	grecaptcha.reset();

	window.recaptchaDfd.reject();
};

$('[data-eb-comments]').on('onSaveComment', function(event, save) {
	save.push(window.recaptchaDfd);

	grecaptcha.execute();
});

// Get the value of the recaptcha response
$('[data-eb-comments]').on('submitComment', function(event, data) {
	data.recaptcha = $('[data-eb-recaptcha-response]').val();
});

<?php } else { ?>

$('[data-eb-comments]').on('submitComment', function(event, data) {
	data.recaptcha = grecaptcha.getResponse();
});

<?php } ?>

$('[data-eb-comments]').on('reloadCaptcha', function() {

	setTimeout(function() {
		grecaptcha.reset();

		window.recaptchaDfd = $.Deferred();
	}, 500);
});

// Create recaptcha task
var task = [
	'recaptcha_<?php echo $uid;?>', {
		'sitekey': '<?php echo $key;?>',
		'theme': '<?php echo $color;?>'
	}
];

var runTask = function() {

	<?php if (!$invisible) { ?>
		grecaptcha.render.apply(grecaptcha, task);
	<?php } ?>

	<?php if ($invisible) { ?>

	// Support infinite scroll where multiple invisible captcha is rendered
	var invisibleCaptchaIndex = $('[data-eb-recaptcha-invisible]').length - 1;
	var element = $('[data-eb-recaptcha-invisible]')[invisibleCaptchaIndex];

	// Invisible captcha
	if (!window.JoomlaInitReCaptcha2 || (window.JoomlaInitReCaptcha2 && invisibleCaptchaIndex != 0)) {
		grecaptcha.render(element, {
					"sitekey": "<?php echo $key;?>",
					"callback": getResponse
		});
	}
	<?php } ?>
}

// If grecaptcha is not ready, add to task queue
if (!window.grecaptcha || (window.grecaptcha && !window.grecaptcha.render)) {
	var tasks = window.recaptchaTasks || (window.recaptchaTasks = []);
	tasks.push(task);
// Else run task straightaway
} else {
	runTask(task);
}

// If recaptacha script is not loaded
if (!window.recaptchaScriptLoaded && (!window.grecaptcha || (window.grecaptcha && !window.grecaptcha.render))) {

	if (window.JoomlaInitReCaptcha2) {
		// joomla recaptcha already loaded. let ride ontop of JoomlaInitReCaptcha2 callback.
		var joomlaRecaptcha = window.JoomlaInitReCaptcha2;

		// reset
		window.JoomlaInitReCaptcha2 = function() {
			var task;

			// execute our task.
			while (task = tasks.shift()) {
				runTask(task);
			};

			// now we execute joomla callback.
			$(joomlaRecaptcha);
		};
	} else {

		// Load the recaptcha library
		EasyBlog.require()
			.script("//www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=<?php echo $language;?>");

		window.recaptchaCallback = function() {
			var task;

			while (task = tasks.shift()) {
				runTask(task);
			}
		};

	}

	window.recaptchaScriptLoaded = true;
}

});
