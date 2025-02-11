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

<?php echo $this->fd->html('email.heading', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_MODERATE_HEADING', 'COM_EASYBLOG_MAIL_TEMPLATE_NEW_COMMENT_MODERATE_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? JText::sprintf('COM_EB_MAIL_TEMPLATE_NEW_COMMENT_MODERATE_MESSAGE', 'John Doe', '13th August 2017', 'test post', 'http://test.com/') : JText::sprintf('COM_EB_MAIL_TEMPLATE_NEW_COMMENT_MODERATE_MESSAGE', $commentAuthor, $commentDate, $blogTitle, $blogLink), 'clear'); ?>

<?php echo $this->fd->html('email.content', $templatePreview ? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s' : $commentContent, 'box'); ?>

<?php echo $this->fd->html('email.spacer'); ?>

<!-- [if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
	<table role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:0;padding-bottom:0px;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:235px;">
			<![endif]-->
			<div class="mj-column-px-235 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_NOTIFICATION_REJECT_COMMENT', $templatePreview ? 'javascript:void(0);' : $rejectLink, 'danger'); ?>
			</div>
			<!--[if mso | IE]>
			</td>
			<td style="vertical-align:top;width:235px;">
			<![endif]-->
			<div class="mj-column-px-235 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<?php echo $this->fd->html('email.button', 'COM_EASYBLOG_NOTIFICATION_APPROVE_COMMENT', $templatePreview ? 'javascript:void(0);' : $approveLink, 'primary'); ?>
			</div>
			<!--[if mso | IE]>
			</td></tr></table>
			<![endif]-->
		</td>
	</tr>
	</tbody>
	</table>
</div>

<!--[if mso | IE]>
</td></tr></table>
<![endif]-->
