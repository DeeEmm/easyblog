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
<div class="eb-post-featured">
	<?php if ($icon) { ?>
		<i class="fdi fa fa-star" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_POST_IS_FEATURED');?>" data-fd-tooltip-placement="bottom"></i>
	<?php } ?>

	<?php if ($text) { ?>
		<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURED');?>
	<?php } ?>
</div>
