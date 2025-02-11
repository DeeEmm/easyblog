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
<div id="eb" class="eb-mod mod-easyblogsubscribers<?php echo $params->get('moduleclass_sfx') ?>">
	<div class="mod-thumbs">
		<?php if ($subscribers['users']) { ?>
			<?php foreach ($subscribers['users'] as $subscriber) { ?>
			<div>
				<a href="<?php echo $subscriber->getPermalink();?>" class="mod-avatar">
					<img src="<?php echo $subscriber->getAvatar();?>" />
				</a>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="mod-hr"></div>

	<?php if ($subscribers['guests']) { ?>
		<p>
			<?php echo JText::sprintf('MOD_EASYBLOGSUBSCRIBERS_TOTAL_GUESTS', count($subscribers['guests'])); ?>
		</p>
	<?php } ?>

	<div>
		<a href="javascript:void(0);" class="btn btn-danger btn-sm <?php echo $subscribed ? '' : 'hide'; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscribed;?>" data-return="<?php echo $return;?>">
			<?php echo JText::_('MOD_EASYBLOGSUBSCRIBERS_UNFOLLOW_' . strtoupper($type));?>
		</a>
		<a href="javascript:void(0);" class="btn btn-primary btn-sm <?php echo $subscribed ? 'hide' : ''; ?>" data-blog-subscribe data-id="<?php echo $id;?>" data-type="<?php echo $type;?>">
			<?php echo JText::_('MOD_EASYBLOGSUBSCRIBERS_FOLLOW_' . strtoupper($type));?>
		</a>
	</div>
</div>