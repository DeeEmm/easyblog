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
<div class="btn-group t-lg-ml--lg t-w--100" data-composer-toolbar-actions data-toolbar-view="actions">
	<button type="button" class="btn btn-eb-primary dropdown-toggle_ eb-comp-toolbar__btn-publish" data-composer-action-button>

		<?php // User with publishing rights ?>
		<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
			<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
		<?php } ?>

		<?php if (($this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('publish_entry'))) && $post->isPending()) { ?>
			<?php // Admin moderation options ?>
			<?php echo JText::_('COM_EASYBLOG_APPROVE'); ?>
		<?php } ?>

		<?php // Users without publishig rights ?>
		<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
			<?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
		<?php } ?>

		<?php // User is updating a published post ?>
		<?php if ((!$post->isPending() && !$post->isBlank() && (!$post->isDraft() || ($post->isDraft() && $post->isPostUnpublished()))) && $this->acl->get('publish_entry')) { ?>
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
		<?php } ?>

		<?php // User is updating an unpublished revision but published post ?>
		<?php if ((!$post->isBlank() && !$post->isNew() && $post->isDraft() && !$post->isPostUnpublished()) && $this->acl->get('publish_entry')) { ?>
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
		<?php } ?>

		&nbsp;<i class="fdi fa fa-chevron-down t-lg-ml--md"></i>
	</button>

	<div class="dropdown-menu dropdown-menu--right dropdown-menu--eb-comp-toolbar" data-eb-composer-form="actions">
		<div>
			<?php // Users with publishing rights  ?>
			<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
			<div class="eb-publish-item" data-composer-publish data-undo-enabled="<?php echo $undoPublishing; ?>">
				<div class="t-d--flex t-align-items--c t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c t-min-width--0">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-arrow-up', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--primary t-font-weight--bold">
								<?php echo JText::_('COM_EB_PUBLISH_NOW'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500 t-text--wrap" data-publish-tips>
								<?php echo JText::_('COM_EASYBLOG_PUBLISH_POST_BUTTON_TIPS'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500 t-hidden" data-undo-tips>
								<?php echo JText::_('COM_EB_PUBLISHING_POST'); ?>
							</div>
						</div>
					</div>

					<div class="eb-publish-item__action t-align-items--fe t-flex-shrink--0 t-pl--md t-hidden" data-undo-action-wrapper>
						<div class="l-cluster l-spaces--2xs">
							<div>
								<a href="javascript:void(0);" data-cancel-publish>
									<?php echo JText::_('COM_EB_CANCEL'); ?>
								</a>
								<div>&middot;</div>
								<a href="javascript:void(0);" data-publish-now>
									<?php echo JText::_('COM_EB_PUBLISH_NOW'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="eb-publish-item__countdown-bar" style="width: 0%" data-publishing-progress></div>
			</div>
			<?php } ?>

			<!-- Admin moderation options -->
			<?php if (($this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('publish_entry'))) && $post->isPending()) { ?>
			<div class="eb-publish-item" data-composer-approve>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--success t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-arrow-up', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--success t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_APPROVE_AND_PUBLISH_POST'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500">
								<?php echo JText::_('COM_EB_APPROVE_POST_TIPS'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php // User is updating published post ?>
			<?php if ((!$post->isBlank() && (!$post->isDraft() || ($post->isDraft() && $post->isPostUnpublished()))) && $this->acl->get('publish_entry')) { ?>
			<div class="eb-publish-item" data-composer-update>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-arrow-up', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--800 t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500">
								<?php if ($post->isScheduled()) { ?>
									<?php echo JText::_('COM_EASYBLOG_UPDATE_SCHEDULED_POST_TIPS'); ?>
								<?php } else if (!$post->isUnpublished() && !$post->isPostUnpublished() && !$post->isPending()) { ?>
									<?php echo JText::_('COM_EASYBLOG_UPDATE_POST_TIPS'); ?>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php // User is updating an unpublished revision but published post ?>
			<?php if ((!$post->isBlank() && !$post->isNew() && $post->isDraft() && !$post->isPostUnpublished()) && $this->acl->get('publish_entry')) { ?>
			<div class="eb-publish-item" data-composer-publish>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-arrow-up', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--800 t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500">
								<?php echo JText::_('COM_EASYBLOG_UPDATE_POST_TIPS'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php // Users without publishig rights ?>
			<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
			<div class="eb-publish-item" data-composer-submit-approval>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-arrow-up', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--primary t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
							</div>
							<div class="eb-publish-item__desc t-text--500">
								<?php echo JText::_('COM_EB_SUBMIT_FOR_APPROVALS_TIPS'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php // Save as draft ?>
			<?php if ($post->isBlank() || $post->isPublished() || ($post->isDraft() && !$post->isPostUnpublished())) { ?>
			<div class="eb-publish-item" data-composer-save-draft>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi far fa-save', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--800 t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON');?>
							</div>
							<div class="eb-publish-item__desc t-text--500 t-hidden" data-composer-autosave-message></div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->config->get('composer_templates') && (FH::isSiteAdmin() || $this->acl->get('create_post_templates'))) { ?>
			<div class="eb-publish-item" data-composer-save-template>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fas fa-columns', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--800 t-font-weight--bold<?php echo $post->getPostTemplateId() ? ' t-hidden' : ''; ?>" data-template-button-save>
								<?php echo JText::_('COM_EB_SAVE_AS_POST_TEMPLATE');?>
							</div>
							<div class="eb-publish-item__title t-text--800 t-font-weight--bold<?php echo $post->getPostTemplateId() ? '' : ' t-hidden'; ?>" data-template-button-update>
								<?php echo JText::_('COM_EB_UPDATE_POST_TEMPLATE');?>
							</div>
							<div class="eb-publish-item__desc t-text--500 t-hidden">
								<?php echo JText::_('COM_EB_SAVE_AS_POST_TEMPLATE_TIPS'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if ($post->isPending() && $post->canModerate()) { ?>
			<div class="eb-publish-item" data-composer-reject>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--danger t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi fa fa-ban', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--danger t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_REJECT_POST_BUTTON'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } else if (!$post->isBlank()) { ?>
			<div class="eb-publish-item" data-composer-trash>
				<div class="t-d--flex t-px--md t-py--md">
					<div class="t-d--flex t-flex-grow--1 t-align-items--c">
						<div class="eb-publish-item__icon t-pr--md t-text--danger t-font-size--03">
							<?php echo $this->fd->html('icon.font', 'fdi far fa-trash-alt', true); ?>
						</div>
						<div>
							<div class="eb-publish-item__title t-text--danger t-font-weight--bold">
								<?php echo JText::_('COM_EASYBLOG_MOVE_TO_TRASH'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
