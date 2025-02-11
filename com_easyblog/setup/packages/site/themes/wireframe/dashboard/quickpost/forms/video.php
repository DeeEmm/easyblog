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
<div class="tab-pane <?php echo $active=='video' ? 'active' : ''; ?>" id="video" data-quickpost-form data-type="video">
	<form class="eb-quick-video form-horizontal">
		<div class="form-group">
			<div class="col-md-12">
				<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_VIDEO_TITLE_PLACEHOLDER', true);?>" data-quickpost-video-title>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
					<input name="video" class="form-control" type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_VIDEO_PLACEHOLDER', true);?>" data-quickpost-video-source />
					<span class="input-group-btn">
						<button class="btn btn-default" data-quickpost-video-retrieve>
							<i class="fdi fa fa-video-camera"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_INSERT_VIDEO_BUTTON'); ?>
							<i class="eb-loader-o size-sm hidden" style="margin: 0 0 0 10px" data-quickpost-video-loader></i>
						</button>
					</span>
				</div>
			</div>
		</div>

		<div class="form-group hidden" data-youtube-nocookie>
			<div class="col-md-3">
				<label class="o-control-label eb-composer-field-label">
					<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_YOUTUBE_NOCOOKIE'); ?>
				</label>
			</div>
			<div class="col-md-6">
				<?php echo $this->fd->html('form.toggler', 'youtube_nocookie', false, 'youtube_nocookie', 'data-option-nocookie'); ?>
			</div>
		</div>

		<div class="form-group hidden" data-youtube-related>
			<div class="col-md-3">
				<label class="o-control-label eb-composer-field-label">
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED'); ?>
				</label>
			</div>
			<div class="col-md-6">
				<?php echo $this->fd->html('form.dropdown', 'youtube_related', '', [
					'any' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_ANY_CHANNEL',
					'same' => 'COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_SHOW_RELATED_SAME_CHANNEL'
				], ['attr' => 'data-option-related']); ?>
			</div>
		</div>

		<div class="form-group hidden" data-video-preview-wrapper>
			<div class="col-md-12">
				<div class="video-embed-wrapper is-responsive">
					<div class="eb-quick-video-preview" data-quickpost-video-preview>
					</div>
				</div>
			</div>
		</div>

		<?php echo $this->output('site/dashboard/quickpost/forms/more'); ?>
	</form>
</div>
