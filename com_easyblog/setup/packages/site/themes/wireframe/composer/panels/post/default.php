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
<div class="eb-composer-panel <?php echo $templateEditor ? 'hide' : 'active'; ?>" data-eb-composer-panel data-id="post-options">

	<div class="eb-composer-panel-content">
		<div data-eb-composer-panel-content-viewport data-scrolly-viewport>
			<?php if ($this->config->get('layout_composer_permalink')) { ?>
			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('permalink', true) ? 'is-open' : ''; ?>" data-name="permalink" data-eb-composer-block-section>

				<?php echo $this->html('composer.panel.header', 'COM_EB_PERMALINK'); ?>

				<div class="eb-composer-fieldset-content">
					<div class="o-form-group">
						<div class="eb-comp-permalink <?php echo !$post->permalink ? 'is-empty' : '';?>" data-eb-editor-permalink>
							<div class="eb-comp-permalink__empty">
								<?php echo JText::_('COM_EB_PERMALINK_AUTO_GENERATED'); ?>
							</div>

							<div class="eb-comp-permalink__preview">
								<div class="eb-comp-permalink__sample">
									<a data-permalink-preview href="javascript:void(0);" class="eb-comp-permalink__post-name">
										/<span ><?php echo $post->permalink;?></span>
									</a>

									<div class="eb-comp-permalink__edit-field">
										<input type="text" class="o-form-control" name="permalink" value="<?php echo $this->fd->html('str.escape', $post->permalink);?>" data-permalink-input />
									</div>
								</div>
								<div class="eb-comp-permalink__edit-action">
									<a href="javascript:void(0);" class="btn-permalink--edit" data-permalink-edit><?php echo JText::_('COM_EASYBLOG_EDIT_POST_PERMALINK'); ?></a>

									<a href="javascript:void(0);" class="btn-permalink--cancel" data-permalink-cancel>
										<?php echo JText::_('COM_EASYBLOG_CANCEL'); ?>
									</a>

									<a href="javascript:void(0);" class="btn-permalink--confirm" data-permalink-update>
										<?php echo JText::_('COM_EASYBLOG_SAVE'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if (!$templateEditor) { ?>
				<?php echo $this->output('site/composer/panels/post/author/default'); ?>
			<?php } ?>

			<?php if (!$templateEditor && $post->getType() == 'link') { ?>
				<?php echo $this->output('site/composer/panels/post/quickpost/default'); ?>
			<?php } ?>

			<?php echo $this->output('site/composer/panels/post/general/default'); ?>

			<?php echo $this->output('site/composer/panels/post/category/default'); ?>

			<?php if ($this->config->get('layout_composer_tags')) { ?>
				<?php echo $this->output('site/composer/panels/post/tags/default'); ?>
			<?php } ?>

			<?php echo $this->output('site/composer/panels/post/autopost/default'); ?>

			<?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) && EB::isAssociationEnabled() && $languages) { ?>
				<?php echo $this->output('site/composer/panels/post/association/default'); ?>
			<?php } ?>

			<?php if ($this->config->get('layout_composer_customnotifications') && $this->acl->get('allow_custom_notifications')) { ?>
				<?php echo $this->output('site/composer/panels/post/notifications/default'); ?>
			<?php } ?>
		</div>
	</div>
</div>
