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

require_once(__DIR__ . '/abstract.php');

class JFormFieldTextExt extends EasyBlogFormField
{
	protected $type = 'TextExt';

	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Initialize some field attributes.
		$size         = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
		$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$disabled     = $this->disabled ? ' disabled' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
		$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
		$autofocus    = $this->autofocus ? ' autofocus' : '';
		$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';
		$pattern      = !empty($this->pattern) ? ' pattern="' . $this->pattern . '"' : '';
		$inputmode    = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
		$dirname      = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';
		$style        = $this->value == '-1' ? ' style="display:none;" ' : ' ';

		// Initialize JavaScript field attributes.
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		$datalist = '';
		$list     = '';

		/* Get the field options for the datalist.
		Note: getSuggestions() is deprecated and will be changed to getOptions() with 4.0. */
		$options  = (array) $this->getOptions();

		if ($options)
		{
			$datalist = '<datalist id="' . $this->id . '_datalist">';

			foreach ($options as $option)
			{
				if (!$option->value)
				{
					continue;
				}

				$datalist .= '<option value="' . $option->value . '">' . $option->text . '</option>';
			}

			$datalist .= '</datalist>';
			$list     = ' list="' . $this->id . '_datalist"';
		}

		// $inputName = (string) $this->element['name'];
		// $radioName = str_replace($inputName , $inputName . '_option', $this->name);
		// $textName = str_replace($inputName , $inputName . '_txt', $this->name);
		//
		//
		// var_dump($this->id);exit;

		$radioId = $this->id . '_option';
		$textId = $this->id . '_txt';
		$hiddenId = $this->id;

		$config = EB::config();
		$previousVal = $this->value != '-1' ? $this->value : $config->get('layout_' . (string) $this->element['name'], '3' );

		$functionNameRadio = 'changeValue' . $this->id . 'Radio';
		$functionName = 'changeValue' . $this->id;

		$extraAttr = $class . $style . $size . $disabled . $readonly . $list . $hint . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern;


		$theme = EB::themes();

		$theme->set('eleId', $this->id);
		$theme->set('eleValue', $this->value);
		$theme->set('eleName', $this->name);

		$theme->set('dirname', $dirname);
		// dirname

		$theme->set('radioId', $radioId);
		$theme->set('textId', $textId);
		$theme->set('hiddenId', $hiddenId);
		$theme->set('previousVal', $previousVal);
		$theme->set('functionNameRadio', $functionNameRadio);
		$theme->set('functionName', $functionName);
		$theme->set('extraAttr', $extraAttr);


		$output = $theme->output('admin/elements/textext');

		// $html[] = "
		// 	<script type=\"text/javascript\">
		// 		function $functionNameRadio() {
		// 			if (jQuery('input[name=$radioId]:checked').val() == '-1') {
		// 				jQuery('input#$hiddenId').val('-1');
		// 				jQuery('input[name=$textId]').hide();
		// 			} else {
		// 				jQuery('input[name=$textId]').val('$previousVal');
		// 				jQuery('input#$hiddenId').val('$previousVal');
		// 				jQuery('input[name=$textId]').show();
		// 			}
		// 		}

		// 		function $functionName() {
		// 			var val = jQuery('input[name=$textId]').val();
		// 			jQuery('input#$hiddenId').val(val);
		// 		}
		// 	</script>
		// ";

		// $checked = $this->value == '-1' ? ' checked="true" ' : ' ';
		// $html[] = '<input type="radio" name="' . $radioId . '" value="-1" onclick="' . $functionNameRadio . '()"' . $checked . '/>&nbsp;' . JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS');

		// $checked = $this->value != '-1' ? ' checked="true" ' : ' ';
		// $html[] = '<input type="radio" name="' . $radioId . '" value="1" onclick="' . $functionNameRadio . '()"' . $checked . '/>&nbsp;' . JText::_('COM_EASYBLOG_USE_BELOW_OPTIONS');

		// $style = $this->value == '-1' ? ' style="display:none;" ' : ' ';
		// $html[] = '<input type="text" name="' . $textId . $dirname . '" value="'
		// 	. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" onchange="' . $functionName. '()" ' . $class . $style . $size . $disabled . $readonly . $list
		// 	. $hint . $maxLength . $required . $autocomplete . $autofocus . $spellcheck . $inputmode . $pattern . ' />';

		// $html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
		// 	. $this->value . '"' . ' />';

		return $output;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.4
	 */
	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_(
				'select.option', (string) $option['value'],
				JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text'
			);
		}

		return $options;
	}



}
