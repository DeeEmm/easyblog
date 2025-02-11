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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModal_Categories extends JFormField
{
	protected $type = 'Modal_Categories';

	protected function getInput()
	{
		$app = JFactory::getApplication();
		$attr = '';

		// Load the language
		$language = $app->getLanguage();
		$language->load('com_easyblog', JPATH_ADMINISTRATOR);

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$label = JText::_('COM_EASYBLOG_SELECT_CATEGORY_BUTTON');

		$group = [];
		$group[$label] = [];
		$group[$label]['items'] = JHtml::_('category.options', 'com_content');

		$html = JHTML::_('select.groupedlist', $group, $this->name, ['list.attr' => trim($attr), 'list.select' => $this->value]);

		return $html;
	}
}
