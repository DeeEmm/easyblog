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

require_once(__DIR__ . '/adapter.php');

class EasyBlogMailboxAdapterPost extends EasyBlogMailboxAdapter
{
	/**
	 * Check for requirements
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function checkRequirements()
	{
		// Check if this is enabled
		if (!$this->config->get('main_remotepublishing_mailbox')) {

			$this->setError('Mailbox publishing is not enabled.');
			return false;
		}

		if ($this->config->get('main_remotepublishing_mailbox_userid') == 0 && !$this->config->get('main_remotepublishing_mailbox_syncuser')) {
			$this->setError('Unspecified default user id for remote publishing.');
			return false;
		}

		if (!$this->config->get('main_remotepublishing_mailbox_categoryid')) {

			$this->setError('No default category selected for remote publishing.');
			return false;
		}

		return true;
	}

	/**
	 * Set the next run time for remote publishing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function setNextRunTime()
	{
		$interval = (int) $this->config->get('main_remotepublishing_mailbox_run_interval');
		$nextrun = EB::date('+' . $interval . ' minutes');

		// Save the next run time
		// use $configTable to avoid variable name conflict
		$table = EB::table('configs');
		$table->load('config');

		$params = new JRegistry($table->params);
		$params->set('main_remotepublishing_mailbox_next_run', $nextrun->toUnix());

		$table->params = $params->toString('ini');

		$table->store();
	}

	/**
	 * Imports blog posts from a specific email address
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function execute()
	{
		// Check if the requirements are set
		$state = $this->checkRequirements();

		if (!$state) {
			return $state;
		}

		// Get the interval for fetching items
		$nextrun  = (int) $this->config->get('main_remotepublishing_mailbox_next_run');
		$nextrun  = EB::date($nextrun)->toUnix();
		$now = EB::date()->toUnix();

		// Ensure that the processing time is not elapsing
		if ($nextrun !== 0 && $now < $nextrun && !$this->debug) {

			$time = EB::date($nextrun)->format(JText::_('DATE_FORMAT_LC3'));
			// $this->setError('Email service interval is not up yet. Next service is at ' . $time);
			return EB::exception('Email service interval is not up yet. Next service is at ' . $time, EASYBLOG_MSG_INFO);
		}

		// Set the next run time
		$this->setNextRunTime();

		// Get the mailbox lib
		$this->getMailbox();

		// Get total emails
		$total = $this->mailbox->getMessageCount();

		// Check if there are any emails
		if ($total < 1) {
			$this->mailbox->disconnect();
			// TODO: Language
			return EB::exception('No emails found in mailbox. Skipping this.', EASYBLOG_MSG_INFO);
		}

		// Determines if we should fetch emails by specific title
		$criteria = $this->config->get('main_remotepublishing_mailbox_prefix');

		// Search for messages
		$list = $this->search($criteria);

		// Go through each items and import them now
		$result = array();
		$result['success'] = array();
		$result['error'] = array();

		if ($list === false) {
			// TODO: Language
			return EB::exception('No emails found in mailbox. Skipping this.', EASYBLOG_MSG_INFO);
		}

		$total = 0;

		foreach ($list as $index) {
			$state = $this->import($index);

			if ($state === true) {
				$total += 1;
			}
		}

		// Disconnect the mailbox when we are done
		$this->mailbox->disconnect();

		if ($total > 0) {
			return EB::exception(JText::sprintf('%1$s emails fetched from mailbox.', $total), EASYBLOG_MSG_INFO);
		}

		return EB::exception(JText::_('No emails found in mailbox.'), EASYBLOG_MSG_INFO);
	}

	public function import($index)
	{
		// Get the data from the mailbox
		$data = $this->mailbox->getMessageInfo($index);

		if ($data === false) {
			return JText::_('Unable to get message data for index <b>' . $index . '</b>');
		}

		// Get the properties from the mail
		$uid = $data->message_id;
		$udate = $data->udate;
		$size = $data->Size;
		$mailDate = $data->MailDate;

		// Try to get the sender data
		$from = $this->getSender($data);
		$authorId = 0;

		// Map the sender's email to the user on this site.
		if ($this->config->get('main_remotepublishing_mailbox_syncuser')) {

			$model = EB::model('Users');

			// Get the user's id
			$authorId = $model->getUserIdByEmail($from);

			// if no user found. lets use the user id specified in 'Associate Posts With User' setting. #2039
			if (!$authorId) {
				$authorId = $this->config->get('main_remotepublishing_mailbox_userid');
			}

			if (!$authorId) {
				return JText::sprintf('Unable to detect the user based on the email <b>%1$s</b>.', $from);
			}

		} else {
			$authorId = $this->config->get('main_remotepublishing_mailbox_userid');

			if (!$authorId) {
				return JText::sprintf('Invalid configured user for mapping blog post into user <b>%1$s</b>.', $authorId);
			}
		}

		// Ensure that the user has privilege to submit new blog posts
		$acl = EB::acl($authorId);

		if (!$acl->get('add_entry')) {
			return JText::sprintf('User <b>%1$s</b> does not have permissions to post new blog post.', $authorId);
		}

		// Get the author
		$author = JFactory::getUser($authorId);

		// Get the subject of the email
		$subject = EBString::str_ireplace($this->config->get('main_remotepublishing_mailbox_prefix'), '', $data->subject);
		$filter  = JFilterInput::getInstance();
		$subject = $filter->clean($subject, 'string');

		if (!$subject) {
			$subject = JText::sprintf('COM_EASYBLOG_REMOTE_PUBLISHING_DEFAULT_EMAIL', EB::date()->toSql());
		}

		// Test if the sender email address is allowed
		if (!$this->isSenderAllowed($from)) {
			return false;
		}

		// Get the mail contents now
		$message = $this->getMessage($index);

		// Get the contents
		$contents = $this->getMessageContents($message, $this->config->get('main_remotepublishing_mailbox_format'));

		// Set the content target
		$contentTarget = $this->config->get('main_remotepublishing_mailbox_type');

		// Bind our result to the post lib
		$data = array();
		$post = EB::post(null, $author->id);
		$post->create(array('overrideDoctType' => EASYBLOG_POST_DOCTYPE_LEGACY));

		// now let get the uid
		$data['uid'] = $post->uid;
		$data['revision_id'] = $post->revision->id;

		$data['title'] = $subject;
		$data['posttype'] = EBLOG_MICROBLOG_EMAIL;
		$data['created_by'] = $author->id;
		$data['created'] = EB::date()->toSql();
		$data['modified'] = EB::date()->toSql();
		$data['publish_up'] = EB::date()->toSql();

		//always set as siteside item
		$data['source_id'] = 0;
		$data['source_type'] = EASYBLOG_POST_SOURCE_SITEWIDE;

		// Set the privacy status for this post
		$data['access'] = $this->config->get('main_remotepublishing_mailbox_privacy');

		// Set the publish status
		$data['published'] = $this->config->get('main_remotepublishing_mailbox_publish');

		// If the user does not have privilege to publish post, we shouldn't allow the post to be published
		if (!$acl->get('publish_entry')) {
			$data['published'] = EASYBLOG_POST_PENDING;
		}

		// Set the frontpage status
		$data['frontpage'] = $this->config->get('main_remotepublishing_mailbox_frontpage');

		// Determines if notification should be sent
		$data['send_notification_emails'] = $this->config->get('main_remotepublishing_mailbox_publish');

		// Set the primary category for this post
		$data['category_id'] = $this->config->get('main_remotepublishing_mailbox_categoryid');
		$data['categories'] = array($this->config->get('main_remotepublishing_mailbox_categoryid'));

		// we need to set this as legacy post as the post did not go through composer.
		// $data['doctype'] = EASYBLOG_POST_DOCTYPE_LEGACY;

		// lets do the binding here 1st. the rest we will manually assign.
		$post->bind($data, array());

		// Process attachments for this mail
		$attachments = array();

		if ($this->config->get('main_remotepublishing_mailbox_image_attachment')) {
			$attachments = $this->getMessageAttachments($message, $post, $contents->body, $authorId);
		}

		// After processing everything, assign the contents
		$post->{$contentTarget} = $contents->body;

		// Try to save the blog post now
		// Save the post now
		try {

			$saveOptions = array(
							'applyDateOffset' => false,
							'validateData' => true,
							'useAuthorAsRevisionOwner' => true
							);

			$post->save($saveOptions);

		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return $exception;
		}

		// Delete the message once completed
		$this->markAsRead($index);

		return true;
	}
}
