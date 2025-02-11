<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-reactions mt-10" data-reactions data-id="<?php echo $post->id;?>">
	<div class="eb-reactions__options">
		<div class="eb-reaction-option">
			<div class="eb-reaction-option__link">
				<div class="eb-reaction-option__text">
					<?php echo JText::_('COM_EASYBLOG_REACTIONS_HOW_DO_YOU_FEEL');?>
				</div>
			</div>
		</div>
	</div>

	<div class="eb-reactions__results">
		<div class="eb-reaction-state">
			<?php foreach ($reactions as $reaction) { ?>
			<a href="javascript:void(0);" class="eb-reaction-state__item <?php echo $userReaction && $userReaction->type == $reaction->type ? ' is-active' : '';?>" 
				data-reaction="<?php echo $reaction->type;?>" data-id="<?php echo $reaction->id;?>">
				<i class="eb-reaction-state__icon eb-emoji-icon eb-emoji-icon--<?php echo $reaction->type;?>"></i>
				<div class="eb-reaction-state__counter">
					<b><?php echo JText::_('COM_EASYBLOG_REACTION_' . strtoupper($reaction->type));?></b> (<span data-count><?php echo $reaction->total;?></span>)
				</div>
			</a>
			<?php } ?>
		</div>
	</div>
</div>