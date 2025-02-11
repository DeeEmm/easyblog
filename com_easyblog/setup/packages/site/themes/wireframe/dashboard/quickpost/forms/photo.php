<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="tab-pane <?php echo $active=='photo' ? 'active' : ''; ?>" id="photo" data-quickpost-form data-type="photo">
	<form class="eb-quick-photo form-horizontal">
		<div class="eb-quick-photo-options">
		    <ul class="eb-quick-photo-tab reset-list" role="tablist">
				<li class="active" data-quickpost-photo-tab-upload data-quickpost-photo-tab data-type="upload">
					<a href="#home" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_UPLOAD_PHOTO');?></a>
				</li>
				<li data-quickpost-photo-tab-webcam data-quickpost-photo-tab data-type="webcam">
					<a href="#profile" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_TAKE_PHOTO');?></a>
				</li>
		    </ul>

			<div class="form-group">
				<div class="col-md-12">
					<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TITLE_REQUIRED');?>" data-quickpost-title />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<textarea class="form-control" rows="10" placeholder="<?php echo $this->fd->html('str.escape', JText::_('COM_EASYBLOG_MICROBLOG_STANDARD_CONTENT_PLACEHOLDER', true));?>" data-quickpost-content></textarea>
				</div>
			</div>

			<div class="eb-quick-photo-tab-content tab-content">

				<div class="tab-pane fade in active" id="home">

					<div id="dropzone" class="<?php echo !$this->isMobile() && !$this->isTablet() ? 'eb-quick-photo-uploader' : '';?> input-drop"
						data-photo-upload-container
						data-plupload
						data-plupload-url="<?php echo rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&task=media.upload&key=' . $place->key . '&tmpl=component&' . EB::token() . '=1';?>"
						data-plupload-max-file-size="<?php echo '10mb';?>"
						data-plupload-file-data-name="file">

						<?php if (!$this->isMobile() && !$this->isTablet()) { ?>
						<span class="eb-plupload-btn">
							<div id="input-drop-container" data-plupload-browse-button data-plupload-drop-element>
								<i class="o-empty__icon far fa-image"></i>
								<div><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_CLICK_TO_UPLOAD');?></div>
							</div>
						</span>
						<?php } ?>

						<?php if ($this->isMobile() || $this->isTablet()) { ?>
						<a href="javascript:void(0);" class="btn btn-default btn-block" data-plupload-browse-button>
							<i class="fdi far fa-image"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_IMAGE');?>
						</a>
						<div data-plupload-drop-element></div>
						<?php } ?>
					</div>

					<div class="eb-quick-photo-uploader-preview upload-preview hidden" data-photo-upload-preview>
					</div>

					<div class="eb-quick-photo-uploader-loading hidden" data-photo-upload-loading>
						<span>
							<div id="input-drop-container">
								<i class="fdi fa fa-sync fa-spin"></i>
								<div><?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOADING_IMAGE');?></div>
							</div>
						</span>
					</div>

					<br>

					<a href="javascript:void(0);" class="hidden eb-quick-photo-uploader-reupload btn btn-default btn-block" data-photo-upload-reupload>
						<i class="fdi fa fa-sync"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_REUPLOAD');?>
					</a>
				</div>

				<div class="tab-pane fade" id="profile">
					<div class="eb-quick-photo-camera">
						<div id="camera" class="eb-responsive-video" data-photo-camera-canvas data-key="<?php echo $webcamKey;?>"></div>

						<div data-photo-camera-preview class="eb-quick-photo-camera-preview hidden"></div>
					</div>

					<br />

					<a href="javascript:void(0);" class="eb-quick-photo-camera-recapture btn btn-default btn-block hidden" data-photo-camera-recapture>
						<i class="fdi fa fa-sync"></i>
						<?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_RECAPTURE');?>
					</a>

					<a href="javascript:void(0);" class="eb-quick-photo-camera-capture btn btn-default btn-block" data-photo-camera-capture>
						<i class="fdi fa fa-camera"></i>
						<?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_CAPTURE');?>
					</a>
				</div>
			</div>
		</div>
		<input type="hidden" name="uri" data-photo-uri value="" />
		<?php echo $this->output('site/dashboard/quickpost/forms/more'); ?>
	</form>
</div>
