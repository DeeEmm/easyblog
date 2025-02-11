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
<dialog>
	<width>400</width>
	<height>180</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{submitButton} click": function() {
			this.submitButton().attr('disabled', "true");
			this.submitButton().html('<?php echo JText::_('COM_EASYBLOG_REPORT_SUBMIT', true);?>');
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_REPORT_THIS_BLOG_POST'); ?></title>
	<content>
		<form name="reportForm" data-form-response action="<?php echo JRoute::_('index.php');?>" method="post">
			<p>
				<?php echo JText::_('COM_EASYBLOG_REPORTS_INFO');?>
			</p>
			<div class="mt-20">
				<textarea class="form-control input-sm" name="reason" id="reason" style="width:100%" placeholder="<?php echo JText::_('COM_EASYBLOG_REPORTS_SPECIFY_REASON'); ?>"></textarea>
			</div>
					
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="type" value="<?php echo $type;?>" />
			<?php echo $this->fd->html('form.action', 'reports.submit'); ?>
		</form>

	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_REPORT'); ?>
	</buttons>
</dialog>
