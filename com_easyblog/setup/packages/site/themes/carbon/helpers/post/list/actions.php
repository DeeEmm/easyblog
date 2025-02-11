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
<div class="eb-post-actions" style="display: inline-block;">
	<?php if ($post->hasReadmore() && $params->get('post_readmore', true)) { ?>
	<div class="col-cell">
		<div class="eb-post-more">
			<?php echo $this->html('post.list.readmore', $post); ?>
		</div>
	</div>
	<?php } ?>

	<?php if ($this->config->get('main_ratings') && $params->get('post_ratings', true)) { ?>
	<div class="col-cell">
		<?php echo $this->html('post.ratings', $post, $this->config->get('main_ratings_frontpage_locked')); ?>
	</div>
	<?php } ?>

	<?php if ($params->get('post_hits', true)) { ?>
	<div class="col-cell">
		<?php echo $this->html('post.hits', $post, true); ?>
	</div>
	<?php } ?>

	<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
	<div class="col-cell">
		<?php echo $this->html('post.comments', $post->getTotalComments(), $post->getCommentsPermalink(), true); ?>
	</div>
	<?php } ?>
</div>
