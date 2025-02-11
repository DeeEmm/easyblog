<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class JFormFieldMultiTags extends EasyBlogFormField
{
	protected $type = 'MultiTags';

	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		// Retrieve the list of categories on the site.
		$model = EB::model('Tags');
		$tags	= $model->getTags();

		// Ensure that the selected value is always an array
		if (!is_array($this->value)) {
			$this->value = array($this->value);
		}

		$isJoomla4 = EB::isJoomla4();

		$theme = EB::themes();
		$theme->set('tags', $tags);
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('isJoomla4', $isJoomla4);

		if ($isJoomla4) {
			JFactory::getApplication()->getDocument()->getWebAssetManager()
			->usePreset('choicesjs')
			->useScript('webcomponent.field-fancy-select');
		}

		$output = $theme->output('admin/elements/multitags');

		return $output;
	}

}
