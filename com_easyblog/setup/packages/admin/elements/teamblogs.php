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

class JFormFieldTeamBlogs extends EasyBlogFormField
{
	protected $type = 'TeamBlogs';

	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		$title = JText::_('COM_EASYBLOG_SELECT_A_TEAM');

		if ($this->value) {
			$team = EB::table('TeamBlog');
			$team->load($this->value);

			$title = $team->title;
		}

		$theme = EB::themes();
		$theme->set('id', $this->id);
		$theme->set('name', $this->name);
		$theme->set('value', $this->value);
		$theme->set('title', $title);

		$output = $theme->output('admin/elements/team');

		return $output;
	}
}
