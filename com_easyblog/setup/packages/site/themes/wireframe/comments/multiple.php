<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<ul class="eb-comments-tab blog-comment-tabs reset-list">
	<?php $i = 0; ?>
	<?php foreach ($types as $key => $val) { ?>
		<li class="<?php echo $i == 0 ? 'active' : 'inactive';?>" id="system-<?php echo strtolower( $key );?>">
			<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYBLOG_COMMENT_SYSTEM_' . strtoupper( $key ) ); ?></a>
		</li>
		<?php $i++;?>
	<?php } ?>
</ul>

<div class="eb-comments-tab-content blog-comment-contents">
	<?php $i = 0; ?>
	<?php foreach ($types as $key => $html) { ?>
		<div class="blog-comment-system-<?php echo strtolower( $key );?>"<?php echo $i != 0 ? ' style="display:none;"' : '';?>>
			<?php echo $html;?>
		</div>
		<?php $i++;?>
	<?php } ?>
</div>
