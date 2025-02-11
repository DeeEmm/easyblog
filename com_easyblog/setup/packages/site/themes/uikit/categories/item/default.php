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
<?php if ($this->params->get('category_header', true)) { ?>
<div class="t-mb--lg">
	<?php echo $this->html('headers.category', $category, [
		'title' => $this->params->get('category_title', true),
		'description' => $this->params->get('category_description', true),
		'avatar' => $this->params->get('category_avatar', true),
		'subcategories' => $this->params->get('category_subcategories', true),
		'rss' => $this->params->get('category_subscribe_rss', true),
		'subscription' => $this->params->get('category_subscribe_email', true)
	]); ?>
</div>
<?php } ?>

<div data-blog-listings>
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
		<?php echo EB::renderModule('easyblog-before-entries');?>

		<?php if ($posts) { ?>
			<?php $index = 0; ?>
			<div class="uk-child-width-1-2@m uk-grid uk-grid-stack">
			<?php foreach ($posts as $post) { ?>
				<!-- Determine if post custom fields should appear or not in category listings -->
				<?php if (!$this->params->get('category_post_customfields')) { ?>
					<?php $post->fields = '';?>
				<?php } ?>

				<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return); ?>
				<?php $index++; ?>
			<?php } ?>
			</div>
		<?php } ?>

		<?php echo EB::renderModule('easyblog-after-entries'); ?>
	</div>

	<?php if (!$posts) { ?>
		<?php if ($this->my->guest && $category->private == 1) { ?>
			<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY'); ?>
		<?php } ?>

		<?php if ($category->private == 2 && !$allowCat) { ?>
			<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_CATEGORIES_NOT_ALLOWED'); ?>
		<?php } ?>

		<?php if (!$category->private) { ?>
		<div class="t-mt--lg">
			<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
		</div>
		<?php } ?>
	<?php } ?>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination;?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
