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
<?php if ($params->get('show_reading_time')) { ?>
	<?php echo $this->html('post.entry.readingTime', $post); ?>
<?php } ?>

<?php if ($params->get('post_category', true)) { ?>
<div>
	<?php echo $this->html('post.category', $post->categories); ?>
</div>
<?php } ?>

<?php if ($params->get('post_hits', true)) { ?>
<div>
	<?php echo $this->html('post.hits', $post); ?>
</div>
<?php } ?>

<?php if ($this->config->get('main_comment') && $post->totalComments !== false && $params->get('post_comment_counter', true) && $post->allowcomment) { ?>
<div>
	<?php echo $this->html('post.comments', $post->totalComments, EBFactory::getURI(true) . '#comments'); ?>
</div>
<?php } ?>

<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
<div>
	<?php echo $this->html('post.contributor', $post->getBlogContribution(), true); ?>
</div>
<?php } ?>