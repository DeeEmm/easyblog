<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewTeamBlog extends EasyBlogView
{
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$id = $this->input->get('id', '', 'cmd');
		$team = EB::table('TeamBlog');
		$team->load($id);

		$this->doc->link = EBR::_('index.php?option=com_easyblog&view=latest');
		$this->doc->setTitle(JText::sprintf('COM_EASYBLOG_FEEDS_TEAMBLOGS_TITLE', $team->title));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_TEAMBLOGS_DESC', $team->title));

		$posts = $this->getPosts($id);

		if (!$posts) {
			return;
		}

		$this->doc->items = EB::formatter('feeds', $posts);
	}

	/**
	 * Retrieves a list of team blog posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($team)
	{
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'));
		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('teamblog', $team, $sort);

		return $posts;
	}
}
