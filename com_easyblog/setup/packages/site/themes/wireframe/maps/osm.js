EasyBlog.require()
.library('leaflet')
.done(function($) {
	osm = L.map('map-<?php echo $uid; ?>', {
		zoom: 12
	});

	osm.fitWorld();

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		minZoom: 1,
		maxZoom: 19,
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(osm);

	var latlng = {
		lat: parseFloat(<?php echo $post->latitude; ?>),
		lng: parseFloat(<?php echo $post->longitude; ?>)
	}

	// if (marker !== undefined) {
	// 	osm.removeLayer(marker);
	// }

	osm.flyTo(latlng, 10, {
		"duration": 3
	});

	marker = L.marker(latlng).addTo(osm);
});
