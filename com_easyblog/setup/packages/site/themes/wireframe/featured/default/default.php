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
<div data-blog-listings>
	<?php echo EB::renderModule('easyblog-before-entries');?>

	<div class="eb-post-listing
		<?php echo $postStyles->row === 'row' ? 'is-row' : '';?>
		<?php echo $postStyles->row === 'column' && $postStyles->column === 'column' ? 'is-column ' : '';?>
		<?php echo $postStyles->row === 'column' && $postStyles->column === 'masonry' ? 'is-masonry ' : '';?>
		<?php echo $postStyles->row === 'column' ? 'eb-post-listing--col-' . $postStyles->columns : '';?>
		<?php echo $postStyles->row === 'row' && $this->params->get('row_divider', true) ? 'has-divider' : '';?>
		<?php echo $this->isMobile() ? 'is-mobile' : '';?>
		"
		data-blog-posts
	>
		<?php if ($posts) { ?>
			<?php $index = 0; ?>
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return); ?>
				<?php $index++; ?>
			<?php } ?>
		<?php } ?>
	</div>

	<?php if (!$posts) { ?>
		<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_FEATURED_POSTS_YET'); ?>
	<?php } ?>

	<?php echo EB::renderModule('easyblog-after-entries'); ?>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination->getPagesLinks();?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
