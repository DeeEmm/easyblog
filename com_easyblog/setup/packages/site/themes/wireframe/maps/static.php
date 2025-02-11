<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<a class="eb-entry-location-map"
    target="_BLANK"
	href="<?php echo $mapUrl; ?>"
	style="background-image:url(<?php echo $mapUrl;?>);
    height:<?php echo $this->config->get('main_locations_blog_map_height');?>px;">
</a>

<a class="eb-entry-location-address row-table" href="<?php echo $mapUrl; ?>" target="_blank">
	<div class="col-cell"><i class="fdi fa fa-map-marker text-muted"></i></div>
	<div class="col-cell">
		<?php echo $post->address;?>
	</div>
</a>