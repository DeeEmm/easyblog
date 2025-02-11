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

$mediaPositions = [
	'top' => 'COM_EASYBLOG_TOP_OPTION',
	'bottom' => 'COM_EASYBLOG_BOTTOM_OPTION',
	'hidden' => 'COM_EASYBLOG_DO_NOT_SHOW_OPTION'
];
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_CONTENT_INFO', '/administrators/configuration/truncation-settings'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_ENABLE', 'composer_truncation_enabled'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'composer_truncation_enabled', $this->config->get('composer_truncation_enabled')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_DISPLAY_READMORE_WHEN_NECESSARY', 'composer_truncation_readmore'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'composer_truncation_readmore', $this->config->get('composer_truncation_readmore'));?>
					</div>
				</div>

				<?php $mediaTypes = ['image', 'video', 'audio', 'gallery']; ?>

				<?php foreach ($mediaTypes as $media) { ?>
					<?php echo $this->fd->html('settings.dropdown', 'composer_truncate_' . $media . '_position', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS', $mediaPositions, '', 'data-composer-truncate-option=' . $media); ?>

					<?php if ($media != 'gallery') { ?>
					<div data-composer-truncate-items-<?php echo $media;?> class="form-group <?php echo $this->config->get('composer_truncate_' . $media . '_position') == 'hidden' ? 'hide' : '';?>">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_LIMITS', 'composer_truncate_' . $media . '_limit'); ?>

						<div class="col-md-7">
							<input type="text" name="composer_truncate_<?php echo $media; ?>_limit" id="composer_truncate_<?php echo $media; ?>_limit"
								class="form-control input-mini text-center" value="<?php echo $this->config->get('composer_truncate_'.$media.'_limit' , '0');?>" />
						</div>
					</div>
					<?php } ?>

				<?php } ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'composer_truncation_chars', 'COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_MAX_CHARS', '', [
					'size' => '5',
					'postfix' => 'Characters'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'main_truncate_type', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE', [
					'chars' => 'COM_EASYBLOG_BY_CHARACTERS',
					'words' => 'COM_EASYBLOG_BY_WORDS',
					'paragraph' => 'COM_EASYBLOG_BY_PARAGRAPH',
					'break' => 'COM_EASYBLOG_BY_BREAK'
				], '', 'data-truncate-type'); ?>

				<?php echo $this->fd->html('settings.text', 'layout_maxlengthasintrotext', 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT', '', [
					'size' => '5',
					'postfix' => 'Characters',
					'visible' => $this->config->get('main_truncate_type') === 'chars' || $this->config->get('main_truncate_type') === 'words',
					'wrapperAttributes' => 'data-max-chars'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'main_truncate_maxtag', 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS', '', [
					'size' => '5',
					'postfix' => 'Tags',
					'visible' => $this->config->get('main_truncate_type') === 'break' || $this->config->get('main_truncate_type') === 'paragraph',
					'wrapperAttributes' => 'data-max-tag'
				]); ?>

			</div>
		</div>

	</div>
</div>
