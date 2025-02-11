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
<form name="composer" method="post" action="<?php echo JRoute::_('index.php');?>" data-composer-form autocomplete="off">
	<div data-composer-manager
	class="eb-comp
		<?php echo $this->isMobile() || $this->isTablet() || $this->isIpad() ? '' : ' is-sidebar-open';?>
		<?php if (!$templateEditor) { ?>
			<?php echo $post->isBlank() && $this->config->get('composer_templates') && $postTemplates ? ' show-templates' : '';?>
			<?php echo $draftRevision ? ' warning-draft' : ''; ?>
			<?php echo $post->renderClassnames(); ?>
		<?php } else { ?>
		is-editing-template
		<?php } ?>
		"
	>
		<div class="eb-comp-mobile-header">
			<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
			<div class="eb-comp-toolbar__nav toolbar-dropping" data-toolbar-blocks>

				<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-drop-button>
					<?php echo JText::_('COM_EASYBLOG_CANCEL');?>
				</button>
			</div>

			<div class="eb-comp-toolbar__nav toolbar-moving" data-toolbar-blocks>

				<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-move>
					<?php echo JText::_('COM_EASYBLOG_CANCEL');?>
				</button>
			</div>
			<?php } ?>

			<?php echo $onEasyblogPrepareEditForm; ?>

			<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
			<div class="eb-comp-mobile-header__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-header__link" data-toolbar-blocks>
					<i class="fdi fa fa-layer-group"></i>
					<div class="eb-comp-mobile-header__link-text"><?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?></div>
				</a>
			</div>
			<?php } ?>
			<div class="eb-comp-mobile-header__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-header__link" data-eb-composer-media data-uri="post">
					<i class="fdi fa fa-photo-video"></i>
					<div class="eb-comp-mobile-header__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA');?></div>
				</a>
			</div>
			<div class="eb-comp-mobile-header__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-header__link" data-composer-mobile-cover>
					<i class="fdi far fa-image"></i>
					<div class="eb-comp-mobile-header__link-text"><?php echo JText::_('COM_EASYBLOG_POST_COVER');?></div>
				</a>
			</div>

			<div class="eb-comp-mobile-header__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-header__link" data-composer-mobile-posts>
					<i class="fdi far fa-file-alt"></i>
					<div class="eb-comp-mobile-header__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_TITLE_POSTS');?></div>
				</a>
			</div>

			<?php if ($this->config->get('main_locations')) { ?>
			<div class="eb-comp-mobile-header__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-header__link" data-composer-mobile-location>
					<i class="fdi far fa-compass"></i>
					<div class="eb-comp-mobile-header__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?></div>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('layout_composer_history') && !$templateEditor) { ?>
				<div class="eb-comp-mobile-revisions">
					<div class="dropdown_ eb-comp-toolbar__dropdown eb-comp-toolbar__revisions" data-toolbar-view data-type="revisions">
						<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?> /</span>

						<button type="button" class="eb-comp-toolbar__btn-revision dropdown-toggle_ t-text--800" data-composer-toolbar-revisions
						>
							Initial Post <i class="fdi fa fa-chevron-down t-lg-ml--md"></i>
						</button>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php echo $this->output('site/composer/toolbar/default'); ?>

		<div class="eb-comp__body">
			<div class="eb-alert-container">
				<div data-eb-alert-placeholder>
					<div data-eb-alert-template>
						<div class="o-alert o-alert--eb-composer o-alert--dismissible t-lg-mb--no t-hidden" data-composer-alert>
							<div class="t-flex-grow--1 l-stack l-spaces--xs">
								<div class="t-d--flex">
									<div class="t-flex-grow--1">
										<span data-composer-alert-message>
										</span>
									</div>

									<div class="t-flex-shrink--0 t-pl--md">
										<a href="javascript:void(0);" class="o-alert__close" data-eb-composer-alert-close>×</a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php if ($alert) { ?>
						<div class="o-alert o-alert--eb-composer o-alert--dismissible t-lg-mb--no <?php echo $alert ? 'o-alert--' . $alert->type : 't-hidden';?>" data-composer-alert>
							<div class="t-flex-grow--1 l-stack l-spaces--xs">
								<div class="t-d--flex">
									<div class="t-flex-grow--1">
										<span data-composer-alert-message>
											<?php if ($alert) { ?>
												<?php echo $alert->text;?>
											<?php } ?>
										</span>
									</div>

									<div class="t-flex-shrink--0 t-pl--md">
										<a href="javascript:void(0);" class="o-alert__close" data-eb-composer-alert-close>×</a>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="eb-revision-bar">
				<div class="eb-revision-bar__title">
					<div class="eb-revision-bar__txt" data-revisions-comparison-title></div>
				</div>

				<div class="eb-revision-bar__action">
					<div class="eb-revision-bar__close">
						<a href="javascript:void(0);" data-revisions-close-comparison>
							<i class="fdi fa fa-times-circle"></i>
						</a>
					</div>
				</div>
			</div>

			<div class="eb-composer-views" data-eb-composer-views data-post-template-is-locked="<?php echo $postTemplateIsLocked; ?>">
				<div class="eb-composer-view eb-composer-document active" data-eb-composer-view data-name="document" data-eb-composer-document>
					<div class="eb-composer-viewport" data-eb-composer-viewport>
						<div class="eb-composer-viewport-content" data-eb-composer-viewport-content>
							<div class="eb-composer-page">
								<div class="eb-composer-page-viewport" data-eb-composer-page-viewport>
									<div class="eb-composer-page-header" data-eb-composer-page-header>
										<div class="eb-composer-field-title">
											<textarea name="title" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE'); ?>" data-post-title data-post-empty-title-alert-message="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR'); ?>"><?php echo $this->fd->html('str.escape', $post->title); ?></textarea>
										</div>
									</div>

									<div class="eb-composer-page-body eb-editor--<?php echo $this->config->get('layout_editor'); ?>" data-eb-composer-page-body>
										<?php if ($post->isLegacy() || ($templateEditor && $postTemplate->isLegacy())) { ?>
											<?php  echo $this->output($legacyEditorNamespace); ?>
										<?php } else { ?>
											<?php echo $this->output('site/composer/editor/ebd'); ?>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php if (!$post->isLegacy() || ($templateEditor && !$postTemplate->isLegacy())) { ?>
				<div class="hide" data-eb-block-template>
					<?php echo $this->output('site/composer/document/blocks/editable'); ?>
				</div>
				<?php } ?>

				<div class="eb-composer-view eb-composer-revisions" data-eb-composer-view data-name="revisions" data-eb-composer-revisions>
					<div data-eb-composer-revisions-compare-screen></div>
				</div>
			</div>


			<?php if (!$post->isLegacy() || ($templateEditor && !$postTemplate->isLegacy())) { ?>
			<div class="eb-composer-blocks" data-eb-composer-blocks>
				<div class="eb-composer-blocks__hd">
					<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
						<i class="fdi fa fa-layer-group fa-fw"></i>
					</div>
					<?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?>
					<div class="eb-composer-blocks__hd-action">
						<a href="javascript:void(0);" class="eb-composer-blocks__close" data-toolbar-blocks-close>
							<i class="fdi fa fa-times-circle"></i>
						</a>
					</div>
				</div>
				<div class="eb-composer-blocks__bd">

					<div class="eb-composer-blocks__search">
						<input type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SEARCH');?>" data-eb-blocks-search />
					</div>

					<div class="eb-composer-blocks-group-container" data-eb-composer-blocks-group-container>
						<?php if ($this->config->get('composer_block_templates')) { ?>
						<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open<?php echo empty($blockTemplates) ? ' hide' : ''; ?>" data-eb-composer-block-section data-id="<?php echo strtolower('template');?>">
							<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
								<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_CATEGORY_TEMPLATE'); ?></strong>
								<i class="eb-composer-fieldset-header__icon t-lg-pull-right" data-panel-icon></i>
							</div>

							<div class="eb-composer-fieldset-content" data-eb-composer-block-section-content>
								<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group data-eb-composer-block-template-menu>
									<?php foreach ($blockTemplates as $block) { ?>
										<?php echo $this->output('site/composer/blocks/templates', ['block' => $block]); ?>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php $i = 0; ?>
						<?php foreach ($blocks as $category => $blockItems) { ?>
							<?php if ((count($blockItems) == 1 && $blockItems[0]->visible == true) || count($blockItems) > 1) { ?>
							<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$composerPreferences || $composerPreferences->get(strtolower($category), 'open') == 'open' ? 'is-open' : '';?>" data-eb-composer-block-section data-id="<?php echo strtolower($category);?>">
								<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
									<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_CATEGORY_' . strtoupper($category)); ?></strong>
									<i class="eb-composer-fieldset-header__icon" data-panel-icon></i>
								</div>

								<div class="eb-composer-fieldset-content" data-eb-composer-block-section-content>
									<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
										<?php foreach ($blockItems as $block) { ?>
										<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->visible ? $block->keywords : ''; ?>">
											<div>
												<i class="<?php echo $block->icon; ?>"></i>
												<span><?php echo $block->title; ?></span>
											</div>
											<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
										</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } else if (count($blockItems) == 1 && $blockItems[0]->visible != true) { ?>
							<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
								<?php foreach ($blockItems as $block) { ?>
								<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->visible ? $block->keywords : ''; ?>">
									<div>
										<i class="<?php echo $block->icon; ?>"></i>
										<span><?php echo $block->title; ?></span>
									</div>
									<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
								</div>
								<?php } ?>
							</div>
							<?php } ?>

							<?php $i++; ?>
						<?php } ?>

						<div class="o-empty">
							<div class="o-empty__content">
								<i class="o-empty__icon fdi fa fa-cube"></i>
								<div class="o-empty__text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NOT_FOUND'); ?></div>
							</div>
						</div>

						<div class="o-loader o-loader--top"></div>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="eb-comp__side">
				<div class="eb-comp-side-content">
					<?php echo $this->output('site/composer/panels/default'); ?>
				</div>
			</div>

			<?php if ($this->config->get('composer_templates') && !$templateEditor && $totalTemplates > 1) { ?>
				<?php echo $this->output('site/composer/templates'); ?>
			<?php } ?>

			<?php if (!$templateEditor && $googleImportEnabled) { ?>
				<?php echo $this->output('site/googleimport/composer/default'); ?>
			<?php } ?>

			<?php if ($draftRevision) { ?>
				<?php echo $this->output('site/composer/revisions/draft.warning'); ?>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="category_id" value="<?php echo $primaryCategory->id;?>" data-category-primary-input />
	<input type="hidden" name="document" value="" data-composer-field-document />
	<input type="hidden" name="published" value="<?php echo $post->published; ?>" data-composer-field-published />
	<input type="hidden" name="rejectMessage" value="" data-composer-reject-message />
	<input type="hidden" name="preview" value="0" data-composer-field-preview />
	<input type="hidden" name="return" value="<?php echo $returnUrl; ?>" />

	<?php if ($templateEditor) { ?>
		<input type="hidden" name="template_id" value="<?php echo $postTemplate ? $postTemplate->id : '';?>" data-eb-composer-post-template-id />
		<?php echo $this->fd->html('form.action', 'templates.save'); ?>
	<?php } else { ?>
		<input type="hidden" name="uid" value="<?php echo $post->uid; ?>" data-eb-composer-post-uid />
		<input type="hidden" name="id" value="<?php echo $post->id; ?>" data-eb-composer-post-id />
		<input type="hidden" name="revision_id" value="<?php echo $post->revision->id; ?>" data-eb-composer-revision-id />
		<input type="hidden" name="params[post_template_id]" value="<?php echo $postTemplateId; ?>" data-eb-composer-post-template-id />
		<?php echo $this->fd->html('form.action', 'posts.save'); ?>
	<?php } ?>
</form>

<?php if ($isShortCutsEnabled) { ?>
	<?php echo $this->output('site/composer/shortcuts'); ?>
<?php } ?>

<?php
	$tmpPostId = null;
	if (!$templateEditor && $post && $post->isLegacy()) {
		$tmpPostId = $post->id;
	}
?>
<?php echo EB::mediamanager()->render($tmpPostId);?>
