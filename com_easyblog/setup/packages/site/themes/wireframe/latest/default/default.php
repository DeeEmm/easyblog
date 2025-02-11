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
<?php if ($featured && $this->params->get('featured_slider', true)) { ?>
<div class="t-mb--2xl">
	<?php echo $this->html('featured.slider', $featured, [
		'style' => $this->params->get('featured_style', 'default'),
		'autoplay' => $this->params->get('featured_auto_slide', true),
		'autoplayInterval' => $this->params->get('featured_auto_slide_interval', 8),
		'navigation' => $this->params->get('featured_bottom_navigation', true),
		'image' => $this->params->get('featured_post_image', true),
		'postTitle' => $this->params->get('featured_post_title', true),
		'postDate' => $this->params->get('featured_post_date', true),
		'postDateSource' => $this->params->get('featured_post_date_source', 'created'),
		'postCategory' => $this->params->get('featured_post_category', true),
		'postContent' => $this->params->get('featured_post_content', true),
		'postContentLimit' => $this->params->get('featured_post_content_limit', 250),
		'authorAvatar' => $this->params->get('featured_post_author_avatar', true),
		'authorTitle' => $this->params->get('featured_post_author', true),
		'readmore' => $this->params->get('featured_post_readmore', true),
		'ratings' => $this->params->get('featured_post_ratings', false)
	]); ?>
</div>
<?php } ?>

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
			<?php echo $this->output('site/listing/wrapper', [
				'postStyle' => $postStyles->post,
				'posts' => $posts,
				'return' => $return,
				'currentPageLink' => $currentPageLink
			]); ?>
		<?php } ?>
	</div>

	<?php if (!$posts) { ?>
	<div class="t-mt--lg">
		<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
	</div>
	<?php } ?>

	<?php echo EB::renderModule('easyblog-after-entries'); ?>

	<?php if ($pagination || $showLoadMore) { ?>
		<?php echo EB::renderModule('easyblog-before-pagination'); ?>

		<?php if (!$showLoadMore && $this->params->get('pagination_style', 'normal') != 'autoload') { ?>
			<?php echo $pagination;?>
		<?php } else if ($showLoadMore) { ?>
		<div class="mt-20">
			<a class="btn btn-default btn-block" href="javascript:void(0);" data-eb-pagination-loadmore data-limitstart="<?php echo $limitstart; ?>">
				<i class="fdi fa fa-sync"></i>&nbsp;<?php echo JText::_('COM_EB_LOADMORE'); ?>
			</a>
		</div>
		<?php } ?>

		<?php echo EB::renderModule('easyblog-after-pagination'); ?>
	<?php } ?>
</div>
