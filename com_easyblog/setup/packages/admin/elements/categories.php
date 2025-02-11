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

class JFormFieldCategories extends EasyBlogFormField
{
	protected $type = 'Categories';

	/**
	 * Displays the category selection form
	 *
	 * @since	5.0
	 * @access	public
	 */	
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}

		$this->set('id', $this->id);
		$this->set('name', $this->name);
		$this->set('value', $this->value);

		return $this->output('admin/elements/categories');
	}
}
