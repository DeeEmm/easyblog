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
<form method="post" id="adminForm" data-id="<?php echo $poll->id ? $poll->id : ''; ?>" action="<?php echo JRoute::_('index.php');?>" class="eb-composer-poll-form" data-eb-poll-form>
	<?php if ($isComposer && !empty($userPolls)) { ?>
	<div class="">
		<div class="eb-composer-fieldset-content">
			<?php echo $this->html('composer.field.radio', 'poll_form', 'COM_EB_POLL_FORM_SELECT_SECTION', null, ['checked' => $isComposer && !$poll->id ? true : false, 'attributes' => 'data-eb-poll-form-select']); ?>
		</div>
	</div>

	<div class=" <?php echo $poll->id || !$isComposer ? 't-hidden': ''; ?>" data-eb-poll-form-select-section>
		<div class="eb-composer-fieldset-content o-form-horizontal">
			<div class="o-form-group">
				<?php echo $this->html('composer.field.dropdown', 'unassociated_post_polls', '-1', $userPollsList, ['class' => 'o-form-control', 'attributes' => 'data-unassociated-post-polls-list']); ?>
			</div>
		</div>
	</div>

	<div class="">
		<div class="eb-composer-fieldset-content">
			<?php echo $this->html('composer.field.radio', 'poll_form', $pollFormSectionTitle, null, ['checked' => $showNewPollForm ? true : false, 'disabled' => false, 'attributes' => 'data-eb-poll-form-create']); ?>
		</div>
	</div>
	<?php } ?>

	<?php if (EB::isSiteAdmin() || (!$poll->id && $this->acl->get('polls_create')) || ($poll->id && $this->acl->get('polls_edit'))) { ?>
	<div class=" <?php echo $showNewPollForm ? '': 't-hidden'; ?>" data-eb-poll-form-content-section>
		<div class="form-group">
			<label for="poll_title" class="t-font-weight--normal">
				<?php echo JText::_('COM_EB_POLL_FORM_TITLE'); ?>
			</label>

			<div>
				<?php echo $this->fd->html('form.text', 'poll_title', $poll->title, null, ['class' => ' t-border--300', 'placeholder' => JText::_('COM_EB_POLL_DEFAULT_TITLE'), 'attr' => 'data-eb-poll-form-title']); ?>
			</div>
		</div>

		<div class="form-group mt-20">
			<label for="poll_options" class="t-font-weight--normal">
				<?php echo JText::_('COM_EB_POLL_FORM_OPTIONS'); ?>
			</label>
		</div>
		<div class="eb-poll-form-items-list l-stack l-spaces--xs">
			<?php foreach ($poll->items as $item) { ?>
			<div class="o-form-multi-item" data-eb-poll-form-item>
				<div class="o-form-multi-item__name">
					<?php echo $this->fd->html('form.text', 'poll_items', $this->escape($item->content), null, ['baseClass' => 'o-form-multi-item__input', 'placeholder' => JText::_('COM_EB_POLL_DEFAULT_OPTION_TITLE')]); ?>
				</div>

				<div class="o-form-multi-item__remove" data-eb-poll-form-remove-button>
					&times;
				</div>

				<?php echo $this->fd->html('form.hidden', 'items[]', '', '', 'data-eb-poll-form-item-input'); ?>
			</div>
			<?php } ?>
		</div>

		<div class="form-group t-mt--md">
			<div class="t-d--flex t-justify-content--fe t-align-items--c is-add">
				<div>
					<a href="javascript:void(0);" class="t-no-focus-outline t-text--primary-500" data-eb-poll-form-add-button>
						<?php echo JText::_('COM_EB_POLL_FORM_ADD_NEW_ITEM'); ?> 
					</a>
				</div>
			</div>
		</div>

		<div class="form-group t-d--flex t-mt--md" data-eb-poll-form-multiple>
			<?php echo $this->fd->html('form.label', 'COM_EB_POLL_SETTINGS_ALLOW_MULTIPLE_CHOICES', 'poll_multiple_allowed', '', '', true, ['class' => 't-pl--no t-font-weight--normal']); ?>

			<div class="o-control-input">
				<?php echo $this->fd->html('form.toggler', 'multiple', $poll->multiple, '', 'data-poll-multiple'); ?>
			</div>
		</div>

		<div class="form-group t-d--flex t-mt--md" data-eb-poll-form-unvote>
			<?php echo $this->fd->html('form.label', 'COM_EB_POLL_SETTINGS_ALLOW_UNVOTE', 'poll_unvote_allowed', '', '', true, ['class' => 't-pl--no t-font-weight--normal']); ?>

			<div class="o-control-input">
				<?php echo $this->fd->html('form.toggler', 'unvote_allowed', $poll->allow_unvote, '', 'data-poll-unvote'); ?>
			</div>
		</div>

		<div class="form-group t-d--flex t-mt--md" data-eb-poll-form-expiration>
			<?php echo $this->fd->html('form.label', 'COM_EB_POLL_EXPIRATION_DATE', 'poll_expiration_date', '', '', true, ['class' => 't-pl--no t-font-weight--normal']); ?>

			<?php echo $this->html('composer.field.calendar', 'expiry_date', $poll->hasExpirationDate ? $poll->expiry_date : '', '', ['placeholder' => EASYBLOG_DATE_PLACEHOLDER]); ?>
		</div>
	</div>
	<?php } ?>

	<?php echo $this->fd->html('form.hidden', 'id', $poll->id); ?>
	<?php echo $this->fd->html('form.action'); ?>
</form>
