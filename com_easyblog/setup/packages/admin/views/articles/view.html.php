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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewArticles extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Check for access
		$this->checkAccess('easyblog.manage.blog');

		$search = $this->app->getUserStateFromRequest('com_easyblog.blogs.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));

		$options = array();

		if ($search) {
			$options['search'] = $search;
		}

		//Get data from the model
		$model = EB::model('Articles');
		$articles = $model->getItems($options);
		$pagination = $model->getPagination();

		$limit = $model->getState('limit');

		// Determines if the viewer is rendering this in a dialog
		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertArticle', 'cmd');

		$this->set('browsefunction', $browsefunction);
		$this->set('limit', $limit);
		$this->set('search', $search);
		$this->set('articles', $articles);
		$this->set('pagination', $pagination);

		parent::display('articles/default');
	}
}
