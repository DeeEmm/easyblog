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

class JFormFieldColumns extends EasyBlogFormField
{
	protected $type = 'Columns';

	/**
	 * Field to determine number of columns per row
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$min = (int) $this->element->attributes()->min;
		$min = !$min ? 2 : $min;

		$max = (int) $this->element->attributes()->max;
		$max = !$max ? 6 : $max;

		$useGlobal = (bool) $this->element->attributes()->useglobal;
		$globalValue = '';

		if ($useGlobal) {
			$configName = (string) $this->element->attributes()->config;
			$configValue = $this->config->get($configName);

			$globalValue = $configValue;
		}

		$theme = EB::themes();
		$theme->set('min', $min);
		$theme->set('max', $max);
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('isJoomla4', EB::isJoomla4());
		$theme->set('useGlobal', $useGlobal);
		$theme->set('globalValue', $globalValue);

		$output = $theme->output('admin/elements/columns');

		return $output;
	}
}
