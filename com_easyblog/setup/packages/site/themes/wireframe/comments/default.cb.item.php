<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li>
	<div class="blog-comment-avatar">
		<a href="<?php echo $comment->creator->getProfileLink(); ?>"><img src="<?php echo $comment->creator->getAvatar(); ?>" width="32" /></a>
	</div>
	<div class="blog-comment-item eztc">
		<div class="small">
			<a href="<?php echo $comment->creator->getProfileLink(); ?>"><?php echo $comment->creator->getName(); ?></a>
			<?php echo JText::_( 'on' ); ?>
			<span class="small"><?php echo $comment->getCreated()->format(JText::_('COM_EASYBLOG_DATE_FORMAT_STATISTICS')); ?></span>
		</div>
		<?php echo $comment->comment; ?>
	</div>
	<div style="clear: both;"></div>
</li>
