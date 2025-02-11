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

class JFormFieldCover extends EasyBlogFormField
{
	protected $type = 'Cover';

	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$title = JText::_('COM_EASYBLOG_SELECT_A_USER');

		// Default values
		$crop = (int) $this->element['crop'];
		$full = (int) $this->element['full'];
		$width = (int) $this->element['defaultwidth'];
		$height = (int) $this->element['defaultheight'];
		$alignment = (string) $this->element['alignment'];

		$hideFull = (int) $this->element['disablefull'];

		if ($this->value) {

			if (isset($this->value['width'])) {
				$width = (int) $this->value['width'];
			}
			
			if (isset($this->value['height'])) {
				$height = (int) $this->value['height'];
			}

			if (isset($this->value['crop'])) {
				$crop = true;
			}

			if (isset($this->value['full'])) {
				$full = true;
			}

			if (isset($this->value['alignment'])) {
				$alignment = (string) $this->value['alignment'];
			}

			// User did not want to crop.
			if (!isset($this->value['crop'])) {
				$crop = false;
			}

			// User did not want to use full width.
			if (!isset($this->value['full'])) {
				$full = false;
			}
		}


		$theme = EB::themes();
		$theme->set('hideFull', $hideFull);
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('title', $title);
		$theme->set('full', $full);
		$theme->set('crop', $crop);
		$theme->set('width', $width);
		$theme->set('height', $height);
		$theme->set('alignment', $alignment);

		$output = $theme->output('admin/elements/cover');

		return $output;
	}
}
