<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ebd-block is-standalone is-nested" data-type="file">
	<div class="ebd-block-viewport" data-ebd-block-viewport>
		<div class="ebd-block-content" data-ebd-block-content>

			<div class="eb-block-file row-table" data-responsive="400,300,200,100">
				<div class="col-cell eb-file-thumb cell-tight" data-file-icon>
					<i class="fdi fa fa-file">
						<b data-file-type></b>
					</i>
				</div>
				<div class="col-cell eb-file-details">
					<div>
						<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_FILENAME'); ?></span>
						<span data-file-name><?php echo $file->title;?></span>
					</div>

					<div>
						<span class="text-muted"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_FILESIZE'); ?></span>
						<span data-file-size><?php echo $file->size;?></span>
					</div>

					<a href="javascript:void(0);" data-url="<?php echo $file->url;?>" class="btn btn-default" target="_blank" data-file-url><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_DOWNLOAD_BUTTON'); ?></a>
				</div>
			</div>
			
		</div>
	</div>
</div>