<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-link-preview="" class="eb-blocks-link">
    <div class="media-table">

        <?php if ($data->showImage) { ?>
        <a href="<?php echo $data->url;?>" class="media-thumb" target="_blank">
            <img width="150" height="150" alt="<?php echo $this->fd->html('str.escape', $data->title);?>" class="media-object" src="<?php echo $data->image;?>" />
        </a>
        <?php } ?>

        <div class="media-body">
            <h4 class="media-heading">
                <a href="<?php echo $data->url;?>"><?php echo $data->title;?></a>
            </h4>

            <div data-preview-content="" class="media-content">
                <?php echo $data->content;?>
            </div>

            <a href="<?php echo $data->url;?>" target="_blank" class="media-link"><?php echo $data->url;?></a>
        </div>
    </div>
</div>
