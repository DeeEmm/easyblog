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
?>
<div class="eb-nmm-breadcrumb__item" data-mm-breadcrumb-item data-uri="<?php echo $meta->uri;?>" data-key="<?php echo $meta->key;?>">
	<a class="eb-nmm-breadcrumb__link" href="javascript:void(0);"><?php echo $meta->title;?></a>
</div>

<?php if ($meta->uri == 'unsplash') { ?>
<div data-mm-breadcrumb-search>
	<input type="text" class="o-form-control o-form-control--eb-nmm" placeholder="<?php echo JText::_('COM_EB_UNSPLASH_SEARCH_PHOTO_PLACEHOLDER'); ?>" value="<?php echo $meta->query; ?>" data-mm-search />
</div>
<?php } ?>
