<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ((!empty($navigation->prev) || !empty($navigation->next)) && $this->entryParams->get('post_navigation', true)) { ?>
<ul class="pager pagenav">
	
	<?php if (!empty($navigation->prev)) { ?>
	<li class="previous">
		<a href="<?php echo $navigation->prev->link;?>">
			<span class="icon-chevron-left" aria-hidden="true"></span>
			<span aria-hidden="true"><?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS'); ?></span>
		</a>
	</li>
	<?php } ?>

	<?php if (!empty($navigation->next)) { ?>
	<li class="next">
		<a class="" href="<?php echo $navigation->next->link;?>">
			<span aria-hidden="true"><?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT'); ?></span> 
			<span class="icon-chevron-right" aria-hidden="true"></span>
		</a>
	</li>
	<?php } ?>
	
</ul>
<?php } ?>