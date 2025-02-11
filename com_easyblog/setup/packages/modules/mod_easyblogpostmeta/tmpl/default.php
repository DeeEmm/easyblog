<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod_easyblogpostmeta<?php echo $modules->getWrapperClass();?>">
	<?php if ($params->get('showavatar', true) || $params->get('showAuthor', true)) { ?>
		<div class="mod-table mb-10">
			<?php if ($params->get('showavatar', true)) { ?>
				<div class="col-cell cell-tight">
					<a href="javascript:void(0);" class="mod-avatar mr-10">
						<img src="<?php echo $blogger->getAvatar();?>" class="avatar" width="50" height="50" />
					</a>
				</div>
			<?php } ?>
			<?php if ($params->get('showAuthor', true)) { ?>
				<div class="mod-cell">
					<a href="<?php echo $blogger->getProfileLink();?>">
						<b><?php echo $blogger->getName();?></b>
					</a>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ($params->get('showDate', true)) { ?>
		<div>
			<i class="mod-muted fdi far fa-clock"></i>&nbsp; <?php echo $post->getDisplayDate('created')->format(JText::_('DATE_FORMAT_LC1')); ?>
		</div>
	<?php } ?>

	<?php if ($params->get('showcategory', true)) { ?>
		<div class="mod-option ezwrite">
			<div class="eb-meta-category eb-comma">
				<i class="mod-muted fdi far fa-folder-open"></i>

				<?php foreach ($categories as $category) { ?>
				<span><a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a></span>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if ($params->get('showhits', true)) { ?>
		<div class="mod-option ezmyblog">
			<i class="mod-muted fdi fa fa-eye"></i>&nbsp; <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
		</div>
	<?php } ?>

	<?php if ($params->get('showcommentcount', true)) { ?>
		<div class="mod-option ezmycomment">
			<i class="mod-muted fdi fa fa-comments"></i>&nbsp; <a href="#comments"><?php echo  JText::sprintf('MOD_EASYBLOGPOSTMETA_COMMENTS', $post->getTotalComments()); ?></a>
		</div>
	<?php } ?>
</div>
