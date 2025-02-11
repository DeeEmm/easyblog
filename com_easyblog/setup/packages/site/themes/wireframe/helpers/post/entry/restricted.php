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
<div class="eb-restrictions mb-15 mt-10">
	<div class="eb-restrictions__title">
		<?php echo JText::_('COM_EB_LOGIN_TO_READ_FULL_TITLE'); ?>
	</div>
	<div class="eb-restrictions__desc">
		<?php echo JText::_('COM_EB_LOGIN_TO_READ_FULL_DESC'); ?>
	</div>
	<div class="eb-restrictions__action">
		<a href="<?php echo $loginLink; ?>" class="btn btn-primary">
			<?php echo JText::_('COM_EB_SIGN_IN'); ?>
		</a>
	</div>
</div>
