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
<div class="eb-simple-post">
	<div class="eb-simple-post__icon">
		<?php echo $this->html('post.icon', $post->getType()); ?>
	</div>
	<div class="eb-simple-post__content">
		<div class="eb-simple-post__context">
			<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
		</div>
		<div class="eb-simple-post__date">
			<time><?php echo $post->getDisplayDate($dateSource)->format(JText::_($dateFormat));?></time>
		</div>
	</div>
</div>
