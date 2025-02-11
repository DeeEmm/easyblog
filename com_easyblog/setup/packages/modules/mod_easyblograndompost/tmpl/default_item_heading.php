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
<div class="eb-mod-head mod-table align-middle">
	<?php if ($params->get('showavatar', false)) { ?>
		<div class="mod-cell cell-tight">
			<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="mod-avatar-sm mr-10">
				<img src="<?php echo $post->getAuthor()->getAvatar(); ?>" width="50" height="50" />
			</a>
		</div>
	<?php } ?>

	<div class="mod-cell">
		<?php if ($params->get('showauthor', false)) { ?>
			<strong>
				<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->getAuthor()->getName(); ?></a>
			</strong>
		<?php } ?>

		<?php if ($params->get('showdate' , true)) { ?>
			<div class="mod-muted mod-small mod-fit">
				<?php echo $post->getCreationDate()->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
			</div>
		<?php } ?>
	</div>
</div>