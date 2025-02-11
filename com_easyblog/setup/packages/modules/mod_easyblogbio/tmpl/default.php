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
<div id="eb" class="eb-mod mod_easyblogbio"<?php echo $modules->getWrapperClass();?>>
	<div class="eb-mod-head eb-mod-item mod-table">
		<?php if ($params->get('showavatar')) { ?>
		<div class="mod-cell cell-tight">
			<a href="<?php echo $blogger->getProfileLink(); ?>" class="mod-avatar mr-10">
				<img src="<?php echo $blogger->getAvatar();?>" class="mr-10" />
			</a>
		</div>
		<?php } ?>
		<div class="mod-cell">
			<a class="eb-mod-media-title" href="<?php echo $blogger->getProfileLink(); ?>">
				<b><?php echo $blogger->getName();?></b>
			</a>
			<div class="mod-author-post">
				<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $blogger->id);?>" class="mod-muted"><?php echo JText::_('MOD_EASYBLOGBIO_VIEW_ALLPOSTS'); ?></a>
			</div>
		</div>
	</div>
	<?php if ($params->get('showbio')) { ?>
	<div class="mt-10">
		<?php if ($biolimit == 0) { ?>
			<?php echo $biography; ?>
		<?php } ?>

		<?php if ($biolimit > 0) { ?>
			<?php echo EBString::substr(strip_tags($biography), 0, $biolimit);?><?php echo JText::_('MOD_EASYBLOGBIO_ELLIPSES'); ?>
		<?php } ?>
	</div>
	<?php } ?>
</div>
