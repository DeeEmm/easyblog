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
<a class="btn btn-outline-secondary btn-sm" rel="nofollow" title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT', true); ?>" href="<?php echo $post->getPrintLink();?>" data-post-print>
	<span class="fdi fa fa-print" aria-hidden="true"></span>
	<span>
		<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT'); ?>
	</span>
</a>
