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
<div id="eb" class="eb-mod mod_easybloglatestblogger<?php echo $modules->getWrapperClass();?>">
	<?php if(!empty($bloggers)) { ?>
		<?php foreach($bloggers as $blogger) { ?>
		<div class="eb-mod-item mod-table cell-top">
			<?php if ($params->get('showavatar', true)) { ?>
			<div class="mod-cell cell-tight">
				<a href="<?php echo $blogger->profile->getProfileLink();?>" class="mod-avatar mr-10">
				   <img src="<?php echo $blogger->profile->getAvatar();?>" width="50" height="50" alt="<?php echo $blogger->profile->getName(); ?>" />
				</a>
			</div>
			<?php } ?>
			<div class="mod-cell">
				<a href="<?php echo $blogger->profile->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $blogger->profile->getName(); ?></a>
				<?php if ($params->get('showcount', true)) { ?>
				<div class="mod-muted">
					<?php echo JText::sprintf('MOD_EASYBLOGLATESTBLOGGER_COUNT_' . ($blogger->totalPost > 1 ? 'PLURAL' : 'SINGULAR'), $blogger->totalPost);?>
				</div>
				<?php } ?>

				<?php if ($params->get('showbio', true)) { ?>
				<div class="eb-mod-media-meta">
					<?php if ($blogger->biography != '') { ?>
						<?php echo strip_tags($blogger->biography); ?>
					<?php } else { ?>
						<?php echo JText::sprintf('COM_EASYBLOG_BIOGRAPHY_NOT_SET', $blogger->profile->getName()); ?>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($params->get('showwebsite', true) && $blogger->profile->getWebsite() != '' && !($blogger->profile->getWebsite() == 'http://')) { ?>
				<div class="eb-mod-media-meta">
					<?php echo $blogger->bloggerwebsite;?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	<?php } else { ?>
	<div class="mod-item-nothing">
		<?php echo JText::_('MOD_EASYBLOGLATESTBLOGGER_NO_BLOGGER'); ?>
	</div>
	<?php } ?>
</div>