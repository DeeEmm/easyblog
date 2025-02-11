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
	<width>400</width>
	<height>250</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{submitButton}": "[data-submit-button]",
		"{radioInline}": "[data-importstyle-inline]",
		"{radioPlain}": "[data-importstyle-plain]"
	}
	</selectors>
	<title>
		<?php echo JText::_('COM_EB_GOOGLEIMPORT_TITLE');?>
	</title>
	<content>
			<div class="t-lg-mb--lg"><?php echo JText::_('COM_EB_GOOGLEIMPORT_DIALOG_CONTENT');?></div>

			<div class="o-radio t-lg-mt--md">
				<input type="radio" id="importstyle1" name="importstyle" value="inline" data-importstyle-inline checked="checked"/>
				<label for="importstyle1"><?php echo JText::_('COM_EB_GOOGLEIMPORT_DIALOG_RADIO_INLINE'); ?></label>
				<div class="muted t-lg-pl--sm"><?php echo JText::_('COM_EB_GOOGLEIMPORT_DIALOG_RADIO_INLINE_INFO'); ?></div>
			</div>
			<div class="o-radio t-lg-mt--lg">
				<input type="radio" id="importstyle2" name="importstyle" value="plain" data-importstyle-plain />
				<label for="importstyle2"><?php echo JText::_('COM_EB_GOOGLEIMPORT_DIALOG_RADIO_PLAIN'); ?></label>
				<div class="muted t-lg-pl--sm"><?php echo JText::_('COM_EB_GOOGLEIMPORT_DIALOG_RADIO_PLAIN_INFO'); ?></div>
			</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EB_GOOGLEIMPORT_DIALOG_IMPORT_NOW'); ?>
	</buttons>
</dialog>
