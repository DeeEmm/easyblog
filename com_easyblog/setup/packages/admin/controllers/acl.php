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

class EasyBlogControllerAcl extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');
	}

	/**
	 * Saves an acl
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('acl');

		// get current task name.
		$task = $this->getTask();

		$id = $this->input->get('id', 0, 'int');
		$name = $this->input->get('name', '', 'cmd');

		$link = 'index.php?option=com_easyblog&view=acls&layout=form&id=' . $id;

		// Ensure that the composite keys are provided.
		if (empty($id)) {
			$this->info->set('COM_EASYBLOG_ACL_INVALID_ID_ERROR', 'error');

			return $this->app->redirect($Link);
		}

		// Get the data from the post
		$data = $this->input->getArray('post');

		// Get the text filters first.
		$filter = EB::table('ACLFilter');
		$state = $filter->load($id);

		if (!$state) {
			$filter->content_id = $id;
			$filter->type = 'group';
		}

		// Set the disallowed tags
		$filter->disallow_tags = $data['disallow_tags'];
		$filter->disallow_attributes = $data['disallow_attributes'];
		$filter->store();

		// Load the acl model
		$model = EB::model('ACL');

		// Delete all existing rule set
		$state = $model->deleteRuleset($id);

		// Unset unecessary data form the post
		unset($data['task']);
		unset($data['option']);
		unset($data['c']);
		unset($data['id']);
		unset($data['name']);
		unset($data['disallow_tags']);
		unset($data['disallow_attributes']);

		// Insert new rules
		$state = $model->insertRuleset($id, $data);

		$groupTitle = $model->getUsergroupTitle($id);

		$actionlog = EB::fd()->getActionLog();

		$actionlog->log('COM_EB_ACTIONLOGS_ACL_UPDATE', 'acls', array(
			'link' => $link,
			'group' => JText::_($groupTitle)
		));

		if (!$state) {
			$this->info->set('COM_EASYBLOG_ACL_ERROR_SAVING_ACL', 'error');

			return $this->app->redirect($link);
		}

		$url = 'index.php?option=com_easyblog&view=acls';
		
		if ($task == 'apply') {
			$url = $link;
		}

		$this->info->set('COM_EASYBLOG_ACL_SAVE_SUCCESS', 'success');
		return $this->app->redirect($url);
	}
}
