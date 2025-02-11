<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod-easyblogpostmap<?php echo $modules->getWrapperClass(); ?>" data-eb-module-postmap>
	<div id="locationMap" class="locationMap" style="width:<?php echo $params->get('fluid', true) ? '100%' : $mapWidth.'px'; ?>; height: <?php echo $mapHeight; ?>px;"></div>
</div>
<script type="text/javascript">
<?php if ($config->get('location_service_provider') != 'osm') {  ?>
	EasyBlog.require()
	.script('site/location', 'site/vendors/ratings')
	.done(function($) {

		$("[data-eb-module-postmap]").implement("EasyBlog.Controller.Location.Map", {
			language: "<?php echo $language; ?>",
			gMapsKey: "<?php echo $gMapsKey; ?>",
			zoom: <?php echo $zoom; ?>,
			fitBounds: <?php echo $fitBounds; ?>,
			useStaticMap: false,
			disableMapsUI: <?php echo $mapUi; ?>,
			locations: <?php echo json_encode($locations); ?>,
			enableClusterer: <?php echo $enableMarkerClusterer; ?>
		});
	});

<?php } else { ?>
	EasyBlog.require()
	.library('leaflet')
	.script('site/vendors/ratings')
	.done(function($) {
		osm = L.map('locationMap');
		osm.fitWorld();
		var locations = <?php echo json_encode($locations); ?>

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			minZoom: 1,
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(osm);

		var bounds = new L.LatLngBounds();
		var infoWindow = new Array();

		$.each(locations, function(index, location) {
			var infoWindow = L.popup().setContent(location.content);
			var marker = L.marker([location.latitude, location.longitude])
				.addTo(osm)
				.bindPopup(infoWindow)

			marker.addTo(osm);
			bounds.extend(marker.getLatLng());
		});

		osm.fitBounds(bounds, {'maxZoom': <?php echo $osm_zoom; ?>});
		osm.on('popupopen', function() {
			$('[data-eb-module-postmap] [data-rating-form]').implement(EasyBlog.Controller.Ratings);
		});
	});
<?php } ?>
</script>
