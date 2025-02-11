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

class JFormFieldTags extends EasyBlogFormField
{
	protected $type = 'Tags';

	/**
	 * Displays the post selection form
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		$title = JText::_('COM_EASYBLOG_MENU_SELECT_A_TAG');

		$model = EB::model('Tags');
		$tags = $model->getData(false);

		if ($this->value) {
			$tag = EB::table('Tag');
			$tag->load($this->value);

			$title = $tag->title;
		}

		$theme = EB::themes();
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('title', $title);
		$theme->set('tags', $tags);

		$output = $theme->output('admin/elements/tags');

		return $output;
	}
}
