<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod-easyblogtagcloud<?php echo $modules->getWrapperClass();?>" data-eb-module-tagcloud>
    <?php if ($tags) { ?>
    <div class="mod-items-compact">
        <?php foreach ($tags as $tag) { ?>
        <div class="mod-item mod-table">
            <div class="mod-cell cell-tight">
                <i class="fdi fa fa-tag mr-5 mod-muted"></i>
            </div>
            <div class="mod-cell">
                <a class="tag-cloud" href="<?php echo $tag->getPermalink();?>"><?php echo JText::_($tag->title); ?></a>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } else { ?>
        <?php echo JText::_('MOD_EASYBLOGTAGCLOUD_NO_TAG'); ?>
    <?php } ?>
</div>
