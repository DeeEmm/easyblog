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
	<height>150</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-close-button]",
		"{form}": "[data-autopost-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_AUTOPOST_DIALOG_INFO');?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-autopost-form>
			<p><?php echo JText::sprintf('COM_EASYBLOG_AUTOPOST_DIALOG_DESC', $type); ?></p>

			<div class="checkbox t-mt--md">
				<input type="checkbox" id="force" name="force" value="1" />
				<label for="force">
					<?php echo JText::_('Force Autoposting. This will try to force EasyBlog to try to repost again eventhough it has already been shared before');?>
				</label>
			</div>

			<input type="hidden" name="cid[]" value="<?php echo $id;?>" />
			<input type="hidden" name="type" value="<?php echo $type;?>" />

			<?php echo $this->fd->html('form.action', 'blogs.autopost'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_SHARE_BUTTON'); ?>
	</buttons>
</dialog>
