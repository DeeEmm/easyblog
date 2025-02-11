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
<?php foreach ($categories as $category) { ?>
<div class="mod-item <?php echo $category->parent_id ? 'mt-10' : '';?>">
	 <div class="mod-table">
		<div class="mod-cell cell-tight">
			<?php if ($showCategoryAvatar) { ?>
			<a href="<?php echo $category->getPermalink();?>" class="eb-mod-media-thumb">
				<img class="avatar" src="<?php echo $category->getAvatar(); ?>" width="32" height="32" alt="<?php echo JText::_($category->title); ?>" />
			</a>
			<?php } else { ?>
				<i class="eb-mod-media-thumb fdi fa fa-folder mod-muted mr-10"></i>
			<?php } ?>
		</div>

		<div class="mod-cell">
			<div class="mod-table">
				<div class="mod-cell">
					<a href="<?php echo $category->getPermalink(); ?>"><?php echo $category->title; ?></a>
					<?php if ($params->get('showcount', true)) { ?>
						<span class="mod-muted">(<?php echo $category->cnt; ?>)</span>
					<?php } ?>
					<?php if ($params->get('showrss', true)) { ?>
					<div class="mod-small">
						<a class="eb-brand-rss" title="<?php echo JText::_('MOD_EASYBLOGCATEGORIES_SUBSCRIBE_FEEDS'); ?>" href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&id=' . $category->id, false, 'category'); ?>" target="_blank">
							<i class="fdi fa fa-rss-square"></i>&nbsp; <?php echo JText::_('MOD_EASYBLOGCATEGORIES_SUBSCRIBE_FEEDS'); ?>
						</a>
					</div>
					<?php } ?>
				</div>

				<?php if ($category->childs) { ?>
				<a class="mod-cell cell-tight mod-muted" data-eb-toggle="collapse" href="#eb-cat-<?php echo $category->id; ?>" aria-label="Toggle">
					<i class="eb-mod-media-thumb fdi fa fa-chevron-right mod-muted"></i>
				</a>
				<?php } ?>
			</div>

			<?php if ($category->childs) { ?>
			<div <?php echo $category->childs ? 'id="eb-cat-' . $category->id . '"' : '';?> class="collapse">
				<?php echo $helper->getToggleOutput($category->childs, true); ?>
			</div>
			<?php } ?>
		</div>
	 </div>
</div>
<?php } ?>
