<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<p>
    <a class="eb-thumb-preview easyblog-thumb-preview" href="<?php echo $url;?>" data-popup="<?php echo $url;?>" title="<?php echo $this->fd->html('str.escape', $title);?>">
        <img width="<?php echo $this->config->get('main_image_thumbnail_width');?>" title="<?php echo $this->fd->html('str.escape', $title);?>" alt="<?php echo $this->fd->html('str.escape', $title);?>"
            src="<?php echo $url;?>" />
    </a>
</p>
