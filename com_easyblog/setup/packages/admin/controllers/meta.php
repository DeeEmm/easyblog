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

class EasyBlogControllerMeta extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
		$this->registerTask('addIndexing', 'saveIndexing');
		$this->registerTask('removeIndexing', 'saveIndexing');
		$this->registerTask('delete', 'delete');
	}

	/**
	 * Saves a new meta object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('meta');

		// Default return url
		$return = JRoute::_('index.php?option=com_easyblog&view=metas' , false);

		$post = $this->input->getArray('post');

		if (!isset($post['id']) || empty($post['id'])) {
			$this->info->set('COM_EASYBLOG_INVALID_META_TAG_ID', 'error');

			return $this->app->redirect($return);
		}

		$meta = EB::table('Meta');
		$meta->load((int) $post['id']);

		$meta->bind($post);

		// Save the meta object
		$meta->store();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_SEO_UPDATED', 'meta', array(
			'link' => 'index.php?option=com_easyblog&view=metas&layout=form&id=' . $meta->id,
			'pageTitle' => $meta->getTitle()
		));

		$task = $this->getTask();

		if ($task == 'apply') {
			$return = 'index.php?option=com_easyblog&view=metas&layout=form&id=' . $meta->id;
		}

		$this->info->set('COM_EASYBLOG_META_SAVED', 'success');

		return $this->app->redirect($return);
	}

	public function saveIndexing()
	{
		FH::checkToken();

		$this->checkAccess('meta');

		$app = JFactory::getApplication();
		$task = $this->getTask();
		$cid = $this->input->get('cid', 0, 'int');

		$meta = EB::table('Meta');
		$meta->load($cid[0]);

		if (empty($cid) || !$meta->id) {
			$app->redirect( 'index.php?option=com_easyblog&view=metas' , JText::_( 'COM_EASYBLOG_INVALID_ID_PROVIDED') , 'error' );
			$app->close();
		}

		$meta->indexing = $task == 'addIndexing' ? 1 : 0;
		$meta->store();

		$actionString = $task == 'addIndexing' ? 'COM_EB_ACTIONLOGS_SEO_ENABLED_INDEXING' : 'COM_EB_ACTIONLOGS_SEO_DISABLED_INDEXING';
		$actionlog = EB::fd()->getActionLog();
		$actionlog->log($actionString, 'meta', array(
			'link' => 'index.php?option=com_easyblog&view=metas&layout=form&id=' . $meta->id,
			'pageTitle' => $meta->getTitle()
		));

		$message = $task == 'addIndexing' ? 'COM_EASYBLOG_META_ENABLED_INDEXING' : 'COM_EASYBLOG_META_DISABLED_INDEXING';
		$message = JText::_($message);

		$app->redirect( 'index.php?option=com_easyblog&view=metas' , $message );
	}

	/**
	 * Deletes metas from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl rules.
		$this->checkAccess('meta');

		// Get the list of metas to be deleted
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('Invalid meta id'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=metas');
		}

		// Do whatever you need to do here
		foreach ($ids as $id) {

			$meta = EB::table('Meta');
			$meta->load((int) $id);

			$meta->delete();

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_SEO_DELETED', 'meta', array(
				'pageTitle' => $meta->getTitle()
			));
		}

		$this->info->set('COM_EASYBLOG_METAS_META_REMOVED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=metas');
	}

	/**
	 * Create those blogger meta which missing in meta table
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function restoreMeta()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('meta');

		// Retrieve the meta type what user select
		$metaType = $this->input->get('meta_type', '', 'default');

		// Currently, only support 3 restore meta types
		$isBloggerMetaType = $metaType === 'blogger';
		$isCategoryMetaType = $metaType === 'category';
		$isTagMetaType = $metaType === 'tag';

		// Default return url
		$return = JRoute::_('index.php?option=com_easyblog&view=metas', false);

		if ($isBloggerMetaType) {
			$bloggerModel = EB::model('Bloggers');
			$results = $bloggerModel->getMissingMetaBloggers();
		}

		if ($isCategoryMetaType) {
			$categoriesModel = EB::model('Categories');
			$results = $categoriesModel->getMissingMetaCategories();
		}

		if ($isTagMetaType) {
			$tagModel = EB::model('Tags');
			$results = $tagModel->getMissingMetaTags();
		}

		if (!$results || !$metaType) {
			$this->info->set('COM_EB_UPDATE_MISSING_META_EMPTY', 'success');
			return $this->app->redirect($return);
		}

		$model = EB::model('Metas');

		foreach ($results as $result) {
			if ($isBloggerMetaType) {
				// Ensure that the user has authoring rights
				$acl = EB::acl($result->id);

				if (!$acl->get('add_entry')) {
					continue;
				}
			}

			$model->createMeta($result->id, $metaType);
		}

		$this->info->set('COM_EB_UPDATE_MISSING_META_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}
}
