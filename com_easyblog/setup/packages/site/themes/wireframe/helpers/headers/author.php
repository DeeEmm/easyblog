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
<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">
	<div class="eb-authors-head">
		<?php if ($this->config->get('layout_avatar') && $viewOptions->avatar) { ?>
		<div class="t-flex-shrink--0 t-pr--md">
			<?php echo $this->html('avatar.user', $author, 'lg'); ?>
		</div>
		<?php } ?>

		<div class="t-flex-grow--1">
			<?php if ($viewOptions->name) { ?>
			<div class="">
				<h2 class="eb-authors-name reset-heading">
					<a href="<?php echo $author->getProfileLink(); ?>" class="text-inherit"><?php echo $author->getName(); ?></a>
				</h2>
			</div>
			<?php } ?>


			<div class="eb-authors-subscribe spans-separator">
				<?php if (EB::messaging()->hasMessaging($author->id)) { ?>
					<?php echo EB::messaging()->html($author);?>
				<?php } ?>

				<?php if ($author->getTwitterLink() && $viewOptions->twitter) { ?>
				<span>
					<a href="<?php echo $author->getTwitterLink(); ?>" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME', true);?>" data-fd-tooltip-placement="top">
						<i class="fdi fab fa-twitter"></i>
					</a>
				</span>
				<?php } ?>

				<?php if ($author->getWebsite() && $viewOptions->website) { ?>
				<span class="eb-authors-url">
					<a href="<?php echo $this->escape($author->getWebsite());?>" target="_blank" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EB_VISIT_WEBSITE', true);?>" data-fd-tooltip-placement="top">
						<i class="fdi fa fa-globe"></i>
					</a>
				</span>
				<?php } ?>

				<?php if ($this->acl->get('allow_subscription') && $this->config->get('main_bloggersubscription') && $viewOptions->subscription && $author->id !== (int) $this->my->id) { ?>
				<span class="eb-authors-subscription">
					<a href="javascript:void(0);" class="<?php echo $author->isBloggerSubscribed ? 'hide' : ''; ?>" data-blog-subscribe data-type="blogger" data-id="<?php echo $author->id;?>"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER', true);?>" data-fd-tooltip-placement="top"
					>
						<i aria-hidden="true" class="fdi fa fa-envelope"></i>
						<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
					</a>
					<a href="javascript:void(0);" class="<?php echo $author->isBloggerSubscribed ? '' : 'hide'; ?>"
						data-blog-unsubscribe
						data-type="blogger"
						data-id="<?php echo $author->id;?>"
						data-subscription-id="<?php echo $author->isBloggerSubscribed ? $author->isBloggerSubscribed : '';?>"
						data-email="<?php echo $this->my->email;?>"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER', true);?>" data-fd-tooltip-placement="top"
					>
						<i aria-hidden="true" class="fdi fa fa-envelope"></i>
						<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER'); ?></span>
					</a>
				</span>
				<?php } ?>

				<?php if ($this->acl->get('allow_subscription_rss') && $this->config->get('main_rss') && $viewOptions->rss) { ?>
				<span class="eb-authors-rss">
					<a href="<?php echo $author->getRSS();?>" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS', true);?>" data-fd-tooltip-placement="top">
						<i class="fdi fa fa-rss"></i>
					</a>
				</span>
				<?php } ?>
				<?php if ($viewOptions->featureAction) { ?>
				<span>
					<?php if (!FH::isSiteAdmin()) { ?>
					<small class="eb-authors-featured eb-star-featured<?php echo !$isFeatured ? ' hide' : '';?>"
						data-featured-tag
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>" data-fd-tooltip-placement="top"
					>
						<i class="fdi fa fa-star"></i>
					</small>
					<?php } ?>
					<?php if (FH::isSiteAdmin()) { ?>
					<a href="javascript:void(0);"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_UNFEATURE_AUTHOR', true);?>" data-fd-tooltip-placement="top"
						class="eb-star-featured <?php echo !$isFeatured ? ' hide' : '';?>" data-author-unfeature
						data-id="<?php echo $author->id;?>">
						<i class="fdi fa fa-star"></i>
					</a>
					<a href="javascript:void(0);"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_FEATURE_AUTHOR', true);?>" data-fd-tooltip-placement="top"
						class="<?php echo $isFeatured ? ' hide' : '';?>"
						data-author-feature data-id="<?php echo $author->id;?>">
						<i class="fdi fa fa-star"></i>
					</a>
					<?php } ?>
				</span>
				<?php } ?>
			</div>

			<?php  if ($author->getBiography() && $viewOptions->biography) { ?>
			<div class="eb-authors-bio">
				<?php if ($viewOptions->isActivateBioTruncation) { ?>
					<?php echo $this->fd->html('str.truncate', $author->getBiography(), 350); ?>
				<?php } else { ?>
					<?php echo $author->getBiography();?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
