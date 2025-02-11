<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-nmm-flickr">
	<div class="eb-nmm-flickr__content" >
		<i class="fdi fa fa-flickr eb-nmm-flickr__icon"></i>
		<div>
			<strong><?php echo JText::_('COM_EASYBLOG_MM_AUTHORIZE_FLICKR_ACCOUNT');?></strong>
		</div>
		<div>
			<?php echo JText::_('COM_EASYBLOG_MM_AUTHORIZE_FLICKR_ACCOUNT_INFO'); ?>
		</div>
		<button data-flickr-login class="btn btn-eb-primary eb-nmm-flickr__btn" data-url="<?php echo $login; ?>">
			<?php echo JText::_('COM_EASYBLOG_MM_SIGN_IN_TO_FLICKR'); ?>
		</button>
	</div>
</div>
