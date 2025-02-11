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
<div class="eb-post-foot">
	<?php if ($params->get('post_hits', true)) { ?>
	<div class="col-cell eb-post-hits">
		<i class="fdi fa fa-eye"></i> <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
	</div>
	<?php } ?>

	<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
	<div class="col-cell eb-post-comments">
		<i class="fdi far fa-comment"></i>
		<a href="<?php echo $post->getCommentsPermalink();?>"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->getTotalComments(), true); ?></a>
	</div>
	<?php } ?>
</div>
