<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-posts-list">
	<?php foreach ($posts as $post) { ?>
	<a href="javascript:void(0);" class="eb-comp-toolbar-post"
		data-post-insert
		data-title="<?php echo htmlspecialchars($post->title, ENT_QUOTES); ?>" 
		data-permalink="<?php echo $post->permalink; ?>"
		data-image="<?php echo $post->getImage('thumbnail', true, true);?>"
		data-content="<?php echo strip_tags($post->getIntro());?>"
	>
		<div class="eb-comp-toolbar-post__title"><?php echo $post->title;?></div>
		<div class="eb-comp-toolbar-post__meta">
			<?php echo $post->getCreationDate(true)->format(JText::_('DATE_FORMAT_LC1'));?>
		</div>
	</a>
	<?php } ?>
</div>