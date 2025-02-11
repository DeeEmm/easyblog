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
<dialog>
	<width>600</width>
	<height>360</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{insertButton}" : "[data-insert-button]",
		"{videoUrl}": "[data-embed-video-url]",
		"{videoWidth}": "[data-embed-video-width]",
		"{videoHeight}": "[data-embed-video-height]",
		"{noCookie}": "[data-embed-video-nocookie]",
		"{relatedSource}": "[data-embed-video-related]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{videoUrl} keyup": function(input, opts) {
			var event = opts[0];

			if (event.keyCode === 13) {
				this.insertButton().click();
				return;
			}
		},

		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_DIALOG_MM_EMBED_VIDEO'); ?></title>
	<content>
		<div>
			<div class="eb-embed-video-note mb-20">
				<?php echo JText::_('COM_EASYBLOG_MM_VIDEO_EMBED_DESC');?>
			</div>

			<div class="eb-embed-video-form">
				<div class="form-group row-table">
					<label class="col-cell text-right" style="width: 120px;">
						<?php echo JText::_('COM_EASYBLOG_MM_VIDEO_URL');?>
					</label>
					<div class="col-cell pl-20">
						<input type="text" class="form-control" data-embed-video-url />
					</div>
				</div>

				<div class="form-group row-table">
					<label class="col-cell text-right" style="width: 120px;">
						<?php echo JText::_('COM_EASYBLOG_MM_VIDEO_WIDTH');?>
					</label>
					<div class="col-cell pl-20" style="width: 100px">
						<input type="text" class="form-control text-center" value="<?php echo $this->config->get('max_video_width');?>" data-embed-video-width />
					</div>
					<div class="col-cell pl-15"><?php echo JText::_('COM_EASYBLOG_PIXELS');?></div>
				</div>

				<div class="form-group row-table">
					<label class="col-cell text-right" style="width: 120px;">
						<?php echo JText::_('COM_EASYBLOG_MM_VIDEO_HEIGHT');?>
					</label>
					<div class="col-cell pl-20" style="width: 100px">
						<input type="text" class="form-control text-center" value="<?php echo $this->config->get('max_video_height');?>" data-embed-video-height />
					</div>
					<div class="col-cell pl-15"><?php echo JText::_('COM_EASYBLOG_PIXELS');?></div>
				</div>
				<hr class="divider mb-0">
				<div class="pt-10 pb-10 t-text--center"><b><?php echo JText::_('COM_EB_MM_YOUTUBE_VIDEO_OPTIONS'); ?></b></div>

				<div class="form-group row-table">
					<label class="col-cell text-right" style="width: 120px;">
						<?php echo JText::_('COM_EB_MM_ENHANCE_PRIVACY');?>
					</label>
					<div class="col-cell pl-20 pt-5" style="width: 80px">
						 <?php echo $this->fd->html('form.toggler', 'youtube_nocookie', $this->config->get('main_youtube_nocookie'), 'youtube_nocookie', 'data-embed-video-nocookie'); ?>
					</div>
					<div class="col-cell"><?php echo JText::_('COM_EB_MM_ENHANCE_PRIVACY_NOTE');?></div>
				</div>

				<div class="form-group row-table">
					<label class="col-cell text-right" style="width: 120px;">
						<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED');?>
					</label>
					<div class="col-cell pl-20 pt-5">
						<?php echo $this->fd->html('form.dropdown', 'youtube_related', '', [
							'any' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_ANY_CHANNEL',
							'same' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_SAME_CHANNEL'
						], ['attr' => 'data-embed-video-related']); ?>
					</div>
				</div>
			</div>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton', 'COM_EASYBLOG_CLOSE_BUTTON'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_EMBED_BUTTON', 'primary', [
			'attributes' => 'data-insert-button'
		]); ?>
	</buttons>
</dialog>
