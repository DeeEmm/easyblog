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
?>
<div id="eb" class="eb-mod mod-easyblogbloggercloud<?php echo $modules->getWrapperClass();?>" data-eb-module-bloggercloud>
	<?php if ($bloggers) { ?>
		<?php foreach ($bloggers as $blogger) { ?>
		  <a style="font-size: <?php echo floor($blogger->fontsize); ?>px;" class="blogger-cloud" href="<?php echo $blogger->getPermalink() ?>"><?php echo JText::_($blogger->nickname); ?></a>
		<?php } ?>
	<?php } else { ?>
		<?php echo JText::_('MOD_EBBLOGGERCLOUD_NO_POST'); ?>
	<?php } ?>
</div>