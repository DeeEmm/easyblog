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
<div class="eb-authors-head">
	<?php if ($this->config->get('layout_categoryavatar', true) && $viewOptions->avatar) { ?>
		<div class="t-flex-shrink--0 t-pr--md">
			<?php echo $this->html('avatar.category', $category, 'lg'); ?>
		</div>
	<?php } ?>

	<div class="t-flex-grow--1">
		<?php if ($viewOptions->title) { ?>
		<div class="">
			<h2 class="eb-authors-name reset-heading">
				<a href="<?php echo $category->getPermalink();?>" class="text-inherit"><?php echo $category->getTitle();?></a>
			</h2>
		</div>
		<?php } ?>

		<div class="eb-authors-subscribe spans-separator">
			<?php if ((($category->private && $this->my->id != 0) || ($this->my->id == 0 && $this->config->get('main_allowguestsubscribe')) || !$this->my->guest) && $this->config->get('main_categorysubscription') && $viewOptions->subscription && $this->acl->get('allow_subscription')) { ?>
			<span>
				<a href="javascript:void(0);" class="<?php echo $category->isCategorySubscribed ? 'hide' : ''; ?>"
					data-blog-subscribe data-type="category" data-id="<?php echo $category->id; ?>"
					data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY', true);?>" data-fd-tooltip-placement="top"
				>
					<i class="fdi fa fa-envelope"></i>
				</a>
				<a href="javascript:void(0);" class="<?php echo $category->isCategorySubscribed ? '' : 'hide'; ?>"
					data-blog-unsubscribe data-subscription-id="<?php echo $category->isCategorySubscribed ? $category->isCategorySubscribed : '';?>" data-return="<?php echo base64_encode(EBFactory::getURI(true));?>"
					data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_CATEGORY', true);?>" data-fd-tooltip-placement="top"
				>
					<i class="fdi fa fa-envelope"></i>
				</a>
			</span>
			<?php } ?>

			<?php if ($this->config->get('main_rss') && $this->acl->get('allow_subscription_rss') && $viewOptions->rss) { ?>
			<span>
				<a class="link-rss" href="<?php echo $category->getRssLink();?>" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS', true);?>" data-fd-tooltip-placement="top">
					<i class="fdi fa fa-rss"></i>
				</a>
			</span>
			<?php } ?>
		</div>

		<?php if ($viewOptions->description && $category->description) { ?>
		<div class="eb-authors-bio">
			<?php echo $this->fd->html('str.truncate', $category->getDescription(), 350); ?>
		</div>
		<?php } ?>

		<?php if (!empty($category->nestedLink) && $viewOptions->subcategories) { ?>
		<div class="eb-authors-bio">
			<p>
				<b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_SUBCATEGORIES'); ?></b>
			</p>

			<?php echo $category->nestedLink; ?>
		</div>
		<?php } ?>
	</div>
</div>

