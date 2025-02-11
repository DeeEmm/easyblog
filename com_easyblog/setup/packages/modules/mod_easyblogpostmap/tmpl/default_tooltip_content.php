<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ebpostmap_infoWindow" style="max-width: <?php echo $this->params->get('infowidth', 300);?>px;width: <?php echo $this->params->get('infowidth', 300);?>px; line-height: 1.35; white-space: nowrap;overflow: hidden;">
	<table>
		<?php if ($this->params->get('showimage', true) && $post->hasImage()) { ?>
		<tr>
			<td class="ebpostmap_featuredImage" <?php echo $this->params->get('showavatar', false) ? 'colspan="2"' : '';?>>
				<div style="padding: 2px;margin-bottom: 8px;border: 1px solid #d7d7d7;"><img src="<?php echo $post->cover;?>" style="width: 100%;" /></div>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<?php if ($this->params->get('showavatar', false)) { ?>
			<td class="ebpostmap_avatar" valign="top">
				<a href="<?php echo $post->creator->getProfileLink();?>" class="mod-avatar">
					<img class="avatar" src="<?php echo $post->creator->getAvatar();?>" />
				</a>
			</td>
			<?php } ?>
			<td class="ebpostmap_detail">
				<div class="ebpostmap_title">
					<a href="<?php echo $post->getPermalink();?>"><b><?php echo $post->title;?></b></a>
				</div>

				<?php if ($this->params->get('showauthor', true)) { ?>
				<div class="ebpostmap_blogger"><?php echo JText::sprintf('MOD_EASYBLOGPOSTMAP_POST_BY', $post->creator->getName());?></div>
				<?php } ?>

				<?php if ($this->params->get('showaddress', true)) { ?>
				<div class="ebpostmap_address"><?php echo JText::sprintf('MOD_EASYBLOGPOSTMAP_ADDRESS_AT', $post->address);?></div>
				<?php } ?>

				<?php if ($this->params->get('showcommentcount', false)) { ?>
				<div class="ebpostmap_comments"><?php echo JText::sprintf('MOD_EASYBLOGPOSTMAP_TOTAL_COMMENTS', $post->commentCount);?></div>
				<?php } ?>

				<?php if ($this->params->get('showhits', false)) { ?>
				<div class="ebpostmap_hits"><?php echo JText::sprintf('MOD_EASYBLOGPOSTMAP_HITS', $post->hits);?></div>
				<?php } ?>

				<?php if ($this->params->get('showratings', true)) { ?>
				<div class="ebpostmap_ratings">
					<?php echo EB::ratings()->html($post, 'ebpostmap_'.$post->id.'-ratings',JText::_('MOD_EASYBLOGPOSTMAP_RATEBLOG'), !$this->params->get('enableratings', true));?>
				</div>
				<?php } ?>
			</td>
		</tr>
	</table>
</div>
