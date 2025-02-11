<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/lib.php');

class EasyBlogMailboxAdapter extends JObject
{
	public function __construct()
	{
		$this->config = EB::config();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();

		// Determines if the current request is for debug
		$this->debug = $this->input->get('debug', false, 'bool');
	}

	/**
	 * Retrieves the mailbox object
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getMailbox()
	{
		$this->mailbox = new EasyblogMailboxLibrary();
		$state = $this->mailbox->connect();

		if (!$state) {
			$this->mailbox->disconnect();

			$this->setError('Unable to connect to the mailbox');
			return false;
		}

		return $this->mailbox;
	}

	/**
	 * Searches the mailbox for specific criteria on the title
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function search($criteria = '')
	{
		$searchCriteria = 'UNSEEN';

		if ($criteria) {
			$searchCriteria .= ' SUBJECT "' . $criteria . '"';
		}

		$list = $this->mailbox->searchMessages($searchCriteria);

		// No emails found
		if ($list === false) {
			$this->mailbox->disconnect();

			$this->setError('No matching mails found.');
			return false;
		}

		// There's not limit function for imap, so we work around with the array
		// Get the oldest message first
		$limit = $this->config->get('main_remotepublishing_mailbox_fetch_limit');
		sort($list);

		$list = array_slice($list, 0, $limit);

		return $list;
	}

	/**
	 * Retrieve the sender details
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getSender($data)
	{
		$from = '';

		if (isset($data->from)) {
			$info = $data->from[0];

			if (!empty($info->mailbox) && !empty($info->host)) {
				$from = $info->mailbox . '@' . $info->host;
			}
		}

		if (!$from) {
			$from = $data->fromemail;
		}

		return $from;
	}

	/**
	 * Retrieves the message data
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getMessage($index)
	{
		$message = new EasyBlogMailboxMessage($this->mailbox->stream, $index);
		$message->getMessage();

		return $message;
	}

	/**
	 * Mapping services
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function map($type, $file, EasyBlogPost &$blog, &$contents, &$authorId, $index)
	{
		$lib = __DIR__ . '/maps/' . strtolower($type) . '.php';

		include_once($lib);

		$class = 'EasyBlogMailboxMap' . ucfirst($type);

		$mapper = new $class($blog, $authorId);
		$mapper->map($file, $index, $contents);
	}

	/**
	 * Processes attachments in an email
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getMessageAttachments(EasyBlogMailboxMessage $message, EasyBlogPost &$blog, &$contents, $authorId = null)
	{
		$result = array();
		$files = $message->getAttachment();

		if (!$files) {
			return $result;
		}

		$extensions = ['jpg', 'png', 'gif', 'jpeg'];
		$index = 0;

		foreach ($files as $file) {
			$mime = $file['mime'];

			if ($mime === '*') {
				$mime = JFile::getExt($file['name']);
			}

			// Clean up file names that contains '/' in the name.
			if (strpos($file['name'], '/') !== false) {
				$file['name'] = substr($file['name'], strrpos($file['name'], '/') + 1);
			}

			// Clean up file names that contains '\' in the file name.
			if (strpos($file['name'], '\\') !== false) {
				$file['name'] = substr($file['name'], strrpos($file['name'], '\\') + 1);
			}

			// PDF files
			if ($mime == 'pdf') {
				$this->map('pdf', $file, $blog, $contents, $authorId, $index);
			}

			if (in_array($mime, $extensions)) {
				$this->map('image', $file, $blog, $contents, $authorId, $index);
			}

			$index++;
		}
	}

	/**
	 * Formats the content from the email to a normal blog content
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getMessageContents(EasyBlogMailboxMessage $message, $format = 'html')
	{
		$result = new stdClass();

		$result->html = $message->getHTML();
		$result->plain = nl2br($message->getPlain());

		// If plain text is empty, just fall back to html
		if (empty($result->plain)) {
			$result->body = nl2br(strip_tags($result->html));
		}

		$result->body = $format == 'html' ? $result->html : $result->plain;

		// If we can't get any result, just use the plain text
		$result->body = $result->body ? $result->body : $result->plain;

		// strip javascript script tag
		$result->body = EB::truncater()->strip_only($result->body, '<script>', true);

		// Filter the contents to avoid any unecessary data
		$filter = JFilterInput::getInstance([], [], 1, 1);

		// JFilterInput doesn't strip css tags
		$result->body = preg_replace("'<style[^>]*>.*?</style>'si", '', $result->body);

		// Clean up the input
		$result->body = $filter->clean($result->body, 'html');

		$result->body = EBString::trim($result->body);

		// Tidup content so that does not contain unclosed html codes
		$result->body = EB::string()->tidyHTMLContent($result->body);

		return $result;
	}

	public function markAsRead($index)
	{
		if ($this->mailbox->service == 'pop3') {
			$this->mailbox->deleteMessage($index);
		}

		if ($this->mailbox->service == 'imap') {
			$this->mailbox->setMessageFlag($index, '\Seen');
		}

		return true;
	}

	/**
	 * Filter senders
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isSenderAllowed($sender)
	{
		// filter email according to the whitelist
		$filter = JFilterInput::getInstance();
		$whitelist = $this->config->get('main_remotepublishing_mailbox_from_whitelist');
		$whitelist = $filter->clean($whitelist, 'string');
		$whitelist = trim($whitelist);

		// No whitelist address set
		if (empty($whitelist)) {
			return true;
		}

		$pattern = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';

		preg_match_all($pattern, $whitelist, $matches);
		$emails = $matches[0];

		if (!in_array($sender, $emails)) {
			$this->setError(JText::sprintf('Message sender <b>%1$s</b> is not in the whitelist', $sender));

			return false;
		}

		return true;
	}
}
