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
<div class="eb-nmm-google" data-eb-composer-oauth-google>
	<br /><br /><br /><br />
	<div class="eb-nmm-google__content" >
		<i class="fdi fab fa-google"></i>
		<div>
			<strong><?php echo JText::_('Authorize Google Account');?></strong>
		</div>
		<div>
			<?php echo JText::_('To retrieve Google documents from Google drive, you will need to associate with your google account.'); ?>
		</div>
		<button data-google-login class="btn btn-eb-primary eb-nmm-flickr__btn" data-url="<?php echo $url; ?>">
			<?php echo JText::_('Sign In To Google'); ?>
		</button>
	</div>
</div>
