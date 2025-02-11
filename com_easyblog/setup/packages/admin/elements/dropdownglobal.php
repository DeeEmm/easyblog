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

class JFormFieldDropdownGlobal extends EasyBlogFormField
{
	protected $type = 'DropdownGlobal';

	/**
	 * Unique type of field to display a dropdown with a "Use Global" option to display the default value from the settings
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$items = $this->element->children();
		$options = [];

		foreach ($items as $item) {
			$options[] = (object) [
				'title' => (string) $item,
				'value' => (string) $item->attributes()->value
			];
		}

		$configName = (string) $this->element->attributes()->config;
		$configValue = $this->config->get($configName);
		$globalValue = '';

		foreach ($options as $option) {
			if ($option->value == $configValue) {
				$globalValue = $option->title;
			}
		}

		$theme = EB::themes();
		$theme->set('globalValue', $globalValue);
		$theme->set('options', $options);
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);

		$output = $theme->output('admin/elements/dropdownglobal');

		return $output;
	}
}
