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
<div class="btn-group" data-toolbar-view data-type="cover">
	<button type="button" class="btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-composer-toolbar-cover
		data-title="<?php echo JText::_('COM_EASYBLOG_POST_COVER');?><?php echo $this->html('composer.shortcut', ['shift', 'c']); ?>"
		data-placement="bottom"
		data-eb-provide="tooltip"
		data-html="1"
	>
		<i class="fdi far fa-image fa-fw"></i>
	</button>

	<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--cover" data-cover-container>
		<div class="eb-comp-toolbar-dropdown-menu__hd">
			<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
				<i class="fdi far fa-image fa-fw"></i>
			</div>
			 <?php echo JText::_('COM_EASYBLOG_POST_COVER');?>
			<div class="eb-comp-toolbar-dropdown-menu__hd-action">
				<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>
		</div>
		<div class="eb-comp-toolbar-dropdown-menu__bd">
			<div class="<?php echo !empty($post->image) ? " has-image" : ""; ?>"
				data-cover-placeholder
				data-eb-composer-art
				data-id="cover"
				data-key="_cG9zdA--"
				data-type="imagevideo"
				data-plupload-multi-selection="0"
			>
				<div class="eb-comp-cover-area-wrapper">
					<div class="eb-comp-cover-area-wrapper__msg o-aspect-ratio t-bg--200 <?php echo $coverUrl || !empty($post->image) ? " t-hidden" : ""; ?>" style="--aspect-ratio: 548/240;" data-cover-message-area>
						<div class="t-d--flex t-flex-direction--c t-w--100 t-justify-content--c" data-plupload-drop-element>
							<div class="">
								<?php echo JText::_('COM_EASYBLOG_POST_COVER_DROP_IMAGE_TO_UPLOAD');?>
							</div>
							<div class="text-danger t-hidden" data-cover-error>
								<i class="fdi fa fa-exclamation-circle" data-eb-mm-failed-message></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD_FAILED');?>
							</div>

							<div class="text-danger t-hidden" data-cover-error-embed>
								<i class="fdi fa fa-exclamation-circle" data-eb-mm-failed-message></i>&nbsp; <?php echo JText::_('COM_EB_COMPOSER_BLOCK_EMBED_ERROR');?>
							</div>
						</div>
					</div>

					<div class="eb-comp-cover-area" data-cover-workarea data-plupload-drop-element>

						<div class="o-progress-radial t-hidden" data-eb-mm-upload-progress-bar>
							<div class="o-progress-radial__overlay" data-eb-mm-upload-progress-value></div>
						</div>

						<div class="o-aspect-ratio" style="--aspect-ratio: 548/240;"  data-cover-preview data-main>
							<?php if ($coverUrl && $isImage) { ?>
								<div class="eb-comp-cover-area__embed-item" data-cover-preview-image style="<?php echo $coverUrl ? 'background-image: url(\'' . $coverUrl . '\');' : '';?>"></div>
							<?php } elseif ($post->image) {  ?>
								<?php if ($post->isEmbedCover()) { ?>
									<?php echo $post->getEmbedCover(); ?>
								<?php } else { ?>
									<div class="eb-comp-cover-area__embed-item" data-cover-preview-video>
										<?php echo EB::media()->renderVideoPlayer($post->getImage(), ['width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false, 'isCover' => true], false); ?>
									</div>
								<?php } ?>
							<?php } else { ?>
								<div class="eb-comp-cover-area__embed-item" data-cover-preview-empty>

								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="eb-comp-cover-area__cover-action">
					<div class="">
						<div class="t-hidden" data-cover-url-form>
							<div class="o-input-group o-input-group--l">
								<input type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_EB_POST_COVER_PLACE_URL_PLACEHOLDER'); ?>" value="<?php echo !empty($post->image) && $post->isEmbedCover() ? $post->getImage() : ""; ?>" data-cover-url-textbox="">

								<span class="o-input-group__btn">
									<button type="button" class="btn btn--sm btn-eb-default-o t-text--primary" data-cover-url-add="">
										<?php echo $this->fd->html('icon.font', 'fdi fa fa-check', 'fdi fas fa-circle-notch'); ?>
									</button>
									<button type="button" class="btn btn--sm btn-eb-default-o" data-cover-url-cancel="">
										<i class="fdi fa fa-times"></i>
									</button>
								</span>
							</div>

							<div class="help-block"><?php echo JText::_('COM_EB_POST_COVER_SUPPORTED_PROVIDERS'); ?></div>
						</div>

						<div class="o-grid-sm" data-cover-buttons>

							<div class="o-grid-sm__cell t-text--right">
								<button type="button" class="btn btn-eb-default-o btn--sm" data-cover-url>
									<?php echo JText::_('COM_EB_COVER_ENTER_URL'); ?>
								</button>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
								<div class="t-lg-ml--sm t-lg-mr--sm t-lg-mt--sm">&nbsp;</div>
							</div>
							<div class="o-grid-sm__cell t-text--right">
								<a href="javascript:void(0);" class="btn btn-eb-default-o btn--sm btn-browse"
									data-cover-browse
									data-eb-mm-browse-button
									data-eb-mm-start-uri="_cG9zdA--"
									data-eb-mm-filter="imagevideo"
									data-eb-mm-browse-place="local"
									data-eb-mm-browse-type="cover"
								>
									<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
								</a>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
								<div class="t-lg-ml--sm t-lg-mr--sm t-lg-mt--sm">&nbsp;</div>
							</div>
							<div class="o-grid-sm__cell t-text--left">
								<button class="btn btn--sm btn-eb-primary"
									data-cover-upload
									data-plupload-browse-button
									data-eb-composer-blogimage-browse-button
								><i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_UPLOAD_BUTTON');?></button>
							</div>
						</div>

						<div class="eb-comp-cover-area__remove t-text--danger <?php echo empty($post->image) && !$coverUrl ? " t-hidden" : ""; ?>" data-cover-remove>
							<?php echo JText::_('COM_EB_REMOVE_POST_COVER');?>
						</div>
					</div>
				</div>

				<div class="eb-comp-cover-field">
					<div class="eb-composer-fieldset eb-composer-fieldset eb-composer-fieldset--accordion is-open">
						<div class="eb-composer-fieldset-header">
							<b><?php echo JText::_('COM_EB_POST_COVER_IMAGE_TITLE');?></b>
						</div>

						<div class="eb-composer-fieldset-content o-form-horizontal">
							<input type="text" name="params[image_cover_title]" value="<?php echo $post->getParams()->get('image_cover_title'); ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_POST_COVER_TITLE_PLACEHOLDER', true);?>"
								class="o-form-control" data-cover-title />
						</div>
					</div>

					<div class="eb-composer-fieldset eb-composer-fieldset eb-composer-fieldset--accordion is-open">
						<div class="eb-composer-fieldset-header">
							<b><?php echo JText::_('COM_EB_POST_COVER_IMAGE_CAPTION');?></b>
						</div>

						<div class="eb-composer-fieldset-content o-form-horizontal">
							<input type="text" name="params[image_cover_caption]" value="<?php echo $post->getParams()->get('image_cover_caption'); ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_POST_COVER_CAPTION_PLACEHOLDER', true);?>"
									class="o-form-control" data-cover-caption />
						</div>
					</div>
				</div>

				<input type="hidden" name="image" value="<?php echo $post->image;?>" data-cover-value />
				<input type="hidden" name="params[image_cover_alt]" value="<?php echo $post->getParams()->get('image_cover_alt'); ?>" data-cover-alt />
			</div>
		</div>
	</div>
</div>
