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
	<width>500</width>
	<height>200</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click" : function() {
			this.parent.close();
		},
		"{submitButton} click" : function() {
			EasyBlog.ajax('site/controllers/bloggers/downloadGDPR', {
				"userId" : "<?php echo $userId;?>"
			}).done(function(result) {
				EasyBlog.dialog({
					"content": result
				});
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_INFORMATION'); ?></title>
	<content>
		<p class="mt-5"><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_DESC1');?></p>
		<p class="mt-5"><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_DESC2');?></p>
		<p class="mt-5"><?php echo JText::sprintf('COM_EB_GDPR_EMAIL', $email);?></p>

	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EB_GDPR_SUBMIT'); ?>
	</buttons>
</dialog>
