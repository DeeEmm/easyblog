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

class JFormFieldPostStyle extends EasyBlogFormField
{
	protected $type = 'PostStyle';

	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$options = [
			'default' => 'COM_EB_FRONTPAGE_LAYOUT_STANDARD_OPTION',
			'card' => 'COM_EB_FRONTPAGE_LAYOUT_CARD_OPTION',
			'simple' => 'COM_EB_LAYOUT_SIMPLE_OPTION',
			'nickel' => 'COM_EB_LAYOUT_NICKEL_OPTION'
		];

		$useGlobal = (bool) $this->element->attributes()->useglobal;
		$globalValue = '';

		if ($useGlobal) {
			$configName = (string) $this->element->attributes()->config;
			$configValue = $this->config->get($configName);

			$globalValue = $options[$configValue];
		}

		$theme = EB::themes();
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('options', $options);
		$theme->set('useGlobal', $useGlobal);
		$theme->set('globalValue', $globalValue);

		$output = $theme->output('admin/elements/poststyle');

		return $output;
	}
}
