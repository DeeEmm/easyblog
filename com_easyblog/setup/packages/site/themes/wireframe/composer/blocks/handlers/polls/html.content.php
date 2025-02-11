<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-poll" data-poll-form data-id="<?php echo $pollId; ?>">
	<div class="eb-poll__title" data-poll-title>
		<?php echo $pollTitle; ?>
	</div>

	<?php foreach ($items as $item) { ?>
		<?php $content = isset($item->content) && $item->content ? $item->content : $defaultItem; ?>
		<div class="o-form-group is-checkbox <?php echo $isMultiple ? '' : 't-hidden'; ?>" data-poll-item>
			<div class="checkbox disabled">
				<label>
					<input type="checkbox" disabled="disabled">
					<span>
						<?php echo $content; ?>
					</span>
				</label>
			</div>
		</div>

		<div class="o-form-group is-radio <?php echo !$isMultiple ? '' : 't-hidden'; ?>" data-poll-item>
			<div class="radio disabled">
				<label>
					<input type="radio" disabled="disabled">
					<span>
						<?php echo $content; ?>
					</span>
				</label>
			</div>
		</div>
	<?php } ?>

	<?php if ($canEdit) { ?>
	<span class="o-input-group__btn">
		<button type="button" class="btn btn-eb-default-o btn--sm mt-20 t-hidden" data-poll-update-button>
			<?php echo JText::_('COM_EB_BLOCKS_POLL_UPDATE_BUTTON');?>
		</button>
	</span>
	<?php } ?>
</div>
