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
<div class="eb-nmm-info-panel__viewport">
	<div class="eb-nmm-info-panel__content">
		<form data-mm-form>
			<?php echo $this->html('media.preview', ['icon' => 'fdi fa fa-folder', 'class' => 't-text--center']); ?>

			<div class="eb-nmm-panel-block">
				<div class="o-form-group">
					<label class="o-control-label" for="mm-preview-title"><?php echo JText::_('COM_EASYBLOG_MM_PANEL_FOLDER_NAME');?></label>

					<?php if ($file->place != 'jomsocial' && $file->place != 'easysocial') { ?>
					<input type="text" name="title" id="mm-preview-title" value="<?php echo $file->type == 'folder' ? $file->filename : $file->title;?>" class="o-form-control input-sm" data-mm-info-title data-mm-folder-title />
					<?php } else { ?>
					<div><?php echo $file->type == 'folder' ? $file->filename : $file->title;?></div>
					<?php } ?>

				</div>
			</div>
		</form>
	</div>
</div>
