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

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableDownload extends EasyBlogTable
{
	public $id = null;
	public $userid = null;
	public $state = null;
	public $params = null;
	public $created = null;

	/**
	 * Constructor for this class.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_download', 'id', $db);

		$this->config = EB::config();
	}


	/**
	 * Determine whether user has requested.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function exists()
	{
		if (is_null($this->id)) {
			return false;
		}

		return true;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function isNew()
	{
		return $this->state == EASYBLOG_DOWNLOAD_REQ_NEW;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function isProcessing()
	{
		return $this->state == EASYBLOG_DOWNLOAD_REQ_PROCESS;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function isReady()
	{
		return $this->state == EASYBLOG_DOWNLOAD_REQ_READY;
	}

	/**
	 * Retrieves the label for the state (used for display purposes)
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function getStateLabel()
	{
		if ($this->getState() == EASYBLOG_DOWNLOAD_REQ_READY) {
			return JText::_('COM_EB_DOWNLOAD_STATE_READY');
		}

		return JText::_('COM_EB_DOWNLOAD_STATE_PROCESSING');
	}

	/**
	 * Method used to update the request state.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function updateState($state)
	{
		$this->state = $state;

		// debug. need to uncomment.
		return $this->store();
	}

	/**
	 * Method used to set filepath.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function setFilePath($filepath)
	{
		$params = new JRegistry($this->params);
		$params->set('path', $filepath);
		$this->params = $params->toString();
	}

	/**
	 * Request state of the download. Return false if not exist.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function getState()
	{
		if (!$this->exists()) {
			return false;
		}

		return $this->state;
	}

	/**
	 * Retrieves the requester
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function getRequester()
	{
		$user = EB::user($this->userid);

		return $user;
	}

	/**
	 * Method used to send email notification to user who requested to download GDPR details.
	 * @since  5.2
	 * @access public
	 */
	public function sendNotification()
	{
		$jConfig = EB::jconfig();
		$user = JFactory::getUser($this->userid);

		$downloadLink = $this->getDownloadLink();

		$emailData['downloadLink'] = $downloadLink;
		$content = EB::notification()->getTemplateContents('gdpr.download.ready', $emailData);

		// Get the sender's name and email
		$fromEmail = $this->config->get('notification_from_email', $jConfig->get('mailfrom'));
		$fromName = $this->config->get('notification_from_name', $jConfig->get('fromname'));
		$recipient = $user->email;
		$subject = JText::_('COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_SUBJECT');

		$mailer = JFactory::getMailer();
		$mailer->sendMail($fromEmail, $fromName, $recipient, $subject, $content, true);

		return true;
	}

	/**
	 * Method to ouput the zip file to browser for download.
	 * @since  5.2
	 * @access public
	 */
	public function showArchiveDownload()
	{
		// or however you get the path
		$param = $this->getParams();
		$file = $param->get('path', '');

		if (! $file) {
			return false;
		}

		$file_name = basename($file);

		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=$file_name");
		header("Content-Length: " . filesize($file));

		readfile($file);
		exit;
	}

	/**
	 * Method generate the download link of this request
	 * @since  5.2
	 * @access public
	 */
	public function getDownloadLink()
	{
		$user = JFactory::getUser($this->userid);

		$key = $this->id . '|' . $user->id . '|' . $this->created;
		$downloadLink = EBR::getRoutedURL('index.php?option=com_easyblog&view=download&id=' . base64_encode($key), false, true);

		return $downloadLink;
	}


	/**
	 * Override parent delete method to manually delete archive file as well.
	 * @since 5.2
	 * @access public
	 */
	public function delete($pk = null)
	{
		// delete archive file if there is any.
		$param = $this->getParams();
		$file = $param->get('path', '');

		if ($file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return parent::delete($pk);
	}

}
