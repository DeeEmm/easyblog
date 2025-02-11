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
<div class="eb-fields-hyperlink">
	<div>
		<textarea class="o-form-control"
				name="<?php echo $formElement;?>[<?php echo $field->id;?>][textlink]"
				placeholder="<?php echo $params->get('placeholder');?>"
				cols="1"
				rows="2"
				style="resize: none;"
				data-field-class-input-hyperlink
			><?php echo isset($value->textlink) ? $value->textlink : '';?></textarea>
	</div>

	<div style="padding-top: 10px;">
		<input type="text"
			class="o-form-control"
			name="<?php echo $formElement;?>[<?php echo $field->id;?>][url]"
			value="<?php echo isset($value->url) ? $value->url : '';?>"
			placeholder="<?php echo JText::_('COM_EB_FIELDS_TYPE_HYPERLINK_URL_PLACEHOLDER');?>"
		/>
	</div>

	<div style="padding-top: 10px;" data-test="<?php echo $formElement . '[' . $field->id . '][targetblank]'; ?>">
		<?php echo $this->fd->html('form.dropdown', $formElement . '[' . $field->id . '][targetblank]', isset($value->targetblank) && $value->targetblank ? $value->targetblank : 1, [
			'1' => 'Open in new tab',
			'0' => 'Stay on same page'
		], [
			'baseClass' => 'o-form-control'
		]); ?>
	</div>
</div>