jQuery(document).ready(function() {

FD.require()
.script('vendor/popper')
.done(function() {


EasyBlog.require()
.script("composer/composer")
.done(function($) {


window.ezb.appearance = "<?php echo $appearance;?>";

// Apply class on root element
$(document.documentElement).switchClass('si-theme--' + window.ezb.appearance);

// prevent all anchor from click when this is a webview page.
<?php if ($isWebview) { ?>
$("a").click(function (e) {
	e.preventDefault();
});
<?php } ?>

class RangeRef {
	constructor() {
		this.updateRect();

		var self = this;

		var update = function(event) {
			var updatePopper = function() {
				var selection = document.getSelection();

				self.range = selection && selection.rangeCount && selection.getRangeAt(0);
				self.updateRect();
			};

			// Immediately update the popper location
			if (event.type == 'scroll') {
				updatePopper();
				return;
			}

			setTimeout(updatePopper, 300);
		}

		$(document).on('mouseup', '[data-ebd-block-content] [contenteditable]', update);
		$(document).on('keydown', '[data-ebd-block-content] [contenteditable]', $.debounce(function(event) {
			update(event);
		}, 300));

		// Fix issue with touch to hold not triggering format bar on mobile. #3036
		if (window.ezb.mobile) {

			var interval;
			var timerValue = 0;

			$(document).on('touchstart', '[data-ebd-block-content] [contenteditable]', function(event) {

				// Always clear the interval
				clearInterval(interval);

				// start timer
				interval = setInterval(() => {
					timerValue++

					if (timerValue > 5) {
						update(event);
						clearInterval(interval);
						timerValue = 0;
					}
				}, 100);
			});

			$(document).on('touchend', '[data-ebd-block-content] [contenteditable]', function(event) {
				clearInterval(interval);
				timerValue = 0;
			});
		}

		// Scrolling on the viewport. Only for desktop view for now. #2957
		if (!window.eb.mobile) {
			$('[data-eb-composer-viewport-content]').on('scroll', update);
		}
	}

	updateRect() {
		this.rect = {
			top: 0,
			left: 0,
			right: 0,
			bottom: 0,
			width: 0,
			height: 0
		};

		if (this.range) {
			this.rect = this.range.getBoundingClientRect();
		}

		this.rectChangedCallback(this.rect);
	}

	rectChangedCallback() {
	}

	getBoundingClientRect() {
		return this.rect;
	}

	clientWidth() {
		return this.rect.width;
	}

	clientHeight() {
		return this.rect.height;
	}
}

var pop = $("[data-composer-fonts]");
var rangeRef = new RangeRef();
var popperPlacement = window.eb.mobile ? "bottom" : "top";

var popper = Popper.createPopper(rangeRef, pop[0], {
	placement: popperPlacement,
	modifiers: [
		{
			name: 'offset',
			options: {
				offset: [0, 5]
			}
		}
	]
});

var i = 1;

rangeRef.rectChangedCallback = (range) => {
	var width = parseInt(range.width);
	var immediateChild = pop.children();

	immediateChild.removeClass('popper--visible');

	if (width <= 0) {
		return;
	}

	i++;

	popper.update();
	immediateChild.addClass('popper--visible');
};

});



});


});
