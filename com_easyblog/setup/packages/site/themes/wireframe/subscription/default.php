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
<div class="eb-subscription">

	<?php if (in_array('site', $groups)) { ?>
	<div class="eb-subscribe row-table cell-top">
		<div class="col-cell eb-subscribe-thumb cell-tight">
			<i class="fdi fa fa-globe"></i>
		</div>
		<div class="col-cell cell-ellipse eb-subscribe-details">
			<h3 class="reset-heading text-ellipsis"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBED_SITE_WIDE_TITLE');?></h3>
			<p class="text-muted">
				<?php echo JText::_('COM_EASYBLOG_SUBSCRIBED_ON');?> <?php echo $subscriptions['site'][0]->getSubscriptionDate()->format(JText::_('DATE_FORMAT_LC1'));?>
			</p>
			<a href="<?php echo EBR::_('index.php?option=com_easyblog');?>"><?php echo EBR::getRoutedURL('index.php?option=com_easyblog', false, true);?></a>
			<a href="javascript:void(0);" class="btn btn-sm btn-default" data-blog-unsubscribe data-subscription-id="<?php echo $subscriptions['site'][0]->id;?>" data-return="<?php echo base64_encode(EBFactory::getURI(true));?>">
				<?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE');?>
			</a>
		</div>
	</div>
	<?php } ?>

	<?php foreach ($groups as $group) { ?>

		<?php if ($group == 'site') { continue; } ?>

		<div class="t-mt--md t-mb--md">
			<?php echo $this->html('snackbar.standard', 'COM_EASYBLOG_SUBSCRIBED_' . strtoupper($group) . '_TITLE'); ?>
		</div>

		<?php foreach ($subscriptions[$group] as $subscription) { ?>
		<div class="eb-subscribe row-table cell-top">
			<?php if ($subscription->object->objAvatar) { ?>
			<div class="col-cell eb-subscribe-thumb cell-tight">
				<a href="<?php echo $subscription->object->objPermalink;?>" class="eb-avatar" class="eb-avatar">
					<?php if ($group == 'category') { ?>
						<img src="<?php echo $subscription->object->objAvatar;?>" width="50" height="50" alt="<?php echo $subscription->object->title;?>" />
					<?php } ?>

					<?php if ($group == 'blogger') { ?>
						<img src="<?php echo $subscription->object->objAvatar;?>" class="eb-authors-avatar" width="50" height="50" alt="<?php echo $subscription->object->title;?>" />
					<?php } ?>

					<?php if ($group == 'entry') { ?>
						<span class="eb-subscribe__cover" style="background-image: url('<?php echo $subscription->object->objAvatar;?>');">
						</span>
					<?php } ?>

					<?php if ($group == 'teamblog' || $group == 'team') { ?>
						<span class="eb-subscribe__cover" style="background-image: url('<?php echo $subscription->object->objAvatar;?>');">
						</span>
					<?php } ?>
				</a>
			</div>
			<?php } else { ?>
			<div class="col-cell eb-subscribe-thumb cell-tight">
				<a href="<?php echo $subscription->object->objPermalink;?>" class="eb-avatar" class="eb-avatar">
					<?php if ($group == 'category') { ?>
						<i class="fdi far fa-folder"></i>
					<?php } ?>

					<?php if ($group == 'blogger') { ?>
						<i class="fdi fa fa-user-o"></i>
					<?php } ?>
				</a>
			</div>
			<?php } ?>

			<div class="col-cell cell-ellipse eb-subscribe-details">
				<h3 class="reset-heading text-ellipsis"><?php echo $subscription->object->title;?></h3>
				<p class="text-muted">
					<?php echo JText::_('COM_EASYBLOG_SUBSCRIBED_ON');?> <?php echo $subscription->getSubscriptionDate()->format(JText::_('DATE_FORMAT_LC1'));?>
				</p>
				<a href="<?php echo $subscription->object->objPermalink;?>"><?php echo $subscription->object->objPermalink;?></a>

				<a href="javascript:void(0);" class="btn btn-sm btn-default" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>" data-return="<?php echo base64_encode(EBFactory::getURI(true));?>">
					<?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE');?>
				</a>
			</div>
		</div>
		<?php } ?>

	<?php } ?>

	<?php if (!$subscriptions) { ?>
	<div class="eb-empty">
		<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_NO_SUBSCRIPTIONS_YET_CURRENTLY'); ?>
	</div>
	<?php } ?>
</div>
