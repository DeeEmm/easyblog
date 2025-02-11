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

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once ($engine);

if (!EB::isFoundryEnabled()) {
	return;
}

$modules = EB::modules($module);

$limit = (int) trim($params->get('count', 0));
if ($limit < 0) {
	$limit = 5;
}

$filter = $params->get('excludeemptypost', 0)? 'showbloggerwithpost' : 'showallblogger';
$sort = $params->get('ordertype', 'latest');
$featuredOnly = $params->get('onlyfeatured', 0);

$model = EB::model('blogger');
$bloggers = $model->getBloggers($sort, $limit, $filter, '', array(), array(),$featuredOnly, true);

if (!$bloggers) {
	return;
}

foreach ($bloggers as $row) {

	$blogger = EB::user($row->id);
	$row->profile = $blogger;

	$biography = $blogger->getBiography();
	$biographyTotal = EBString::strlen(strip_tags($biography));
	$bioLength = (int) $params->get('bio_length', 50);

	if ($biographyTotal > $bioLength) {
		$biography = EBString::substr($biography, 0, $bioLength) . '...';
	}

	$row->biography = $biography;

	$bloggerwebsite = '<a href="' . $blogger->getWebsite() . '" target="_blank">' . $blogger->getWebsite() . '</a>';

	if ($params->get('nofollowadd', true)) {
		$bloggerwebsite = '<a href="' . $blogger->getWebsite() . '" target="_blank" rel="nofollow">' . $blogger->getWebsite() . '</a>';
	}

	$row->bloggerwebsite = $bloggerwebsite;

}


require($modules->getLayout());
