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

class EasyBlogMailer extends EasyBlog
{
	/**
	 * Dispatches pending emails out
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function dispatch($limit = null)
	{
		$jConfig = EB::jConfig();
		$isJoomlaMailerEnabled = $jConfig->get('mailonline', 0);

		// Skip this if the Joomla mailer is turned off
		if (!$isJoomlaMailerEnabled) {
			return;
		}

		if (is_null($limit)) {
			$limit = (int) $this->config->get('main_mail_total');
		}

		$model = EB::model('Mailer');
		$model->cleanup();

		// Get pending emails
		$result = $model->getPendingEmails($limit);

		if (!$result) {
			return EB::exception('There are no pending emails that needs to be dispatched currently.', EASYBLOG_MSG_INFO);
		}


		foreach ($result as $row) {

			$table = EB::table('MailQueue');
			$table->load($row->id);

			$table->status = true;
			$table->store();

			$mailer = JFactory::getMailer();
			$mailer->sendMail($table->mailfrom, $table->fromname, $table->recipient, $table->subject, $table->getBody(), true);
		}

		return EB::exception(JText::sprintf('Processed and sent %1$s emails', count($result)), EASYBLOG_MSG_INFO);
	}

}
