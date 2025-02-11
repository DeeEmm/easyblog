<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-horizonline">
	<div class="eb-horizonline-inner">
		<?php if ($this->config->get('layout_avatar') && $params->get('post_author_avatar', true)) { ?>
			<?php echo $this->html('post.list.authorAvatar', $post); ?>
		<?php } ?>

		<?php if ($params->get('post_author', true)) { ?>
			<?php echo $this->html('post.author', $post->getAuthorName(), $post->getAuthorPermalink()); ?>
		<?php } ?>
	</div>
</div>
