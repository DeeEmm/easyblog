<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="tab-pane <?php echo $active == 'link' ? 'active' : ''; ?>" id="link" data-quickpost-form data-type="link">
	<form class="eb-quick-link form-horizontal">

		<?php if ($this->config->get('main_microblog_blogthis') && !$this->isMobile()) { ?>
		<div class="eb-quick-share-link">
			<p><?php echo JText::_('COM_EB_BLOG_THIS_INFO');?></p>

			<a href="javascript:window.location='<?php echo EBR::getRoutedURL('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=link&link=', true, true);?>' + window.location"
				class="btn btn-default btn-sm"
				data-bookmark-button
			><i class="fdi fa fa-share-alt"></i>&nbsp; <?php echo JText::_('COM_EB_BLOG_THIS');?></a>
		</div>
		<?php } ?>

		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
					<input type="text" name="link" class="form-control"
						value="<?php echo ($active == 'link') && $link ? $this->fd->html('str.escape', $link) : '';?>"
						placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_URL_PLACEHOLDER', true);?>" data-quickpost-link/>
					<span class="input-group-btn">
						<button class="btn btn-default" data-quickpost-crawl-link>
							<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_ADD_LINK'); ?>
							<i class="eb-loader-o size-sm hidden" style="margin: 0 0 0 10px" data-quickpost-crawl-loader></i>
						</button>
					</span>
				</div>
			</div>
		</div>

		<div class="hide" data-quickpost-link-preview>
			<div class="form-group">
				<div class="col-md-12">
					<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_TITLE_PLACEHOLDER', true);?>" data-quickpost-title/>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-12">
					<textarea class="form-control" rows="5" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_CAPTION_PLACEHOLDER', true);?>" data-quickpost-content></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-12">

					<div id="dropzone" class="<?php echo !$this->isMobile() && !$this->isTablet() ? 'eb-quick-photo-uploader' : '';?> input-drop <?php echo $active == 'link' && $link ? 'hidden' : '';?>"
						data-link-upload-container
						data-plupload
						data-plupload-url="<?php echo rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&task=media.upload&key=' . $place->key . '&tmpl=component&' . EB::token() . '=1';?>"
						data-plupload-max-file-size="<?php echo '10mb';?>"
						data-plupload-file-data-name="file"
					>

						<?php if (!$this->isMobile() && !$this->isTablet()) { ?>
						<span class="eb-plupload-btn">
							<div id="input-drop-container" data-plupload-browse-button data-plupload-drop-element>
								<i class="fdi far fa-image"></i>
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

					<div class="eb-quick-photo-uploader-preview upload-preview" data-link-image-preview>
					</div>

					<div class="eb-quick-photo-uploader-loading hidden" data-link-image-loader>
						<span>
							<div id="input-drop-container">
								<i class="fdi fa fa-sync fa-spin"></i>
								<div><?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOADING_IMAGE');?></div>
							</div>
						</span>
					</div>

					<br />

					<div class="form-group">
						<div class="col-md-6">
							<a href="javascript:void(0);" class="eb-quick-photo-uploader-reupload btn btn-default btn-block" data-link-photo-upload>
								<i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_IMAGE');?>
							</a>
						</div>

						<div class="col-md-6">
							<a href="javascript:void(0);" class="eb-quick-photo-uploader-reupload btn btn-default btn-block hidden" data-link-photo-original>
								<i class="fdi fa fa-cloud"></i>&nbsp; <?php echo JText::_('COM_EB_USE_ORIGINAL_PICTURE');?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="uri" data-link-photo-uri value="" />
		<input type="hidden" name="external" data-link-photo-external value="" />

		<?php echo $this->output('site/dashboard/quickpost/forms/more'); ?>
	</form>
</div>
