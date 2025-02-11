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
	<width>480</width>
	<height>230</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]",
		"{suggest}" : "[data-author-suggest]"
	}
	</selectors>
	<bindings type="javascript">
	{
		init: function() {
			this.suggest().implement(EasyBlog.Controller.Author.Suggest, {
				"exclusion": <?php echo json_encode($users); ?>
			});
		},

		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			this.form().submit()
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_INVITE_MEMBERS'); ?></title>
	<content>
		<p class="mt-5">
			<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_INVITE_MEMBERS_DESC');?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<div class="textboxlist controls disabled" data-author-suggest>
				<input type="text" autocomplete="off" disabled class="participants textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_('COM_EASYBLOG_TEABMLOG_INVITE_MEMBERS_PLACEHOLDER');?>" />
			</div>
			<input type="hidden" name="teamId" value="<?php echo $teamId; ?>">
			<?php echo $this->fd->html('form.action', 'teamblogs.addMember'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CANCEL_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_TEAMBLOG_INVITE_MEMBERS'); ?>
	</buttons>
</dialog>
