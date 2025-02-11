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
<div class="eb-pdf-viewer">
	<iframe src="<?php echo $url;?>" width="100%" height="<?php echo $block->data->height;?>" scrolling="no" frameborder="0" allowTransparency="true">
		<a href="<?php echo $block->data->url;?>"><?php echo JText::_('COM_EASYBLOG_DOWNLOAD_PDF_FILE');?></a>
	</iframe>
</div>
