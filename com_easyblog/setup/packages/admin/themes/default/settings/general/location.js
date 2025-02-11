
EasyBlog.ready(function($){

	var updateLocationPanels = function () {
		var service = $('[data-location-integration]').val();

		// Hide everything
		$('[data-panel-integration]').addClass('hide');

		// Show only what we want the user to see
		$('[data-panel-' + service + ']').removeClass('hide');

		$('[data-google-settings]').toggleClass('hide', service == 'osm');
	}

	updateLocationPanels();

	$(document)
		.on('change.location.integration', '[data-location-integration]', function() {
			updateLocationPanels();
		});
});