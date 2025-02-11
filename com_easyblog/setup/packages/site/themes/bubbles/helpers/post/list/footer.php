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
	<div class="t-d--flex">
		<div class="">
			<?php if ($params->get('post_hits', true)) { ?>
				<?php echo $this->html('post.hits', $post, true); ?>
			<?php } ?>
		</div>

		<?php if ($post->getTotalComments() !== false && $params->get('post_comment_counter', true)) { ?>
			<div class="t-ml--md">
				<div class="eb-post-comments">
					<?php echo $this->html('post.comments', $post->getTotalComments(), $post->getCommentsPermalink(), true); ?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
