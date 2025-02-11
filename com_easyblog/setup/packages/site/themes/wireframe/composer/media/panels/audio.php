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
<div class="eb-nmm-info-panel__viewport">
	<div class="eb-nmm-info-panel__content">
		<form data-mm-form>
			<?php echo $this->html('media.preview', ['icon' => $file->icon]); ?>

			<div class="eb-nmm-panel-block">
				<?php echo $this->html('media.field', 'media.textbox', 'title', 'COM_EASYBLOG_MM_PANEL_TITLE', $file->title, 'data-mm-panel-title data-mm-panel-input'); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'autoplay', 'COM_EASYBLOG_IMAGE_MANAGER_AUTOPLAY', false); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'audio_loop', 'COM_EASYBLOG_BLOCKS_AUDIO_REPLAY_AUTOMATICALLY', false); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'showArtist', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_ARTIST', true); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'showTrack', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_TRACK', true); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'showDownload', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_DOWNLOAD_LINK', true); ?>
			</div>
		</form>
	</div>
</div>
