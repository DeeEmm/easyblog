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
<div class="eb-blog-grids" data-eb-grid-listings>
	<div class="eb-post-listing eb-post-listing--col-<?php echo $columns;?> is-column" data-blog-posts>
		<?php if ($featured) { ?>
		<div class="eb-post-listing__item has-featured">
			<?php echo $this->html('featured.slider', $featured, [
				'style' => $this->params->get('featured_style', 'slick'),
				'autoplay' => true,
				'autoplayInterval' => 8,
				'navigation' => true,
				'image' => $this->params->get('photo_show', true),
				'postTitle' => true,
				'postDate' => $this->params->get('contentdate', true),
				'postDateSource' => $this->params->get('showcase_date_source', 'created'),
				'postCategory' => $this->params->get('category_show', true),
				'postContent' => true,
				'postContentLimit' => $this->params->get('showcase_content_limit', 350),
				'authorAvatar' => $this->params->get('authoravatar', true),
				'authorTitle' => $this->params->get('contentauthor', true),
				'readmore' => $this->params->get('showreadmore', true),
				'ratings' => false
			]); ?>
		</div>
		<?php } ?>

		<?php if ($posts) { ?>
			<?php echo $this->output('site/grid/default/posts'); ?>
		<?php } ?>
	</div>

	<?php if ($pagination || $showLoadMore) { ?>
		<?php if (!$showLoadMore && $paginationStyle != 'autoload') { ?>
			<?php echo $pagination;?>
		<?php } else if ($showLoadMore) { ?>
		<div>
			<a class="btn btn-default btn-block" href="javascript:void(0);" data-eb-pagination-loadmore data-limitstart="<?php echo $limitstart; ?>">
				<i class="fdi fa fa-sync"></i>&nbsp;<?php echo JText::_('COM_EB_LOADMORE'); ?>
			</a>
		</div>
		<?php } ?>
	<?php } ?>
</div>
