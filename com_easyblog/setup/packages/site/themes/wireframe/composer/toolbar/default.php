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
<div class="eb-comp-toolbar">

	<?php if ($this->isMobile() || $this->isTablet()) { ?>
	<div class="eb-comp-toolbar__mobile-popup">
		<div class="dropdown_">
			<a href="javascript:void(0);" class="eb-comp-toolbar__btn-option btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-bp-toggle="dropdown">
				<i class="fdi fa fa-ellipsis-h"></i>
			</a>

			<div class="dropdown-menu dropdown-menu--eb-comp-toolbar">
				<div>
					<div class="eb-publish-item t-px--md t-py--md">
						<?php echo $this->output('site/composer/toolbar/lightdark'); ?>
					</div>

					<div class="eb-publish-item" data-composer-mobile-info>
						<div class="t-d--flex t-px--md t-py--md">
							<div class="t-d--flex t-flex-grow--1 t-align-items--c">
								<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
									<i class="fdi fa fa-cog fa-fw" data-eb-icon="" data-loader="fdi fas fa-circle-notch fa-spin" data-original=""></i>
								</div>
								<div>
									<div class="eb-publish-item__title t-text--500 t-font-weight--bold">
										<?php echo JText::_('COM_EB_SETTINGS');?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="eb-publish-item" data-composer-preview>
						<div class="t-d--flex t-px--md t-py--md">
							<div class="t-d--flex t-flex-grow--1 t-align-items--c">
								<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
									<i class="fdi fa fa-eye fa-fw" data-eb-icon="" data-loader="fdi fas fa-circle-notch fa-spin" data-original=""></i>
								</div>
								<div>
									<div class="eb-publish-item__title t-text--500 t-font-weight--bold">
										<?php echo JText::_('COM_EASYBLOG_PREVIEW_POST');?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php if (!$isWebview) { ?>
					<div class="eb-publish-item" data-composer-mobile-exit data-url="<?php echo $returnUrl;?>">
						<div class="t-d--flex t-px--md t-py--md">
							<div class="t-d--flex t-flex-grow--1 t-align-items--c">
								<div class="eb-publish-item__icon t-pr--md t-text--500 t-font-size--03">
									<i class="fdi fa fa-long-arrow-alt-left fa-fw" data-eb-icon="" data-loader="fdi fas fa-circle-notch fa-spin" data-original=""></i>
								</div>
								<div>
									<div class="eb-publish-item__title t-text--danger t-font-weight--bold">
										<?php echo JText::_('COM_EB_EXIT_COMPOSER'); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>
	<?php } ?>

	<?php if (!$isWebview) { ?>
	<div class="eb-comp-toolbar__back">
		<a href="javascript:void(0);" class="btn eb-comp-toolbar__btn-back" data-url="<?php echo $returnUrl;?>"
			data-composer-exit
			data-eb-provide="tooltip"
			data-placement="bottom"
			data-title="<?php echo JText::_('COM_EASYBLOG_BACK');?>"
		>
			<i class="fdi fa fa-long-arrow-alt-left"></i>
		</a>
		<div class="eb-comp-toolbar__divider"></div>
	</div>
	<?php } ?>

	<?php if ($this->config->get('layout_composer_history') && !$templateEditor) { ?>
		<div class="eb-comp-toolbar__revision">
		<?php echo $this->output('site/composer/toolbar/revisions'); ?>
		</div>
	<?php } ?>

	<div class="eb-comp-toolbar__mobile-nav">

		<?php if ($post->isBlank() || $post->isPublished() || ($post->isDraft() && !$post->isPostUnpublished())) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-savedraft btn eb-comp-toolbar__nav-btn" data-composer-save-draft>
			<?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON');?>
		</a>
		<?php } ?>

		<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-primary" data-composer-publish>
			<?php echo JText::_('COM_EASYBLOG_PUBLISH');?>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && (!$post->isDraft() || ($post->isDraft() && $post->isPostUnpublished()) )) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn<?php echo $post->isUnpublished() || $post->isPostUnpublished() || $post->isPending() ? ' btn-eb-default' : ' btn-eb-primary'; ?>" data-composer-update>
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST');?>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && !$post->isNew() && $post->isDraft() && !$post->isPostUnpublished()) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-primary" data-composer-publish>
			<?php echo JText::_('COM_EASYBLOG_PUBLISH');?>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-success" data-composer-submit-approval>
			<?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
		</a>
		<?php } ?>

		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-option btn eb-comp-toolbar__nav-btn" data-composer-mobile-info>
			<i class="fdi fa fa-ellipsis-h"></i>
		</a>
	</div>

	<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
	<div class="eb-comp-toolbar__nav toolbar-dropping" data-toolbar-blocks>
		<div class="eb-block-hint">
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_DROP_DESC');?>
		</div>

		<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-drop-button>
			<?php echo JText::_('COM_EASYBLOG_CANCEL');?>
		</button>
	</div>

	<div class="eb-comp-toolbar__nav toolbar-moving" data-toolbar-blocks>
		<div class="eb-block-hint eb-block-hint-moving">
			<i class="fdi fa fa-arrows"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MOVE_DESC');?>
		</div>

		<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-move>
			<?php echo JText::_('COM_EASYBLOG_CANCEL');?>
		</button>
	</div>
	<?php } ?>

	<div class="eb-comp-toolbar__nav toolbar-composing" data-toolbar-composing>
		<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
			<?php if (!$postTemplateIsLocked) { ?>
				<?php echo $this->output('site/composer/toolbar/blocks'); ?>
			<?php } ?>
		<?php } else { ?>
			<?php echo $this->output('site/composer/toolbar/blocks.legacy'); ?>
		<?php } ?>

		<?php if ($post->isLegacy()) { ?>
		<div class="btn-group" data-toolbar-view data-type="video">
			<button type="button" class="btn eb-comp-toolbar__nav-btn" data-eb-composer-embed-video data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_EMBED_VIDEO');?>" data-placement="bottom" data-eb-provide="tooltip">
				<i class="fdi fa fa-film fa-fw"></i>
			</button>
		</div>
		<?php } ?>

		<?php if (!$postTemplateIsLocked) { ?>
		<div class="btn-group">
			<button type="button" class="btn eb-comp-toolbar__nav-btn" data-eb-composer-media data-uri="post"
				data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA');?><?php echo $this->html('composer.shortcut', ['shift', 'm']); ?>"
				data-html="1"
				data-placement="bottom"
				data-eb-provide="tooltip"
			>
				<i class="fdi fa fa-photo-video fa-fw"></i>
			</button>
		</div>

			<?php echo $this->output('site/composer/toolbar/posts'); ?>
		<?php } ?>

		<?php if ($this->config->get('main_locations') && !$templateEditor) { ?>
			<?php echo $this->output('site/composer/toolbar/location'); ?>
		<?php } ?>

		<?php if (!$templateEditor) { ?>
			<?php echo $this->output('site/composer/toolbar/cover'); ?>
		<?php } ?>

		<?php if (!$templateEditor && !$post->isLegacy() && $post->isBlank() && !$this->config->get('composer_templates') && EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE)->isEnabled()) { ?>
			<?php echo $this->output('site/composer/toolbar/googleimport'); ?>
		<?php } ?>
	</div>

	<?php if (!$this->isMobile() || $this->isTablet()) { ?>
	<div class="eb-comp-toolbar__light-dark t-align-items--c sm:t-d--none lg:t-d--flex">
		<?php echo $this->output('site/composer/toolbar/lightdark'); ?>
	</div>
	<?php } ?>

	<div class="eb-comp-toolbar__action">
		<div class="eb-comp-toolbar__divider"></div>

		<?php if ($isShortCutsEnabled) { ?>
		<div class="btn-group">
			<button type="button" class="btn eb-comp-toolbar__nav-btn eb-comp-toolbar__btn-shortcut" data-composer-shortcuts-button
				data-eb-provide="tooltip"
				data-html="1"
				data-title="<?php echo JText::_('COM_EB_SHORTCUTS');?><?php echo $this->html('composer.shortcut', ['shift', '/']); ?>"
				data-placement="bottom"
			>
				<i class="fdi far fa-question-circle fa-fw"></i>
			</button>
		</div>
		<?php } ?>

		<div class="btn-group sm:t-d--none md:t-d--none">
			<button type="button" class="btn eb-comp-toolbar__nav-btn is-active"
				data-eb-composer-toggle-sidebar
				data-html="1"
				data-title="<?php echo JText::_('COM_EB_SETTINGS');?><?php echo $this->html('composer.shortcut', ['shift', '\\']); ?>"
				data-eb-provide="tooltip"
				data-placement="bottom">
				<i class="fdi fa fa-cog fa-fw"></i>
			</button>
		</div>

		<div class="btn-group sm:t-d--none md:t-d--none">
			<button type="button" class="btn eb-comp-toolbar__nav-btn" data-composer-preview
				data-eb-provide="tooltip"
				title="<?php echo JText::_('COM_EASYBLOG_PREVIEW_POST');?>"
				data-placement="bottom"
			>
				<i class="fdi far fa-eye fa-fw"></i>
			</button>
		</div>

		<?php if ($templateEditor) { ?>
			<?php echo $this->output('site/composer/toolbar/actions.template'); ?>
		<?php } else { ?>
			<?php echo $this->output('site/composer/toolbar/actions'); ?>
		<?php } ?>
	</div>
</div>
