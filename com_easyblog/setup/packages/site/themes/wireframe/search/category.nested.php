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
<ul class="eb-filter-menu eb-filter-menu--nested" data-category-nested>
	<li class="eb-filter-menu__item eb-filter-menu__item-back" data-category-back>
		<a href="javascript:void(0);" class="eb-filter-menu__link">
			<i class="fdi fa fa-angle-left"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_BACK'); ?>
		</a>
	</li>

	<?php foreach ($categories as $category) { ?>
		<li class="eb-filter-menu__item<?php echo $activeCategoryId == $category->id ? ' active' : '';?>" data-category-filter="<?php echo $category->id; ?>">
			<a href="javascript:void(0);" class="eb-filter-menu__link t-text--truncate" 
				title="<?php echo EB::String()->escape($category->title); ?>" 
				data-eb-filter="category" 
				data-id="<?php echo $category->id; ?>">
				<i class="fdi far fa-folder-open"></i>&nbsp; <?php echo $category->title; ?>
			</a>
			<?php if ($category->descendants) { ?>
				<a href="javascript:void(0);" class="eb-filter-menu__toggle" data-category-nav>
					<i class="fdi fa fa-angle-right"></i>
				</a>
			<?php } ?>
		</li>
	<?php } ?>
</ul>