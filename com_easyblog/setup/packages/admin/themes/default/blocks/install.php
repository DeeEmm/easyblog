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
<form name="adminForm" id="adminForm" class="themesForm" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYBLOG_BLOCKS_INSTALL_UPLOAD');?></b>
					<p><?php echo JText::sprintf('COM_EASYBLOG_BLOCKS_INSTALL_UPLOAD_DESC', '<a href="https://stackideas.com/docs/easyblog/administrators/composing/composer-blocks" target="_blank">', '</a>');?></p>
				</div>

				<div class="panel-body">
					<input type="file" name="package" id="package" class="input" style="width:265px;" deasata-uniform />
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->fd->html('form.action', 'blocks.upload'); ?>
</form>
