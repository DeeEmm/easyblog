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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

if (!EB::isFoundryEnabled()) {
	return;
}

require_once(__DIR__ . '/helper.php');
require_once(__DIR__ . '/location.php');

// Load up our library
$modules = EB::modules($module);

// @5.1
// Backward compatibility
$config = $modules->config;

if (!$config->get('main_locations')) {
	return;
}

$my = JFactory::getUser();

$helper = new modEasyBlogPostMapHelper($modules);

// Retrieve posts
$posts = $helper->getPosts();
$totalPosts = count($posts);

// When there is no posts, just skip this
if (!$posts) {
	return;
}

// Sort the posts
$posts = $helper->sortLocation($posts);
$locations = array();

if ($posts) {
	// always store first location
	$locations[] = new modEasyBlogMapLocation($posts[0]);

	// store previous post by reference
	$previousPost = $locations[0];

	// start from second location to check
	for ($i = 1; $i < $totalPosts; $i++) {
		$post = $posts[$i];
		$postObj = new modEasyBlogMapLocation($post);

		if ($helper->sameLocation($post, $previousPost)) {
			$previousPost->content .= $postObj->content;
			$previousPost->ratingid[] = $postObj->id;
		} else {
			$locations[] = $postObj;
			$previousPost = $locations[count($locations) - 1];
		}
	}
}

if (!$locations) {
	return;
}
$osm_zoom = $params->get('osm_zoom', 15);
$language = $params->get('language', 'en');
$zoom = $params->get('zoom', 15);
$fitBounds = $params->get('fitbounds', 1);
$mapUi = $params->get('mapui', 0) == 1? "false" : "true";
$mapWidth = $params->get('mapwidth');
$mapHeight = $params->get('mapheight');
$enableMarkerClusterer = $params->get('enableMarkerClusterer', 0) == 1? "true" : "false";

// Google Maps API Key
$gMapsKey = $config->get('googlemaps_api_key');

// Generate a unique uid for this module
$uid = uniqid();

require($modules->getLayout());

