<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($params->get('showhits' , false) || $params->get('showcommentcount', false) || $params->get('showreadmore', true)) { ?>
<div class="eb-mod-foot mod-muted mod-small">
	<?php if ($params->get('showhits' , false)) { ?>
		<span><?php echo $post->hits;?> <?php echo JText::_( 'MOD_EASYBLOGRELATED_HITS' );?></span>
	<?php } ?>

	<?php if ($params->get('showcommentcount', false)) { ?>
		<span><a href="<?php echo $post->getCommentsPermalink(); ?>"><?php echo $post->commentCount;?> <?php echo JText::_('MOD_EASYBLOGRELATED_COMMENTS'); ?></a></span>
	<?php } ?>

	<?php if( $params->get('showreadmore', true)) { ?>
		<span><a href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('MOD_EASYBLOGRELATED_READMORE'); ?></a></span>
	<?php } ?>
</div>
<?php } ?>

<?php if ($params->get('showratings', false) && $post->showRating) { ?>
	<div class="eb-rating">
		<?php echo EB::ratings()->html($post, 'ebrelatedpost-' . $post->id . '-ratings', JText::_('MOD_EASYBLOGRELATED_RATEBLOG'), !$params->get('enableratings', false)); ?>
	</div>
<?php } ?>