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
<div class="eb-block-file row-table">
	<div class="col-cell eb-file-thumb cell-tight">
		<i class="fdi fa fa-file">
			<b>pdf</b>
		</i>
	</div>
	<div class="col-cell eb-file-details">
		<div>
			<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_FILENAME'); ?></span>
			<span><?php echo $this->fd->html('str.escape', $title); ?></span>
		</div>

		<div>
			<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_FILESIZE'); ?></span>
			<span><?php echo $size; ?></span>
		</div>

		<a href="<?php echo $url;?>" class="btn btn-default" target="_blank"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_DOWNLOAD_BUTTON'); ?></a>
	</div>
</div>
