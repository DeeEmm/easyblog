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
			<?php echo $this->html('media.preview', ['icon' => $file->icon, 'class' => 't-text--center']); ?>

			<div class="eb-nmm-panel-block">
				<?php echo $this->html('media.field', 'media.textbox', 'title', 'COM_EASYBLOG_MM_PANEL_TITLE', $file->title, 'data-mm-panel-title data-mm-panel-input'); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'showicon', 'COM_EASYBLOG_BLOCKS_FILE_SHOW_EXTENSION_ICON', $params->get('showicon', true), 'data-mm-panel-input'); ?>

				<?php echo $this->html('media.field', 'form.toggler', 'showsize', 'COM_EASYBLOG_BLOCKS_FILE_SHOW_FILE_SIZE', $params->get('showsize', true), 'data-mm-panel-input'); ?>
			</div>
		</form>
	</div>
</div>
