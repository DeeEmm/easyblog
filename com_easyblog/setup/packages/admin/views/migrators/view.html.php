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

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('easyblog.manage.migrator');

		$this->setHeading('COM_EASYBLOG_TITLE_MIGRATORS', '', 'fa-laptop');

		$layout = $this->getLayout();

		$contents = '';

		if (method_exists($this, $layout)) {
			$contents = $this->$layout();
		} else {
			return $this->app->redirect('index.php?option=com_easyblog&view=migrators&layout=joomla');
		}

		JToolBarHelper::custom('migrators.purge', 'delete.png', 'delete_f2.png', JText::_('COM_EASYBLOG_PURGE_HISTORY'), false);

		$this->set('contents', $contents);

		parent::display('migrators/default');
	}

	/**
	 * Renders the migration form for blogger
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function blogger()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_BLOGGERXML', '', 'fa-laptop');

		$adapter = EB::migrator()->getAdapter('blogger_xml');
		$files = $adapter->getFiles();

		$files = JFolder::files(JPATH_ROOT);
		$categories = $adapter->getEasyBlogCategories();

		$theme = EB::themes();
		$theme->set('categories', $categories);
		$theme->set('files', $files);
		$output = $theme->output('admin/migrators/adapters/blogger');

		return $output;
	}

	/**
	 * Renders the migration form for K2
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function k2()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_K2', '', 'fa-retweet');

		$adapter = EB::migrator()->getAdapter('K2');
		$installed = $adapter->isInstalled();
		$categories = $adapter->getCategories();

		$theme = EB::themes();
		$theme->set('categories', $categories);
		$theme->set('installed', $installed);

		$output = $theme->output('admin/migrators/adapters/k2');
		return $output;
	}

	/**
	 * Renders the wordpress migration form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function wordpress()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_WORDPRESS_IMPORTXML');

		// Get the xml files for wordpress
		$adapter = EB::migrator()->getAdapter('wordpress_xml');
		$files = $adapter->getFiles();

		$theme = EB::themes();
		$theme->set('files', $files);

		$output = $theme->output('admin/migrators/adapters/wordpress');
		
		return $output;
	}

	/**
	 * Renders the migration form for wordpress for joomla
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function wordpressjoomla()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_WORDPRESSJOOMLA');

		$adapter = EB::migrator()->getAdapter('wordpress');
		$installed = $adapter->isInstalled();
		$blogs = '';

		if ($installed) {
			$blogs = JHTML::_('select.genericlist',  $adapter->getBlogs(), 'wpBlogId', 'class="form-control" data-blogid-wordpress', 'value', 'state', '');
		}

		$theme = EB::themes();
		$theme->set('installed', $installed);
		$theme->set('blogs', $blogs);

		return $theme->output('admin/migrators/adapters/wordpressjoomla');
	}

	/**
	 * Renders the migration form for Zoo
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function zoo()
	{
		$adapter = EB::migrator()->getAdapter('zoo');
		$installed = $adapter->isInstalled();
		
		$this->setHeading('COM_EASYBLOG_MIGRATOR_ZOO', '', 'fa-retweet');

		$apps = '';

		if ($installed) {
			$apps = $adapter->getApps();
		}

		$theme = EB::themes();
		$theme->set('apps', $apps);
		$theme->set('installed', $installed);

		return $theme->output('admin/migrators/adapters/zoo');
	}

	/**
	 * Renders the Joomla articles migration form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function joomla()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_JOOMLA', '', 'fa-joomla');

		$categories[] = JHTML::_('select.option', '0', JText::_('COM_EASYBLOG_MIGRATORS_SELECT_CATEGORY'));
		$authors[] = JHTML::_('select.option', '0', JText::_('COM_EASYBLOG_MIGRATORS_SELECT_AUTHOR'), 'created_by', 'name');

		$lists['sectionid'] = array();

		$articleCat = JHtml::_('category.options', 'com_content');

		$articleAuthors	= $this->get( 'ArticleAuthors16' );

		$categories	= array_merge($categories, $articleCat);
		$lists['catid'] = JHTML::_('select.genericlist',  $categories, 'catId', 'class="form-control" data-migrate-article-category', 'value', 'text', '');

		$authors = array_merge($authors, $articleAuthors);
		$lists['authorid'] = JHTML::_('select.genericlist',  $authors, 'authorId', 'class="form-control" data-migrate-article-author', 'created_by', 'name', 0);

		$state = array('1' => 'Published', '0' => 'Unpublished', '2' => 'Archived', '-2' => 'Trash');
		$articleState = array();
		
		foreach($state as $key => $val) {
			$obj = new stdClass();
			$obj->state	= $val;
			$obj->value	= $key;

			$articleState[]	= $obj;
		}

		$stateList = array();
		$stateList[] = JHTML::_('select.option', '*', JText::_('COM_EASYBLOG_MIGRATORS_SELECT_STATE'), 'value', 'state');

		$stateList = array_merge($stateList, $articleState);
		$lists['state']	= JHTML::_('select.genericlist',  $stateList, 'stateId', 'class="form-control" data-migrate-article-state', 'value', 'state', '*');

		$adapter = EB::migrator()->getAdapter('Content');

		$categoryFilter = EB::populateCategories('', '', 'select', 'categoryid', '', false, true, false, array(), 'data-easyblog-category', 'COM_EASYBLOG_MIGRATORS_SELECT_EASYBLOG_CATEGORY');

		$theme = EB::themes();
		$theme->set('lists', $lists);
		$theme->set('categories', $categoryFilter);

		$output = $theme->output('admin/migrators/adapters/article');

		return $output;
	}
}
