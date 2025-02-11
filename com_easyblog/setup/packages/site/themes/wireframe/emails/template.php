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
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<title></title>
<!--[if !mso]><!-- -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--<![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
	#outlook a { padding:0; }
	body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
	table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
	img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
	p { display:block;margin:13px 0; }
</style>
<!--[if mso]>
<xml>
<o:OfficeDocumentSettings>
	<o:AllowPNG/>
	<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
<!--[if lte mso 11]>
<style type="text/css">
	.mj-outlook-group-fix { width:100% !important; }
</style>
<![endif]-->

<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" type="text/css">
<style type="text/css">
@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap);
</style>
<!--<![endif]-->

<style type="text/css">
@media only screen and (min-width:320px) {
.mj-column-per-100 { width:100% !important; max-width: 100%; }
.mj-column-px-40 { width:40px !important; max-width: 40px; }
.mj-column-px-400 { width:400px !important; max-width: 400px; }
.mj-column-px-235 { width:235px !important; max-width: 235px; }
.mj-column-px-10 { width:10px !important; max-width: 10px; }
.mj-column-per-15 { width:15% !important; max-width: 15%; }
.mj-column-per-85 { width:85% !important; max-width: 85%; }
.mj-column-per-49 { width:49% !important; max-width: 49%; }
.mj-column-px-10 { width:10px !important; max-width: 10px; }
}
</style>

<style type="text/css">
@media only screen and (max-width:320px) {
table.mj-full-width-mobile { width: 100% !important; }
td.mj-full-width-mobile { width: auto !important; }
}
</style>
</head>
<body style="background-color:#ffffff;">
	<!-- Body -->
	<div style="background-color:#ffffff;">

		<?php echo $this->fd->html('email.logo', $logo); ?>

		<?php echo $contents;?>

		<?php echo $this->fd->html('email.spacer'); ?>

		<?php echo $this->fd->html('email.divider'); ?>

		<?php if (!empty($unsubscribeLink)) { ?>
		<!--[if mso | IE]>
		<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
		<![endif]-->
		<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
			<tbody>
			<tr>
				<td style="direction:ltr;font-size:0px;padding:40px 20px 0;text-align:center;">
					<!--[if mso | IE]>
					<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:480px;">
					<![endif]-->

					<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
						<tbody>
						<tr>
							<td style="background-color:#ffffff;vertical-align:top;padding:0;">
								<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
								<tr>
									<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
										<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;line-height:30px;text-align:left;color:#626262;">
											<?php if ($unsubscribe) { ?>
											<unsubscribe style="color:#4e72e2;">
												<?php if (!is_array($unsubscribe)) { ?>
													<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_UNSUBSCRIBE'); ?> <a href="<?php echo $unsubscribe;?>" style="color:#4e72e2; text-decoration:none;"><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_HERE');?></a>
												<?php } else { ?>
													<?php foreach ($unsubscribe as $type => $link) { ?>
														<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_UNSUBSCRIBE_' . strtoupper($type)); ?>
														<a href="<?php echo $link;?>" style="color:#4e72e2; text-decoration:none;"><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_HERE');?></a><br />
													<?php } ?>
												<?php } ?>
											</unsubscribe>
											<?php } ?>

											<?php if ($deleteInfoRequest) { ?>
											<unsubscribe style="color:#4e72e2;">
												<?php echo JText::_('COM_EB_NOTIFICATION_DELETE_INFO'); ?> <a href="<?php echo $deleteInfoRequest;?>" style="color:#4e72e2; text-decoration:none;"><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_HERE');?></a>
											</unsubscribe>
											<?php } ?>
										</div>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						</tbody>
						</table>
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
		<?php } ?>
	</div>
</body>
</html>
