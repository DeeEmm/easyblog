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

class EasyBlogControllerSubscriptions extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'save');
	}

	/**
	 * Create new subscribers on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function create()
	{
		// Check for request forgeries
		FH::checkToken();

		$redirect = 'index.php?option=com_easyblog&view=subscriptions&layout=form';

		$subscription = EB::table('Subscriptions');

		$subscription->utype = $this->input->get('type', 'site', 'word');
		$subscription->uid = $this->input->get('cid_' . $subscription->utype, 0, 'int');
		$subscription->fullname = $this->input->get('fullname', '', 'string');
		$subscription->email = $this->input->get('email', '', 'email');

		// If the subscription type is entry or category, ensure that there is a uid
		if ($subscription->utype !== 'site' && !$subscription->uid) {

			if ($subscription->utype == 'entry') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_ENTRY', 'error');
			}

			if ($subscription->utype == 'category') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_CATEGORY', 'error');
			}

			if ($subscription->utype == 'blogger') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_BLOGGER', 'error');
			}

			if ($subscription->utype == 'team') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_TEAM', 'error');
			}

			return $this->app->redirect($redirect);
		}

		if (!$subscription->fullname) {
			EB::info()->set(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PLEASE_ENTER_NAME'), 'error');

			return $this->app->redirect($redirect);
		}

		if (!$subscription->email) {
			EB::info()->set(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PLEASE_ENTER_EMAIL'), 'error');

			return $this->app->redirect($redirect);
		}

		// check for the current email is it already subscribed
		$isSubscribed = $subscription->isSubscribed($subscription->email, $subscription->uid, $subscription->utype);

		if ($isSubscribed) {
			$this->info->set('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR', 'error');
			return $this->app->redirect($redirect);
		}

		// now let save the subscription
		$state = $subscription->store();

		$redirect = 'index.php?option=com_easyblog&view=subscriptions&filter=' . $subscription->utype;

		if ($state === EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN) {
			$this->info->set('As the site runs on double opt-in, we have sent a confirmation e-mail to the user to confirm subscription. Their e-mail will not appear in the list until they click on the link in the e-mail');
			return $this->app->redirect($redirect);
		}

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_SUBSCRIPTIONS_CREATED', 'subscriptions', [
			'link' => 'index.php?option=com_easyblog&view=subscriptions&layout=form&id=' . $subscription->id,
			'userEmail' => $email
		]);


		$this->info->set('COM_EASYBLOG_SUBSCRIPTIONS_ADDED_SUCCESS', 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Saves changes to an existing subscription
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');

		$redirect = 'index.php?option=com_easyblog&view=subscriptions';

		$subscription = EB::table('Subscriptions');
		$subscription->load($id);

		$task = $this->getTask();

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=subscriptions&layout=form&id=' . $subscription->id;
		}

		$subscription->utype = $this->input->get('type', 'site', 'word');
		$subscription->uid = $this->input->get('cid_' . $subscription->utype, 0, 'int');

		$subscription->fullname = $this->input->get('fullname', '', 'string');
		$subscription->email = $this->input->get('email', '', 'email');

		// If the subscription type is entry or category, ensure that there is a uid
		if ($subscription->utype !== 'site' && !$subscription->uid) {

			if ($subscription->utype == 'entry') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_ENTRY', 'error');
			}

			if ($subscription->utype == 'category') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_CATEGORY', 'error');
			}

			if ($subscription->utype == 'blogger') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_BLOGGER', 'error');
			}

			if ($subscription->utype == 'team') {
				$this->info->set('COM_EASYBLOG_SUBSCRIPTION_PLEASE_SELECT_TEAM', 'error');
			}

			$redirect = 'index.php?option=com_easyblog&view=subscriptions&layout=form&id=' . $subscription->id;

			return $this->app->redirect($redirect);
		}

		// check for the current email is it already subscribed
		$isSubscribed = $subscription->isSubscribed($subscription->email, $subscription->uid, $subscription->utype);

		if ($isSubscribed) {
			$this->info->set('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR', 'error');
			$redirect = 'index.php?option=com_easyblog&view=subscriptions&layout=form&id=' . $subscription->id;

			return $this->app->redirect($redirect);
		}

		$subscription->store(true, true);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_SUBSCRIPTIONS_UPDATED', 'subscriptions', [
			'link' => 'index.php?option=com_easyblog&view=subscriptions&layout=form&id=' . $subscription->id,
			'userEmail' => $subscription->email
		]);

		$this->info->set('COM_EASYBLOG_SUBSCRIPTION_SAVED_SUCCESS', 'success');

		return $this->app->redirect($redirect);
	}

	/**
	 * Removes a subscriber from the list
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess( 'subscription' );

		$ids = $this->input->get('cid', [], 'array');
		$filter = $this->input->get('filter', '', 'cmd');

		if (!$filter) {
			$this->info->set('COM_EASYBLOG_ERROR_REMOVING_SUBSCRIPTION_MISSING_SUBSCRIPTION_TYPE', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		if (!$filter) {
			$this->info->set('COM_EASYBLOG_INVALID_SUBSCRIPTION_ID', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		foreach ($ids as $id) {

			$id = (int) $id;

			if (!$id) {
				continue;
			}

			$table = EB::table('Subscriptions');
			$table->load((int) $id);
			$table->delete();

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_SUBSCRIPTIONS_DELETED', 'subscriptions', [
				'userEmail' => $table->email
			]);
		}

		$this->info->set('COM_EASYBLOG_SUBSCRIPTION_DELETED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
	}

	/**
	 * Allow users to import csv files into subscriptions table
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function importFile()
	{
		// Check for request forgeries
		FH::checkToken();

		$file = $this->input->files->get('package');
		// $file = $this->input->get( 'package', '', 'files', 'array' );

		$model = EB::model('Subscription');

		// Check if the file exists
		if (!$file || !isset($file['tmp_name']) || empty($file['tmp_name'])) {

			EB::info()->set('COM_EASYBLOG_SUBSCRIPTION_IMPORT_FILE_NOT_EXIST', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $file['tmp_name'];
		$fileName = $file['name'];

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$uploadPath = JPATH_ROOT . '/tmp/' . $fileName;
		$result = $model->massAssignSubscriber($fileTemp);

		if($result){
			// Redirect user back
			EB::info()->set(JText::sprintf('COM_EASYBLOG_SUBSCRIPTION_IMPORT_ADDED', count($result)), 'success');

			// $this->app 	= JFactory::getApplication();
			$this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}
		else
		{
			EB::info()->set('COM_EASYBLOG_SUBSCRIPTION_IMPORT_NOONE_ADDED', 'success');
			$this->app->redirect('index.php?option=com_easyblog&view=subscriptions');
		}

	}

	/**
	 * Exports subscribers into csv format
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function export()
	{
		FH::checkToken();

		$output = fopen('php://output', 'w');

		// Determines if this export is on specific subscription type
		$type = $this->input->get('type', 'site', 'word');

		// Get a list of users and their custom fields
		$model = EB::model('Subscription');
		$data = $model->export($type);

		// Output each row now
		foreach ($data as $row) {
			fputcsv($output, (array) $row);
		}

		// var_dump($output);exit;
		// Generate the date of export
		$date = EB::date();
		$fileName = 'subscribers_export_' . $type . '_' . $date->format('m_d_Y') . '.csv';

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $fileName);

		fclose($output);
		exit;

	}
}
