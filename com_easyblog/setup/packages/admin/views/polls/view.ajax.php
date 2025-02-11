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

require_once(JPATH_COMPONENT . '/views.php');

class EasyBlogViewPolls extends EasyBlogAdminView
{
	/**
	 * Display the poll form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('easyblog.manage.polls');

		EB::loadLanguages();

		$pollId = $this->input->get('pollId', 0, 'int');
		$poll = EB::polls($pollId);

		$info = EB::info();
		$info->set('', 'error');
		$error = $info->html();

		$themes = EB::themes();
		$themes->set('poll', $poll);
		$html = $themes->output('admin/polls/form/default');

		$themes = EB::themes();
		$themes->set('error', $error);
		$themes->set('html', $html);
		$output = $themes->output('admin/polls/dialogs/form');

		return $this->ajax->resolve($output);
	}
}