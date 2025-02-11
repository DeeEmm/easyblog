<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-eb-posts-section data-url="<?php echo $currentPageLink; ?>">
	<?php if (isset($autoload) && $autoload) { ?>
		<div class="eb-post"></div>
	<?php } ?>

	<?php if ($posts) { ?>
		<?php $index = 0; ?>
		<div class="uk-child-width-1-2@m uk-grid uk-grid-stack">
		<?php foreach ($posts as $post) { ?>
			<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return, $currentPageLink); ?>
			<?php $index++; ?>
		<?php } ?>
		</div>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fdi far fa-paper-plane"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
		</div>
	<?php } ?>
</div>
