<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<base href="<?php echo $baseUrl;?>" />
	<title><?php echo JText::_('COM_EB_GDPR_YOUR_INFORMATION'); ?></title>
	<style>
		html {
			font-size: 100%;
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
			font-family:sans-serif
		}
		body {
			font-size: 1em;
			line-height: 1.2;
			color: #444;
		}
		html, body {
			margin: 0;
			padding: 0;
			height: 100%;
		}
		a {
			text-decoration: none;
			color: #007bff;
		}
		a:active,a:hover{outline:0;color: #0056b3;}
		.container-wrapper {
			max-width: 900px;
			height: 100%;
			margin: 0 auto;
		}
		.gdpr-container {
			display: table;
			width: 100%;
			height: 100%;
			position: relative;
		}
		.gdpr-container:before {
			position: absolute;
			content: '';
			background-color: #f5f5f5;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			display: block;
		}
		.gdpr-container__nav {
			display: table-cell;
			width: 220px;
			padding: 20px;
			background: #f5f5f5;
		}
		.gdpr-container__content {
			display: table-cell;
			padding: 20px;
		}

		@media (max-width: 720px) {
			.gdpr-container,
			.gdpr-container__nav,
			.gdpr-container__content {
				display: block;
				width: 100%;
				padding: 0;
			}
			.gdpr-content {
				padding: 20px;
			}
		}
		.gdpr-title {
			font-weight: bold;
			padding: .5rem 1rem;
			margin-bottom: .2rem;
		}
		.gdpr-nav {
			list-style: none;
			margin: 0;
			padding: 0;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
		}
		.gdpr-nav li {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			width: 100%;
		}
		.gdpr-nav li a {
			display: block;
			width: 100%;
			padding: .3rem 1rem;

			/*color: #007bff;*/
			background-color: transparent;
		}
		.gdpr-content {
			line-height: 1.5;
		}
		.gdpr-content div {
			padding: 2px 5px;
		}
		.gdpr-content div:nth-child(even) {
			background: #f5f5f5;
		}
		.gdpr-content div:nth-child(odd) {
			background: #ffffff;
		}

		.gdpr-content table tr:nth-child(even) {
			background: #f5f5f5;
		}
		.gdpr-content table tr:nth-child(odd) {
			background: #ffffff;
		}

		.gdpr-content table td.left {
			text-align: right;
			vertical-align: top;
		}

	</style>
</head>

<body>
	<div class="container-wrapper">
		<div class="gdpr-container">
				<div class="gdpr-container__nav">
					<div class="gdpr-side">
						<div class="gdpr-title"><?php echo JText::_('COM_EB_GDPR_YOUR_INFORMATION'); ?></div>
						<?php echo $sidebar; ?>
					</div>
				</div>

				<div class="gdpr-container__content">
					<div class="gdpr-content">
						<?php if ($hasBack) { ?>
						<div><a href="javascript:history.go(-1);"><?php echo JText::_('COM_EASYBLOG_BACK_BUTTON'); ?></a></div>
						<?php } ?>

						<?php echo $contents; ?>
					</div>
				</div>
		</div>
	</div>
</body>
</html>
