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
<div class="eb-entry-moderate">
	<h4 class="reset-heading"><?php echo JText::_('COM_EASYBLOG_VIEWING_UNPUBLISHED_POST');?></h4>
	<div class="mt-10 mb-15"><?php echo JText::_('COM_EASYBLOG_VIEWING_UNPUBLISHED_POST_INFO'); ?></div>

		<div class="clearfix" style="margin-top: 15px;">
			<a class="btn btn-primary" href="javascript:void(0);" data-entry-publish>
				<i class="fdi fa fa-check-circle"></i>
				&nbsp; <?php echo JText::_('COM_EASYBLOG_POST_PUBLISH_POST');?>
			</a>
		</div>
</div>
