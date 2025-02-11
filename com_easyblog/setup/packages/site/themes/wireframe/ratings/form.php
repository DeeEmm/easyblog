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
<form id="<?php echo $elementId; ?>-form"
	class="eb-rating-form<?php echo $voted ? ' voted' : '';?>"
	data-id="<?php echo $uid;?>"
	data-type="<?php echo $type;?>"
	data-rating-form
>
	<?php echo $this->fd->html('rating.item', [
		'reset' => false,
		'readOnly' => $locked,
		'score' => $rating,
		'lockedMessage' => $lockedMessage
	]); ?>

	<div class="col-cell eb-rating-voters">
		<?php if ($this->config->get('main_ratings_display_raters')) { ?>
		<a class="eb-rating-link" href="javascript:void(0);" data-rating-voters>
		<?php } ?>

			<b class="eb-ratings-value" title="<?php echo JText::sprintf('COM_EASYBLOG_RATINGS_TOTAL_VOTES', $total, $this->getNouns('COM_EASYBLOG_RATINGS_VOTES_COUNT', $total));?>" data-rating-value>
				<span data-rating-total><?php echo $total;?></span>

				<b><i class="fdi fa fa-check"></i></b>
			</b>

		<?php if ($this->config->get('main_ratings_display_raters')) { ?>
		</a>
		<?php } ?>
	</div>
</form>