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

class EasyBlogControllerSpools extends EasyBlogController
{
	/**
	 * Purges all emails from the system
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purge()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('mail');

		$model = EB::model('Spools');
		$model->purge();

		$this->info->set(JText::_('COM_EASYBLOG_MAILS_PURGED'), 'success');

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_MAILS_PURGED', 'spools');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Purge Sent items
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function purgeSent()
	{
		// Check for request forgeries
		FH::checkToken();

		// Check for acl
		$this->checkAccess('mail');

		$model = EB::model('Spools');
		$model->purge('sent');

		$this->info->set('COM_EASYBLOG_SENT_MAILS_PURGED', 'success');

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_MAILS_SENT_PURGED', 'spools');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Deletes a mailer item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		FH::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('mail');

		$mails = $this->input->get('cid', [], 'array');

		if (!$mails) {
			$message = JText::_('COM_EASYBLOG_NO_MAIL_ID_PROVIDED');

			$this->info->set($message, 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=spools');
		}

		foreach ($mails as $id) {
			$table = EB::table('MailQueue');
			$table->load((int) $id);

			$table->delete();
		}

		$this->info->set('COM_EASYBLOG_SPOOLS_DELETE_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Resets a list of email template files to it's original state
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reset()
	{
		// Check for request forgeries
		FH::checkToken();

		$files = $this->input->get('cid', [], 'default');
		$files = EB::makeArray($files);

		if (!$files) {
			$this->info->set('COM_EASYBLOG_EMAIL_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=spools&layout=editor');
		}

		$model = EB::model("Spools");

		foreach ($files as $file) {
			$model->reset($file);
		}

		$this->info->set('COM_EASYBLOG_EMAIL_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools&layout=editor');
	}

	/**
	 * Store email template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveFile()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the contents of the email template
		$contents = $this->input->get('source', '', 'raw');

		// To determine if this is coming from Foundry
		$base = $this->input->get('base', 0, 'int');

		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		// Get the overriden path
		$model = EB::model("Spools");

		$path = $model->getOverrideFolder($file, $base);
		$state = $model->write($path, $contents);

		$redirect = 'index.php?option=com_easyblog&view=spools&layout=editor';

		if ($state) {
			$this->info->set('COM_EASYBLOG_EMAILS_TEMPLATE_FILE_SAVED_SUCCESSFULLY', 'success');

			$actionlog = EB::fd()->getActionLog();
			$actionlog->log('COM_EB_ACTIONLOGS_MAIL_TEMPLATE_UPDATED', 'spools', array(
				'file' => str_ireplace('/', '', $file),
				'link' => 'index.php?option=com_easyblog&view=spools&layout=editfile&file=' . urlencode($file)
			));

			return $this->app->redirect($redirect);
		}

		$this->info->set('COM_EB_EMAILS_TEMPLATE_FILE_SAVED_FAILED', 'danger');

		return $this->app->redirect($redirect);
	}
}
