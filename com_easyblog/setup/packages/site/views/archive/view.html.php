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

class EasyBlogViewArchive extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Get the archives model
		$model = EB::model('Archive');

		$limit = EB::getLimit();

		$posts = $model->getPosts([], $limit);
		$posts = EB::formatter('list', $posts);

		// Get the pagination
		$pagination = $model->getPagination();
		$pagination = $pagination->getPagesLinks();

		// meta
		EB::setMeta(META_ID_ARCHIVE, META_TYPE_VIEW);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_ARCHIVE_PAGE_TITLE'));
		parent::setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('archives/list/default');
	}
}
