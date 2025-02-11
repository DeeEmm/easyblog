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
<div class="eb-reactions mt-10" data-reactions data-id="<?php echo $post->id;?>">

	<h3 class="uk-heading-line uk-text-center">
		<span><?php echo JText::_('COM_EASYBLOG_REACTIONS_HOW_DO_YOU_FEEL');?></span>
	</h3>

	<div class="uk-child-width-expand@s uk-text-center" uk-grid>
		<?php foreach ($reactions as $reaction) { ?>
		<div>
			<a href="javascript:void(0);" class=" <?php echo $userReaction && $userReaction->type == $reaction->type ? ' is-active' : '';?>"
				data-reaction="<?php echo $reaction->type;?>" data-id="<?php echo $reaction->id;?>">
				<i class="eb-reaction-state__icon eb-emoji-icon eb-emoji-icon--<?php echo $reaction->type;?>"></i>
				<div class="uk-text-small">
					<b><?php echo JText::_('COM_EASYBLOG_REACTION_' . strtoupper($reaction->type));?></b> (<span data-count><?php echo $reaction->total;?></span>)
				</div>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
