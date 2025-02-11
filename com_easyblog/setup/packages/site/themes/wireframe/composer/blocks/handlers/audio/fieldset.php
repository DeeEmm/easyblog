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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'autoplay', 'COM_EASYBLOG_BLOCKS_AUDIO_AUTOPLAY', true, ['data-audio-fieldset-autoplay']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'audio_loop', 'COM_EASYBLOG_BLOCKS_AUDIO_REPLAY_AUTOMATICALLY', false, ['data-audio-fieldset-loop']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'artist', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_ARTIST', true, ['data-audio-fieldset-artist']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'track', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_TRACK', true, ['data-audio-fieldset-track']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'download', 'COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_DOWNLOAD_LINK', true, ['data-audio-fieldset-download']); ?>
	</div>
</div>
