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
<div class="ebd-block mt-20 mb-20">
	<div class="eb-poll" data-id="<?php echo $poll->id; ?>" data-multiple="<?php echo $isMultiple; ?>" data-unvote="<?php echo $poll->allow_unvote; ?>" data-poll-wrapper>
		<form id="pollsForm" name="pollsForm" class="form-horizontal">
			<div class="t-hidden" data-poll-error>
				<?php echo $this->fd->html('alert.standard', '', 'danger'); ?>
			</div>

			<div class="eb-poll__title">
				<?php echo JText::_($poll->title); ?>
			</div>

			<div class="eb-poll__list" data-poll-list>
				<?php foreach($poll->items as $item) { ?>
				<div class="eb-poll__item <?php echo $isMultiple ? 'checkbox' : 'radio'; ?>"
					data-poll-item-wrapper
					data-id="<?php echo $item->id; ?>">

					<input class="<?php echo $item->voted ? 'is-voted' : ''; ?>" type="<?php echo $isMultiple ? 'checkbox' : 'radio'; ?>" <?php echo $isMultiple ? '' : 'name="eb-poll-' . $poll->id . '"'; ?> 
						<?php echo $disabled ? 'disabled' : ''; ?>
						<?php echo $item->voted ? 'checked' : ''; ?>
						data-poll-input
						/>

					<label class="eb-poll__label">
						<div class="eb-poll__option-txt">
							<span data-poll-option>
								<?php echo JText::_($item->value); ?>
							</span>
						</div>
						<a href="javascript:void(0);" class="eb-poll__count" data-poll-count>
							<?php echo $item->votesText; ?>
						</a>
					</label>

					<?php $percentage = $totalVotes > 0 ? (int) $item->count / $totalVotes * 100 : 0; ?>
					<div class="eb-poll__progress progress progress--eb">
						<div class="progress-bar progress-bar-primary" data-progress-bar style="<?php echo $percentage > 0 ? 'width: ' . $percentage . '%;' : ''; ?>"></div>
					</div>
				</div>
				<?php } ?>
			</div>
		</form>

		<div class="eb-poll-meta">
			<?php if ($hasExpirationDate && !$hasExpired) { ?>
			<div class="eb-poll__count">
				<?php echo $poll->expiry_date; ?>
			</div>
			<?php } ?>

			<?php if ($hasExpired) { ?>
			<div class="eb-poll__count">
				<?php echo $poll->expired; ?>
			</div>
			<?php } ?>

			<div class="eb-poll__count" data-poll-total-votes>
				<?php echo $poll->totalVotesText; ?>
			</div>
		</div>
	</div>
</div>
