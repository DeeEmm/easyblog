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
<div>
	<?php echo JText::_('COM_EB_GOOGLEIMPORT_SIGN_IN_AS'); ?>
	<?php if ($pictureUrl) { ?>
		<img class="t-ml--sm avatar" src="<?php echo $pictureUrl; ?>" style="max-width:24px;" />
	<?php } ?>
	<?php echo $name; ?>.
	&nbsp;
	( <a href="javascript:void(0)" data-eb-googleimport-revoke><?php echo JText::_('COM_EB_GOOGLEIMPORT_SWITCH_USER'); ?></a> )
</div>
