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
<div class="eb-comment-captcha" data-comment-captcha>
	<p class="hide">
		<b><?php echo JText::_('COM_EASYBLOG_CAPTCHA_COMMENTS_TITLE'); ?></b>
	</p>

	<div class="eb-comment-captcha-form">
		<div class="eb-comment-captcha-img">
			<img src="<?php echo EB::_('index.php?option=com_easyblog&task=captcha.generate&no_html=1&tmpl=component&id=' . $id);?>" width="100" height="20" data-captcha-image style="max-width: none;" />
		</div>

		<div class="eb-comment-captcha-reload"><a href="javascript:void(0);" data-captcha-reload class="btn btn-default">
			<i class="fdi fa fa-sync"></i></a>
		</div>
	</div>

	<input type="text" name="captcha-response" id="captcha-response" class="eb-comment-captcha-input form-control text-center" maxlength="5" data-captcha-input />

	<input type="hidden" name="captcha-id" id="captcha-id" value="<?php echo $id;?>" data-captcha-id/>
</div>
