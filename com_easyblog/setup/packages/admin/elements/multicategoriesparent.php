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

class JFormFieldMultiCategoriesParent extends EasyBlogFormField
{
	protected $type = 'MultiCategoriesParent';

	/**
	 * Displays only parent categories for the form
	 *
	 * @since	5.0
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		// Retrieve the list of categories on the site.
		$model = EB::model('Category');
		$categories	= $model->getAllCategories(true);

		// Ensure that the selected value is always an array
		if (!is_array($this->value)) {
			$this->value = array($this->value);
		}

		$theme = EB::themes();
		$theme->set('categories', $categories);
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);

		$output = $theme->output('admin/elements/multicategories.parent');

		return $output;
	}
}
