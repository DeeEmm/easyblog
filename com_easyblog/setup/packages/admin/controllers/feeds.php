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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerFeeds extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'unpublish');
	}

	public function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
	}

	public function add()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
	}

	/**
	 * Remove Feeds from the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		// Get the list of feeds to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
		}

		foreach ($ids as $id) {

			$feed = EB::table('Feed');
			$feed->load($id);

			if (!$feed->delete()) {
				$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_DELETE', 'error');

				return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

			}

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_FEEDS_DELETED', 'feeds', array(
				'feedTitle' => $feed->title
			));
		}

		$this->info->set('COM_EASYBLOG_BLOGS_FEEDS_DELETE_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

	}

	/**
	 * Stores a new rss feed import
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$post = $this->input->getArray('post');
		$id = $this->input->get('id', 0, 'int');

		$feed = EB::table('Feed');
		$feed->load($id);
		$feed->bind($post);

		$isNew = $id ? false : true;

		if (!$feed->item_creator) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_AUTHOR', 'error');

			$session = JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->item_category) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_CATEGORY', 'error');

			$session = JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->url) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_URL', 'error');

			$session = JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		if (!$feed->title) {
			EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_ERROR_TITLE', 'error');

			$session = JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		// Joomla 4 compatibility:
		// To Ensure id column type is integer
		$feed->item_team = (int) $feed->item_team;

		// Store the allowed tags here.
		$allowed = $this->input->get('item_allowed_tags', '', 'raw');
		$copyrights = $this->input->get('copyrights', '', 'default');
		$sourceLinks = $this->input->get('sourceLinks', 0, 'int');
		$feedamount = $this->input->get('feedamount', 0, 'int');
		$autopost = $this->input->get('autopost', 0, 'int');
		$cover = $this->input->get('cover', 0, 'bool');
		$canonical = $this->input->get('canonical', false, 'bool');
		$robots = $this->input->get('robots', '', 'default');

		$params = new JRegistry();
		$params->set('allowed', $allowed);
		$params->set('copyrights', $copyrights);
		$params->set('sourceLinks', $sourceLinks);
		$params->set('autopost', $autopost);
		$params->set('feedamount', $feedamount);
		$params->set('item_get_fulltext', $this->input->get('item_get_fulltext', '', 'default'));
		$params->set('notify', $this->input->get('notify', '', 'default'));
		$params->set('cover', $cover);
		$params->set('canonical', $canonical);
		$params->set('robots', $robots);

		$feed->params = $params->toString();
		$state = $feed->store();

		$actionString = $isNew ? 'COM_EB_ACTIONLOGS_FEEDS_CREATED' : 'COM_EB_ACTIONLOGS_FEEDS_UPDATED';
		$actionlog = EB::fd()->getActionLog();
		$actionlog->log($actionString, 'feeds', array(
			'link' => 'index.php?option=com_easyblog&view=feeds&layout=form&id=' . $feed->id,
			'feedTitle' => $feed->title
		));

		if (!$state) {
			EB::info()->set($feed->getError(), 'error');

			$session = JFactory::getSession();
			$session->set('feeds.data', $post, 'easyblog');

			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}

		EB::info()->set('COM_EASYBLOG_BLOGS_FEEDS_SAVE_SUCCESS', 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form&id=' . $feed->id);
		}

		if ($task == 'save') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
		}

		if ($task == 'savenew') {
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds&layout=form');
		}
	}

	/**
	 * Publishing RSS Feeds import item
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function publish()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$feeds = $this->input->get('cid', array(), 'array');

		if (!$feeds) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
		}

		$model = EB::model('Feeds');

		if ($model->publish($feeds, 1)) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_PUBLISH_SUCCESS'), 'success');
		} else {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_PUBLISH'), 'error');
		}

		foreach ($feeds as $id) {
			$feed = EB::table('Feed');
			$feed->load($id);

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_FEEDS_PUBLISHED', 'feeds', array(
				'link' => 'index.php?option=com_easyblog&view=feeds&layout=form&id=' . $feed->id,
				'feedTitle' => $feed->title
			));
		}
		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');

	}

	/**
	 * Unpublish RSS Feed import item
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function unpublish()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('feeds');

		$feeds = $this->input->get('cid', array(), 'array');

		if (!$feeds) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
		}

		$model = EB::model('Feeds');

		if ($model->publish($feeds, 0)) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_UNPUBLISH_SUCCESS'), 'success');
		} else {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_UNPUBLISH'), 'error');
		}

		foreach ($feeds as $id) {
			$feed = EB::table('Feed');
			$feed->load($id);

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_FEEDS_UNPUBLISHED', 'feeds', array(
				'link' => 'index.php?option=com_easyblog&view=feeds&layout=form&id=' . $feed->id,
				'feedTitle' => $feed->title
			));
		}

		return $this->app->redirect('index.php?option=com_easyblog&view=feeds');
	}
}
