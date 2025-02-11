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
<?php if ($params->get('showratings', false) && $post->showRating) { ?>
	<div class="eb-rating">
		<?php echo EB::ratings()->html($post, 'ebtopblogs-' . $post->id . '-ratings', JText::_('MOD_TOPBLOGS_RATEBLOG'), $disableRatings); ?>
	</div>
<?php } ?>

<?php if ($params->get('showhits', false) || $params->get('showcommentcount', false) || $params->get('showreadmore', true)) { ?>
<div class="eb-mod-foot mod-muted mod-small">
	<?php if ($params->get('showhits' , false)) { ?>
		<div class="mod-cell pr-10">
			<?php echo JText::sprintf('MOD_TOPBLOGS_HITS', $post->hits);?>
		</div>
	<?php } ?>

	<?php if ($params->get('showcommentcount', false)) { ?>
		<div class="mod-cell pr-10">
			<a href="<?php echo $post->getCommentsPermalink();?>">
				<?php echo EB::string()->getNoun('MOD_TOPBLOGS_COMMENTS', $post->commentCount, true);?>
			</a>
		</div>
	<?php } ?>

	<?php if ($params->get('showreadmore', true)) { ?>
		<div class="mod-cell">
			<a href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('MOD_TOPBLOGS_READMORE'); ?></a>
		</div>
	<?php } ?>
</div>
<?php } ?>