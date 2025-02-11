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
<div id="eb" class="eb-mod mod_easybloglatestcomment<?php echo $modules->getWrapperClass();?>">
<?php if ($comments) { ?>
	<?php foreach ($comments as $comment) { ?>
	<div class="eb-mod-item">
		<div class="eb-mod-head mod-table cell-top">
			<?php if ($params->get('showavatar')) { ?>
				<div class="mod-cell cell-tight">
					<a href="#" class="mod-avatar mr-10">
						<img src="<?php echo $comment->author->getAvatar();?>" width="50" height="50" />
					</a>
				</div>
			<?php } ?>

			<div class="mod-cell">
				<?php if ($params->get('showauthor')) { ?>
					<?php if ($comment->created_by == 0) { ?>
						<strong><a href="javascript:void(0);"><?php echo JText::_('COM_EASYBLOG_GUEST').' - '.$comment->name;?></a></strong>
					<?php } else { ?>
						<strong><a href="<?php echo $comment->author->getProfileLink();?>"><?php echo $comment->author->getName();?></a></strong>
					<?php } ?>
				<?php } ?>

				<?php if ($params->get('showauthor') && $params->get('showtitle')) { ?>
					<i class="fdi fa fa-chevron-right mod-xsmall mod-muted"></i>
				<?php } ?>

				<?php if ($params->get('showtitle')) { ?>
					<strong><a href="<?php echo EBR::_('index.php?option=com_easyblog&view=entry&id=' . $comment->post_id); ?>" class="eb-mod-media-title"> <?php echo $comment->blog_title; ?></a></strong>
				<?php } ?>

				<div class="mod-muted mod-small">
					<?php echo $comment->dateString; ?>
				</div>

				<div class="eb-mod-body">
					<?php $text = strip_tags(EB::comment()->parseBBCode($comment->comment)); ?>
					<?php echo EBString::strlen($text) > $maxCharacter? EBString::substr($text, 0, $maxCharacter) . '...' : $text; ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } else { ?>
	<div><?php echo JText::_('MOD_EASYBLOGLATESTCOMMENT_NO_POST'); ?></div>
<?php } ?>
</div>
