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
<div data-eb-posts-section data-url="<?php echo $currentPageLink; ?>">

	<?php echo EB::renderModule('easyblog-before-entries');?>

	<div class="blog">
		<div class="article-list">
			<div class="row">
				<?php if ($posts) { ?>
					<?php $index = 0; ?>
					<?php foreach ($posts as $post) { ?>
					<div class="col-md-4">
						<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return, $currentPageLink); ?>
						<?php $index++; ?>
					</div>
					<?php } ?>
				<?php } ?>

				<?php if (!$posts) { ?>
					<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
				<?php } ?>
			</div>
		</div>
	</div>


	<?php echo EB::renderModule('easyblog-after-entries'); ?>
</div>
