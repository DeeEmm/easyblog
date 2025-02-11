<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogMaps extends EasyBlog
{
	/**
	 * Renders the maps for a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function html(EasyBlogPost &$post)
	{
		static $loaded = false;

		if (!$post->hasLocation()) {
			return;
		}

		// Generate a unique id
		$uid = uniqid();

		$mapIntegration = $this->config->get('location_service_provider');

		if ($mapIntegration == 'osm') {

			$template = EB::themes();
			$template->set('uid', $uid);
			$template->set('post', $post);

			$output = $template->output('site/maps/osm');

			return $output;
		}

		$language = $this->config->get('main_locations_blog_language');
		$gMapkey = $this->config->get('googlemaps_api_key');

		// with Google maps API key
		if (!$loaded && $gMapkey) {
			$this->doc->addScript('https://maps.googleapis.com/maps/api/js?key=' . $gMapkey . '&language=' . $language);
		}

		// without Google maps API key
		if (!$loaded && !$gMapkey) {
			$this->doc->addScript('https://maps.googleapis.com/maps/api/js?sensor=true&language=' . $language);
		}

		// Get the map configuration
		$static = $this->config->get('main_locations_static_maps');
		$type = $this->config->get('main_locations_map_type');
		$maxZoom = $this->config->get('main_locations_max_zoom_level');
		$minZoom = $this->config->get('main_locations_min_zoom_level');
		$defaultZoom = $this->config->get('main_locations_default_zoom_level', '17');

		$namespace = 'site/maps/static';
		$mapUrl = "https://maps.googleapis.com/maps/api/staticmap?center=" . $post->latitude . "," . $post->longitude . "&size=1280x1280&markers=color:red|label:S|" . $post->latitude . "," . $post->longitude . "&key=" . $gMapkey;

		if (!$this->config->get('main_locations_static_maps')) {
			$namespace = 'site/maps/interactive';
			$mapUrl = "https://www.google.com/maps/embed/v1/place?key=" . $gMapkey . "&q=" . str_replace(' ', '%20', $post->address);

			// For interactive maps, only two kind of map type is supported, roadmap and satellite.
			$mapType = 'ROADMAP';

			if ($type == 'SATELLITE') {
				$mapType = $type;
			}

			$type = $mapType;
		}

		$additonalParams = "&language=" . $language . "&maptype=" . strtolower($type) . "&zoom=" . $defaultZoom;
		$mapUrl = $mapUrl . $additonalParams;

		$template = EB::themes();
		$template->set('uid', $uid);
		$template->set('defaultZoom', $defaultZoom);
		$template->set('minZoom', $minZoom);
		$template->set('maxZoom', $maxZoom);
		$template->set('defaultZoom', $defaultZoom);
		$template->set('type', $type);
		$template->set('language', $language);
		$template->set('post', $post);
		$template->set('gMapkey', $gMapkey);
		$template->set('mapUrl', $mapUrl);		

		$output = $template->output($namespace);

		return $output;
	}
}
