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
<div class="eb-nmm-popup t-hidden <?php echo $isMobile ? '' : ' is-places-open';?>" data-mm-frame
	data-mm-uploader-url="<?php echo $uploadUrl; ?>"
	data-mm-uploader-max-file-size="<?php echo $this->config->get('main_upload_image_size'); ?>mb"
	data-mm-uploader-extensions="<?php echo $this->config->get('main_media_extensions'); ?>"
	data-uri="user:<?php echo $this->my->id;?>"
	data-acl-upload="<?php echo $this->acl->get('upload_image') ? '1' : '0';?>"
	data-mobile="<?php echo $isMobile ? '1' : '0';?>"
	data-requirements-width="<?php echo JText::_('COM_EB_MM_MINIMUM_WIDTH');?>"
	data-requirements-height="<?php echo JText::_('COM_EB_MM_MINIMUM_HEIGHT');?>"
>
	<?php if ($this->config->get('main_media_editing')) { ?>
	<div class="eb-img-editor" data-mm-editor>

		<div class="eb-img-editor-header t-p--sm t-align-items--c">
			<div class="eb-img-editor-header__main-action">
				<div class="t-d--flex t-flex-shrink--0">
					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-mm-editor-close>
						<?php echo JText::_('COM_EB_CANCEL');?>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn eb-img-editor-btn-undo t-ml--lg"
						data-title="<?php echo JText::_('COM_EB_UNDO');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-undo
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M12.596 6.904a6.5 6.5 0 00-9.192 0L2.257 8.05V5.257a.5.5 0 10-1 0v4c0 .017.001.033.003.05 0 .007.002.013.003.02l.004.028.006.023.006.025.008.022.009.024c.002.007.006.013.009.02l.012.024.01.018.015.024.013.018.016.022.023.025.01.011.01.01.026.023.02.015.02.014.022.014.02.011.022.011.022.01.022.008.024.009.021.005.027.007.023.003.025.004a.509.509 0 00.038.002h4.011a.5.5 0 100-1H2.965L4.11 7.611A5.5 5.5 0 0113.5 11.5a.5.5 0 101 0 6.458 6.458 0 00-1.904-4.596z" fill="#374151" fill-rule="nonzero"/></svg>
					</a>
				</div>

				<div class="t-d--flex t-flex-grow--1 t-justify-content--se t-px--md sm:t-d--none t-mx--auto" style="max-width: 400px">
					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_ROTATE_LEFT');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-rotate
						data-degrees="-90"
					>

						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M12.243 3.757a6.007 6.007 0 00-8.486 0L2.49 5.025V3.232a.5.5 0 00-1 0v3c0 .017 0 .033.002.05l.003.02.004.028.006.023.006.025.008.022.009.024.01.02.01.024.012.019.014.023.014.02.015.02.03.032.003.004.003.003.034.03c.005.005.012.009.018.013l.02.016.023.013.02.012.022.01.022.011.022.008.025.009.021.005.026.007.025.003.023.004.044.002H4.99a.5.5 0 000-1H3.197l1.267-1.268a5 5 0 110 7.072.5.5 0 00-.707.707 6 6 0 108.486-8.486z" fill="#374151" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_ROTATE_RIGHT');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-rotate
						data-degrees="90"
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M14.083 6.726c.009 0 .017-.002.025-.003l.026-.007.021-.005.025-.009.022-.008.022-.01.022-.01.02-.013.022-.013.021-.016c.006-.004.013-.008.018-.013a.503.503 0 00.034-.03l.003-.003.004-.004c.01-.01.02-.021.029-.033l.015-.02.014-.019.014-.023.011-.019.012-.024.01-.02.008-.024.008-.022.006-.025.006-.023.004-.028.003-.02a.498.498 0 00.002-.05v-3a.5.5 0 10-1 0v1.793l-1.267-1.268a5.997 5.997 0 100 8.486.5.5 0 10-.707-.707 5 5 0 110-7.072l1.267 1.268H11.01a.5.5 0 100 1h3.006c.015 0 .03 0 .044-.002l.023-.004z" fill="#374151" fill-rule="nonzero"/></svg>

					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_CROP');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-crop
					>
						<svg width="16" height="16" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M4 1a.5.5 0 01.5.5v10h10a.5.5 0 010 1h-2v2a.5.5 0 11-1 0v-2H4a.5.5 0 01-.5-.5V4.5h-2a.5.5 0 010-1h2v-2A.5.5 0 014 1zm8 2.5a.5.5 0 01.5.5v6a.5.5 0 11-1 0V4.5H6a.5.5 0 010-1z" fill="#9CA3AF" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_FLIP_HORIZONTALLY');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-flip-horizontal
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M8 10.25a.25.25 0 01.177.073l2.5 2.5a.25.25 0 01-.177.427h-5a.25.25 0 01-.177-.427l2.5-2.5A.25.25 0 018 10.25zm5.5-2.75a.5.5 0 110 1h-11a.5.5 0 010-1h11zm-3-4.75a.25.25 0 01.177.427l-2.5 2.5a.25.25 0 01-.354 0l-2.5-2.5A.25.25 0 015.5 2.75z" fill="#9CA3AF" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_FLIP_VERTICALLY');?>"
						data-html="1"
						data-placement="bottom"
						data-eb-provide="tooltip"
						data-mm-editor-flip-vertical
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path id="svg_1" fill-rule="nonzero" fill="#9CA3AF" d="m7.95,1.81092a0.75,0.75 0 0 1 0.743,0.648l0.007,0.102l0,10a0.75,0.75 0 0 1 -1.493,0.102l-0.007,-0.102l0,-10a0.75,0.75 0 0 1 0.75,-0.75zm-7.523,2.823l2.5,2.5a0.25,0.25 0 0 1 0,0.354l-2.5,2.5a0.25,0.25 0 0 1 -0.427,-0.177l0,-5a0.25,0.25 0 0 1 0.427,-0.177zm15.146,0a0.25,0.25 0 0 1 0.42,0.118l0.007,0.059l0,5a0.25,0.25 0 0 1 -0.38,0.214l-0.047,-0.037l-2.5,-2.5a0.25,0.25 0 0 1 -0.04,-0.302l0.04,-0.052l2.5,-2.5z"/></svg>
					</a>
				</div>

				<div class="t-pr--sm t-pr--sm sm:t-ml--auto">
					<a href="javascript:void(0);" class="btn btn-eb-primary t--invisible" data-mm-editor-save>
						<?php echo JText::_('COM_EB_SAVE');?>
					</a>
				</div>
			</div>
			<div class="eb-img-editor-header__crop-action">
				<div class="t-d--flex t-flex-shrink--0">
				</div>

				<div class="t-d--flex t-flex-grow--1 t-justify-content--c t-align-items--c t-px--md">
					<div class="t-mr--md">
						<?php echo JText::_('COM_EB_CROP_IMAGE_TITLE');?>
					</div>

					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-mm-editor-crop-cancel>
						<?php echo JText::_('COM_EB_CANCEL');?>
					</a>

					<a href="javascript:void(0);" class="btn btn-eb-primary t-ml--lg" data-mm-editor-crop-save>
						<?php echo JText::_('COM_EB_CROP');?>
					</a>
				</div>

				<div class="t-pr--sm">
				</div>
			</div>
		</div>

		<div class="eb-img-editor-content" data-mm-editor-content>

			<div class="eb-img-editor-content__alert t-d--none" data-mm-editor-alert>
				<div class="o-alert o-alert--success o-alert--dismissible t-mb--no" data-mm-editor-alert-wrapper>
					<span data-message></span>
				</div>
			</div>

			<div class="eb-img-editor-content__cropper">
				<div class="eb-image-editor-canvas" data-mm-editor-image>
				</div>

				<div class="eb-img-editor-content__info">
					<div class="t-mt--md t-p--2xs t-bg--400 t-rounded--md t-text--truncate" data-mm-editor-title></div>
				</div>
			</div>
			<div class="o-loader o-loader--inline"></div>
		</div>
		<div class="eb-img-editor-mobile-bar">
			<div class="eb-img-editor-mobile-bar__main-action">
				<div class="t-d--flex t-flex-grow--1 t-justify-content--se">
					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_ROTATE_LEFT');?>"
						data-html="1"
						data-mm-editor-rotate
						data-degrees="-90"
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M12.243 3.757a6.007 6.007 0 00-8.486 0L2.49 5.025V3.232a.5.5 0 00-1 0v3c0 .017 0 .033.002.05l.003.02.004.028.006.023.006.025.008.022.009.024.01.02.01.024.012.019.014.023.014.02.015.02.03.032.003.004.003.003.034.03c.005.005.012.009.018.013l.02.016.023.013.02.012.022.01.022.011.022.008.025.009.021.005.026.007.025.003.023.004.044.002H4.99a.5.5 0 000-1H3.197l1.267-1.268a5 5 0 110 7.072.5.5 0 00-.707.707 6 6 0 108.486-8.486z" fill="#374151" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_ROTATE_RIGHT');?>"
						data-html="1"
						data-mm-editor-rotate
						data-degrees="90"
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M14.083 6.726c.009 0 .017-.002.025-.003l.026-.007.021-.005.025-.009.022-.008.022-.01.022-.01.02-.013.022-.013.021-.016c.006-.004.013-.008.018-.013a.503.503 0 00.034-.03l.003-.003.004-.004c.01-.01.02-.021.029-.033l.015-.02.014-.019.014-.023.011-.019.012-.024.01-.02.008-.024.008-.022.006-.025.006-.023.004-.028.003-.02a.498.498 0 00.002-.05v-3a.5.5 0 10-1 0v1.793l-1.267-1.268a5.997 5.997 0 100 8.486.5.5 0 10-.707-.707 5 5 0 110-7.072l1.267 1.268H11.01a.5.5 0 100 1h3.006c.015 0 .03 0 .044-.002l.023-.004z" fill="#374151" fill-rule="nonzero"/></svg>

					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_CROP');?>"
						data-html="1"
						data-mm-editor-crop
					>
						<svg width="16" height="16" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M4 1a.5.5 0 01.5.5v10h10a.5.5 0 010 1h-2v2a.5.5 0 11-1 0v-2H4a.5.5 0 01-.5-.5V4.5h-2a.5.5 0 010-1h2v-2A.5.5 0 014 1zm8 2.5a.5.5 0 01.5.5v6a.5.5 0 11-1 0V4.5H6a.5.5 0 010-1z" fill="#9CA3AF" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_FLIP_HORIZONTALLY');?>"
						data-html="1"
						data-mm-editor-flip-horizontal
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M8 10.25a.25.25 0 01.177.073l2.5 2.5a.25.25 0 01-.177.427h-5a.25.25 0 01-.177-.427l2.5-2.5A.25.25 0 018 10.25zm5.5-2.75a.5.5 0 110 1h-11a.5.5 0 010-1h11zm-3-4.75a.25.25 0 01.177.427l-2.5 2.5a.25.25 0 01-.354 0l-2.5-2.5A.25.25 0 015.5 2.75z" fill="#9CA3AF" fill-rule="nonzero"/></svg>
					</a>

					<a href="javascript:void(0);" class="btn eb-img-editor-btn"
						data-title="<?php echo JText::_('COM_EB_FLIP_VERTICALLY');?>"
						data-html="1"
						data-mm-editor-flip-vertical
					>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path id="svg_1" fill-rule="nonzero" fill="#9CA3AF" d="m7.95,1.81092a0.75,0.75 0 0 1 0.743,0.648l0.007,0.102l0,10a0.75,0.75 0 0 1 -1.493,0.102l-0.007,-0.102l0,-10a0.75,0.75 0 0 1 0.75,-0.75zm-7.523,2.823l2.5,2.5a0.25,0.25 0 0 1 0,0.354l-2.5,2.5a0.25,0.25 0 0 1 -0.427,-0.177l0,-5a0.25,0.25 0 0 1 0.427,-0.177zm15.146,0a0.25,0.25 0 0 1 0.42,0.118l0.007,0.059l0,5a0.25,0.25 0 0 1 -0.38,0.214l-0.047,-0.037l-2.5,-2.5a0.25,0.25 0 0 1 -0.04,-0.302l0.04,-0.052l2.5,-2.5z"/></svg>
					</a>
				</div>
			</div>
			<div class="eb-img-editor-mobile-bar__crop-action">
				<div class="t-d--flex t-flex-shrink--0">
				</div>

				<div class="t-d--flex t-flex-grow--1 t-justify-content--c t-align-items--c t-px--md">
					<div class="t-mr--md">
						<?php echo JText::_('COM_EB_CROP_IMAGE_TITLE');?>
					</div>

					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-mm-editor-crop-cancel>
						<?php echo JText::_('COM_EB_CANCEL');?>
					</a>

					<a href="javascript:void(0);" class="btn btn-eb-primary t-ml--lg" data-mm-editor-crop-save>
						<?php echo JText::_('COM_EB_CROP');?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="eb-nmm" data-plupload-drop-element>
		<div class="eb-nmm-places t-user-select--none" data-scrolly="y" data-mm-disabledrag>
			<div class="eb-nmm-places__actionbar">
				<div class="eb-nmm-places__title">
					<b><?php echo JText::_('COM_EASYBLOG_MM_PLACES');?></b>
				</div>

				<a href="javascript:void(0);" class="eb-nmm-places__close" data-mm-mobile-close-places>
					<i class="fdi fa fa-chevron-left"></i>
				</a>
			</div>

			<div class="eb-nmm-places-groups">
				<div class="eb-nmm-place__item is-active">
					<div class="eb-nmm-places-list">
						<?php foreach ($places as $place) { ?>
						<div class="eb-nmm-places-list__item" data-mm-place data-id="<?php echo $place->id;?>" data-key="<?php echo $place->key;?>">
							<a href="javascript:void(0);" class="eb-nmm-places-list__link">
								<i class="eb-nmm-places-list__icon <?php echo $place->icon;?>"></i>&nbsp; <?php echo $place->title;?>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="eb-nmm-main">
			<div class="eb-nmm-mobile-header" data-mm-disabledrag>
				<div class="eb-nmm-mobile-header__toggle">
					<a href="javascript:void(0);" data-mm-mobile-open-places class="t-text--500 t-pr--md">
						<i class="fdi fa fa-bars"></i>
					</a>
				</div>

				<div class="eb-nmm-main-header__title">
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA_MANAGER');?>
				</div>
				<a href="javascript:void(0);" class="eb-nmm-main-header__close" data-mm-close>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>

			<div class="eb-nmm-mobile-actionbar" data-mm-disabledrag>
				<div class="eb-nmm-mobile-actionbar__cell eb-nmm-mobile-actionbar__cell--places">
					<div class="eb-nmm-breadcrumb" data-mm-breadcrumbs>
						<div class="eb-nmm-breadcrumb__item t-hidden" data-mm-breadcrumb-back>
							<a class="eb-nmm-breadcrumb__link" href="javascript:void(0);">↰</a>
						</div>
					</div>
				</div>
				<div class="eb-nmm-mobile-actionbar__cell eb-nmm-mobile-actionbar__cell--action">
					<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-nmm-delete" data-mm-delete>
						<i class="fdi far fa-trash-alt"></i>
					</a>

					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-new-folder" data-mm-create-folder>
						<i class="fdi fa fa-plus"></i>
					</a>

					<div class="t-lg-ml--md" data-mm-upload>
						<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-upload" data-plupload-browse-button>
							<i class="fdi fa fa-upload"></i>
						</a>
					</div>
				</div>

			</div>

			<div class="eb-nmm-actionbar" data-mm-disabledrag>

				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--breadcrumb">
					<div class="eb-nmm-breadcrumb" data-mm-breadcrumbs>
						<div class="eb-nmm-breadcrumb__item t-hidden" data-mm-breadcrumb-back>
							<a class="eb-nmm-breadcrumb__link" href="javascript:void(0);">↰</a>
						</div>
					</div>
				</div>

				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--action">
					<div class="eb-nmm-sub-action">
						<a href="javascript:void(0);" data-mm-layout data-type="list">
							<i class="fdi fa fa-th-list"></i>
						</a>
						<a href="javascript:void(0);" class="is-active" data-mm-layout data-type="grid">
							<i class="fdi fa fa-th-large"></i>
						</a>
					</div>
				</div>

				<?php if ($this->acl->get('upload_image')) { ?>
				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--upload">
					<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-nmm-delete" data-mm-delete>
						<i class="fdi far fa-trash-alt"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON');?>
					</a>

					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-new-folder" data-mm-create-folder>
						<i class="fdi fa fa-plus"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_CREATE_FOLDER');?>
					</a>
				</div>

				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--upload" data-mm-upload>
					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-upload" data-plupload-browse-button>
						<i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD');?>
					</a>
				</div>
				<?php } ?>
				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--upload t-pl--lg t-pr--md">
					<a href="javascript:void(0);" class="eb-nmm-main-header__close" data-mm-close>
						<i class="fdi fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="eb-nmm-main-body-wrapper">
				<div class="eb-nmm-main-body" data-dragzone-wrapper>
					<div class="o-loader"></div>

					<div class="eb-nmm-drag-zone t-hidden" data-dragzone>
						<div class="eb-nmm-drag-zone__title">
							<i class="fdi fa fa-upload"></i>
							<div><?php echo JText::_('COM_EASYBLOG_MM_DROP_FILES_HERE');?></div>
						</div>
					</div>



					<div class="eb-nmm-content" data-mm-body>
						<div class="o-loader"></div>

						<div class="o-empty o-empty__flickr eb-nmm-content__empty">
							<div class="o-empty__content">
								<i class="o-empty__icon fa fa-flickr"></i>
								<div class="o-empty__text">
									<?php echo JText::_('COM_EASYBLOG_MM_FLICKR_NO_IMAGES');?>
								</div>
							</div>
						</div>

						<div class="o-empty o-empty__standard eb-nmm-content__empty">
							<div class="o-empty__content">
								<i class="fdi fa fa-images o-empty__icon "></i>
								<div class="o-empty__text">
									<?php echo JText::_('COM_EASYBLOG_MM_NO_FILES_OR_FOLDERS');?>
										<?php if ($this->acl->get('upload_image')) { ?>
										<br />
										<?php echo JText::_('COM_EASYBLOG_MM_DROP_FILES_HERE_TO_UPLOAD');?>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="eb-nmm-group-listing <?php echo $isMobile ? 'is-list' : ' is-grid';?>" data-mm-listing></div>

						<div class="eb-nmm-screens" data-mm-screens></div>
					</div>

					<div class="eb-nmm-main-footer" data-mm-footer data-mm-disabledrag>
						<div class="eb-nmm-main-footer__selection">
							<div class="eb-nmm-media-selection">
								<div class="eb-nmm-media-selection__info">
									<span class="eb-nmm-media-selection__counter" data-mm-selection-counter></span> <?php echo JText::_('COM_EASYBLOG_MM_SELECTED');?> &mdash; <a href="javascript:void(0);" data-mm-clear-selections><?php echo JText::_('COM_EASYBLOG_MM_CLEAR_SELECTIONS');?></a>
								</div>

								<div class="eb-nmm-media-selection__thumbs" data-mm-selection-list>
								</div>
							</div>
						</div>
						<div class="eb-nmm-main-footer__action" data-mm-actions>
							<span class="t-text--danger t-hidden" data-mm-error-message></span>

							<a href="javascript:void(0);" class="btn btn-default eb-nmm-main-footer__action-btn btn-nmm-gallery" data-mm-insert-gallery>
								<?php echo JText::_('COM_EASYBLOG_MM_INSERT_AS_GALLERY');?>
							</a>
							<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-permalink" data-mm-insert-permalink>
								<?php echo JText::_('COM_EB_MM_INSERT_AS_PERMALINK');?>
							</a>
							<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-insert" data-mm-insert>
								<?php echo JText::_('COM_EASYBLOG_MM_INSERT');?>
							</a>
							<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-select" data-mm-insert>
								<?php echo JText::_('COM_EASYBLOG_MM_SELECT');?>
							</a>
						</div>
					</div>
				</div>

				<div class="eb-nmm-info-panel t-user-select--none" data-mm-info-panel-wrapper data-mm-disabledrag>
					<div data-mm-info-panel-loading>
						<div class="o-loader"></div>
					</div>

					<div class="eb-nmm-info-panel__mobile-bar">
						<div class="eb-nmm-info-panel__close">
							<a href="javascript:void(0);" data-mm-mobile-panel-hide>
								<i class="fdi fa fa-chevron-right"></i>
							</a>
						</div>
						<div class="eb-nmm-info-panel__mobile-filename">
							<div data-mm-mobile-panel-title></div>
						</div>
					</div>
					<div class="eb-nmm-info-panel__hd">
						<div class="eb-nmm-info-panel__alert t-hidden" data-mm-info-state>
							<div class="o-alert o-alert--success o-alert--dismissible">
								<div class="t-d--flex">
									<div class="t-pr--md">
										<i class="fdi fas fa-check-circle"></i>
									</div>
									<div class="t-flex-grow--1 l-stack l-spaces--xs">
										<div class="t-d--flex t-align-items--c">
											<div class="t-flex-grow--1">
												<b><?php echo JText::_('COM_EASYBLOG_MM_CHANGES_SAVED');?></b>
											</div>
											<div class="t-flex-shrink--0 t-pl--md">
												<button type="button" class="o-alert__close" data-bp-dismiss="alert">×</button>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="eb-nmm-info-panel__bd" data-mm-info-panel data-uri></div>
				</div>
			</div>
		</div>
	</div>

	<div class="t-hidden" data-mm-selection-template>
		<div class="eb-nmm-media type-image ext-png" data-selection-wrapper>
			<div class="eb-nmm-media__cover">
				<div class="eb-nmm-media__embed">
					<div class="eb-nmm-media__embed-item" data-selection-cover></div>
				</div>
			</div>
		</div>
	</div>

	<div class="t-hidden" data-eb-mm-upload-template>
		<div class="eb-nmm-content-listing__item" data-eb--mm-file data-id="">
			<div class="eb-nmm-media" data-eb-mm-upload-type>
				<div class="eb-nmm-media__icon-wrapper">
					<i class=""></i>
				</div>

				<div class="eb-nmm-media__checkbox-wrap"></div>
				<div class="eb-nmm-media__body">
					<div class="eb-nmm-media__cover">
						<div class="eb-nmm-media__embed">
							<div class="eb-nmm-media__embed-item" data-eb-mm-upload-thumbnail></div>
						</div>

						<div class="eb-nmm-media__progress o-progress-radial" data-eb-mm-upload-progress-bar>
							<div class="eb-nmm-media__progress-overlay o-progress-radial__overlay" data-eb-mm-upload-progress-value></div>
						</div>
					</div>
				</div>

				<div class="eb-nmm-media__info">
					<div class="eb-nmm-media__failed-txt text-danger">
						<i class="fdi fa fa-exclamation-circle" data-eb-mm-failed-message></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD_FAILED');?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

