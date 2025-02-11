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
<div class="eb-side-main-action" data-show-undo>
	<div class="eb-composer-actions" data-eb-composer-form="actions">
	<?php if ($templateEditor) { ?>

		<!-- create or updating template -->
		<?php if ((!$postTemplate->id && $postTemplate->canCreate()) || ($postTemplate->canCreate() && $postTemplate->isOwner())) { ?>
		<?php echo $this->output('site/composer/sidebar/templates/default'); ?>
		<?php } ?>

		<!-- previewing template -->
		<?php if ($postTemplate->id && !$postTemplate->isOwner() || ($postTemplate->id && !$postTemplate->canCreate())) { ?>
		<?php echo $this->output('site/composer/sidebar/templates/preview'); ?>
		<?php } ?>

	<?php } else { ?>

		<!-- for admin -->
		<?php if (($this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('publish_entry'))) && $post->isPending()) { ?>
		<a href="javascript:void(0);" class="btn btn-eb-primary btn--lg btn-block btn-approve" data-composer-approve>
			<?php echo JText::_('COM_EASYBLOG_APPROVE_AND_PUBLISH_POST'); ?><br />
			<span class="btn-hint"><?php echo JText::_('COM_EASYBLOG_APPROVE_AND_PUBLISH_POST_BUTTON_TIPS'); ?></span>
		</a>
		<?php } ?>

		<!-- for user to has the publishig rights -->
		<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="btn btn-eb-primary btn--lg btn-block btn-publish" data-composer-publish data-undo-enabled="<?php echo $undoPublishing; ?>">
			<span class="eb-publish-text"><?php echo JText::_('COM_EASYBLOG_PUBLISH_POST'); ?></span>
			<span class="eb-publishing-text" data-counting-down><?php echo JText::_('COM_EB_PUBLISHING_POST'); ?></span>
			<span class="btn-hint">
				<?php echo JText::_('COM_EASYBLOG_PUBLISH_POST_BUTTON_TIPS'); ?>
			</span>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && (!$post->isDraft() || ($post->isDraft() && $post->isPostUnpublished()) )) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="btn<?php echo $post->isUnpublished() || $post->isPostUnpublished() || $post->isPending() ? ' btn-eb-default' : ' btn-eb-primary'; ?> btn--lg btn-block btn-update" data-composer-update>
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?><br />
			<span class="btn-hint">
			<?php if ($post->isScheduled()) { ?>
				<?php echo JText::_('COM_EASYBLOG_UPDATE_SCHEDULED_POST_TIPS'); ?>
			<?php } else if (!$post->isUnpublished() && !$post->isPostUnpublished() && !$post->isPending()) { ?>
				<?php echo JText::_('COM_EASYBLOG_UPDATE_POST_TIPS'); ?>
			<?php } ?>
			</span>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && !$post->isNew() && $post->isDraft() && !$post->isPostUnpublished()) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="btn btn-eb-primary btn--lg btn-block btn-update" data-composer-publish >
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?><br />
			<span class="btn-hint"><?php echo JText::_('COM_EASYBLOG_UPDATE_POST_TIPS'); ?></span>
		</a>
		<?php } ?>


		<!-- for user to doesnt has the publishig rights -->
		<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="btn btn-eb-success btn--lg btn-block btn-submit-approval" data-composer-submit-approval>
			<span><?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?></span><br />
			<span class="btn-hint"><?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL_BUTTON_TIPS'); ?></span>
		</a>
		<?php } ?>

		<div class="eb-side-main-action__undo-info">
			<a href="javascript:void(0);" class="" data-undo-publish><?php echo JText::_('COM_EB_UNDO_PUBLISHING'); ?></a>
			<a href="javascript:void(0);" class="" data-publish-now><?php echo JText::_('COM_EB_PUBLISH_NOW'); ?></a>
		</div>

		<?php if ($post->isBlank() || $post->isPublished() || ($post->isDraft() && !$post->isPostUnpublished())) { ?>
		<a href="javascript:void(0);" class="btn btn-eb-default btn--lg btn-block btn-draft" data-composer-save-draft>
			<?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON');?>
		</a>
		<?php } ?>

		<div class="eb-side-main-action__note t-text--muted t-hidden" data-composer-autosave-x>
			<span class="eb-hint-text">
				<i class="fdi far fa-clock"></i>&nbsp; <span data-composer-autosave-message-x></span>
			</span>
		</div>

		<div class="eb-side-main-action__ft t-lg-mt--md">
			<div>
				<a href="javascript:void(0);" class="btn btn-eb-default-o btn-sm" data-composer-preview>
					<i class="fdi fa fa-eye"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_PREVIEW_POST'); ?>
				</a>
			</div>
			<div>
				<?php if ($post->isPending() && $post->canModerate()) { ?>
				<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-sm" data-composer-reject>
					<i class="fdi fa fa-ban"></i>&nbsp;  <?php echo JText::_('COM_EASYBLOG_COMPOSER_REJECT_POST_BUTTON'); ?>
				</a>
				<?php } else if (!$post->isBlank()) { ?>
				<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-sm" data-composer-trash>
					<i class="fdi fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MOVE_TO_TRASH'); ?>
				</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>


