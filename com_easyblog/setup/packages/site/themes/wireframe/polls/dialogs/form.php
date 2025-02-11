<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>700</width>
	<height>600</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{saveButton}": "[data-save-button]",
		"{closeAlertButton}": "[data-poll-error] [data-fd-dismiss=alert]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{closeAlertButton} click": function(el, args) {
			el = $(el);
			var event = args[0];

			event.preventDefault();
			event.stopPropagation();

			// Hide the error back
			el.closest('[data-poll-error]').addClass('t-hidden');
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_POLL_DIALOG_FORM_TITLE'); ?></title>
	<content>
		<div class="t-hidden" data-poll-error>
			<?php echo $this->fd->html('alert.standard', '', 'danger'); ?>
		</div>

		<?php echo $html; ?>
	</content>
	<buttons alignment="center">
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', $saveButtonTitle, 'primary', [
			'attributes' => 'data-save-button'
		]); ?>
	</buttons>
</dialog>
