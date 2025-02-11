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

class JFormFieldInputvalue extends EasyBlogFormField
{
	protected $type = 'InputValue';

	/**
	 * Displays the post selection form
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		$value = $this->value;

		if (!$value) {
			$value = $this->default;
		}

		$theme = EB::themes();
		$theme->set('name', $this->name);
		$theme->set('value', $value);
		$theme->set('id', $this->id);
		$theme->set('idx', uniqid());

		$output = $theme->output('admin/elements/input.value');

		return $output;
	}
}
