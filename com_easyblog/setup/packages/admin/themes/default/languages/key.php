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
<form name="adminForm" id="adminForm" method="post">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYBLOG_API_KEY_REQUIRED');?></b>
					<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_API_KEY_REQUIRED_DESC');?></div>
				</div>

				<div class="panel-body">
					<div class="input-group">
						<input type="text" class="form-control" name="apikey" value="" />
						<div class="input-group-btn">
							<button class="btn btn-default"><i class="fdi fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SAVE_APIKEY_BUTTON'); ?></button>
						</div>
					</div>
					<div class="obtain-key mt-10">
						<a href="https://stackideas.com/dashboard" target="_blank"><?php echo JText::_('COM_EASYBLOG_OBTAIN_API_KEY'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->fd->html('form.action', 'settings.saveApi'); ?>
	<input type="hidden" name="return" value="<?php echo $return;?>" />
</form>
