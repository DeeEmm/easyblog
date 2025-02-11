<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div>
    <?php if ($image) { ?>
        <img src="<?php echo $image;?>" style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;" />
    <?php } ?>
    <p><?php echo $content;?></p>
    
    <div style="clear:both;"></div>

    <div style="text-align: right;">
        <a href="<?php echo $permalink;?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING'); ?></a>
    </div>
</div>