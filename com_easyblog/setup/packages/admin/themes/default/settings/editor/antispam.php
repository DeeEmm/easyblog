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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_ANTI_SPAM', 'COM_EASYBLOG_SETTINGS_ANTI_SPAM_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_post_min', 'COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'main_post_min_by', 'COM_EB_SETTINGS_MIN_CONTENT_BY', [
							'characters' => 'COM_EASYBLOG_CHARACTERS',
							'words' => 'COM_EASYBLOG_WORDS'
						]); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH', 'main_post_length'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group<?php echo $this->config->get('main_post_min_by') == 'characters' ? '' : ' hide'; ?>" data-content-type="characters">
									<input type="text" name="main_post_length" id="main_post_length" value="<?php echo $this->escape($this->config->get('main_post_length'));?>" class="form-control text-center" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_CHARACTERS');?></span>
								</div>
								<div class="input-group<?php echo $this->config->get('main_post_min_by') == 'words' ? '' : ' hide'; ?>" data-content-type="words">
									<input type="text" name="main_post_length_words" id="main_post_length_words" value="<?php echo $this->escape($this->config->get('main_post_length_words'));?>" class="form-control text-center" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_WORDS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_CONTENT_FILTERING', 'COM_EASYBLOG_SETTINGS_CONTENT_FILTERING_INFO'); ?>

			<div class="panel-body">
				<p><?php echo JText::_('COM_EASYBLOG_SETTINGS_BLOCKED_WORDS_INFO'); ?></p>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_BLOCKED_WORDS', 'main_blocked_words'); ?>

					<div class="col-md-7">
						<textarea class="form-control" name="main_blocked_words" id="main_blocked_words"><?php echo $this->config->get('main_blocked_words'); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
