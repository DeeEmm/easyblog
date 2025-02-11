<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyblogMailboxLibrary extends JObject
{
	// IMAP stream resource
	public $stream = null;

	// Mailbox status
	public $info = null;

	private $initiated = false;
	private $mailbox_params = '';
	private $flags = '';
	private $config = array();
	private $username = '';
	private $password = '';

	public $server = '';
	public $port = 0;
	public $mailbox_name = '';
	public $service = '';
	public $anonymous = false;
	public $secure = false;
	public $debug = false;
	public $norch = false;
	public $validate_cert = false;
	public $tls = false;
	public $readonly = false;

	public $enabled = false;
	public $subject_prefix = '';
	public $check_interval = '';

	/**
	 * Initialize method
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function init()
	{
		$this->config = EB::config();

		$this->enabled = $this->config->get('main_remotepublishing_mailbox');
		$this->server = $this->config->get('main_remotepublishing_mailbox_remotesystemname');
		$this->port = $this->config->get('main_remotepublishing_mailbox_port');
		$this->mailbox_name = $this->config->get('main_remotepublishing_mailbox_mailboxname');
		$this->service = $this->config->get('main_remotepublishing_mailbox_service');
		$this->username = $this->config->get('main_remotepublishing_mailbox_username');
		$this->password = $this->config->get('main_remotepublishing_mailbox_password');
		$this->ssl = $this->config->get('main_remotepublishing_mailbox_ssl');
		$this->subject_prefix = $this->config->get('main_remotepublishing_mailbox_prefix');
		$this->check_interval = $this->config->get('main_remotepublishing_mailbox_run_interval');
		$this->validate_cert = $this->config->get('main_remotepublishing_mailbox_validate_cert');

		$this->flags = '';
		$this->flags .= $this->service ? '/'.$this->service : '';
		$this->flags .= $this->ssl ? '/ssl' : '';
		$this->flags .= $this->debug ? '/debug' : '';
		$this->flags .= $this->norch ? '/norch' : '';
		$this->flags .= $this->validate_cert ? '' : '/novalidate-cert';
		$this->flags .= $this->readonly ? '/readonly' : '';

		$this->mailbox_params = '{'. $this->server . ':' . $this->port . $this->flags .'}' . $this->mailbox_name;

		$this->initiated = true;
	}

	/**
	 * Open an IMAP stream to a mailbox.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function connect()
	{
		if (!$this->initiated) {
			$this->init();
		}

		if (!$this->enabled || !function_exists('imap_open') || !function_exists('imap_fetchheader') || !function_exists('imap_body')) {
			$this->setError('PHP IMAP not available.');
			return false;
		}

		// Connect to mailbox
		$this->stream = @imap_open($this->mailbox_params, $this->username, $this->password);

		if ($this->stream === false) {
			$this->setError('Remote connect failed.');
			return false;
		}

		return true;
	}

	/**
	 * Test the connection to remote server
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function testConnect($server, $port, $service, $ssl, $mailbox, $user, $pass)
	{
		$flags = '';
		$flags = $service ? $flags . '/' . $service : $flags;
		$flags = $ssl ? $flags . '/ssl' : $flags;
		$flags = true ? $flags . '/novalidate-cert' : $flags;

		// imap library is not compiled
		if (!function_exists('imap_open') || !function_exists('imap_fetchheader') || !function_exists('imap_body')) {
			$result	= '<span style="color:red;">Failed, imap is not compiled with PHP</span>';
			return $result;
		}

		// note: pop3 doesn't support OP_HALFOPEN
		$stream	= imap_open('{'.$server.':'.$port.$flags.'}', $user, $pass);
		$result	= imap_errors();

		if ($stream === false) {

			if (is_array($result)) {
				$result	= $result[0];
			}

			if ($result === false) {
				$result = 'Failed';
			}
		} else {
			$result	= '<span style="color:green">Success</span>';
			imap_close($stream);
		}

		return $result;
	}

	public function getStream()
	{
		return $this->stream;
	}

	public function getErrors()
	{
		$errors	= array_merge((array)$this->_errors, (array)imap_errors());
		$errors = !empty($errors) ? $errors : '';

		return $errors;
	}

	public function disconnect()
	{
		if (!$this->stream || !is_resource($this->stream)) {
			return false;
		}

		imap_expunge($this->stream);

		$errors	= $this->getErrors();

		if (!empty($errors)) {
			//print_r($errors);
		}

		imap_close($this->stream);
	}

	public function searchMessages($criteria)
	{
		return imap_search($this->stream, $criteria);
	}

	public function getMessageInfo($sequence)
	{
		$headers = imap_headerinfo($this->stream, $sequence);

		if (empty($headers)) {
			return false;
		}

		// Decode headers
		foreach ($headers as $key => $value) {
			
			if (!is_array($value)) {
				$data = imap_mime_header_decode($value);

				$value = '';

				// Iterate each header 
				foreach ($data as $header) {
					$header->charset = strtoupper($header->charset);

					if ($header->charset != 'DEFAULT' && $header->charset != 'UTF-8') {
						$header->text = iconv($header->charset, 'UTF-8', $header->text);
						$header->subject = iconv($header->charset, 'UTF-8', $header->subject);
					}

					$value .= $header->text;
				}

				$headers->$key = $value;
			}
		}

		$from = $headers->fromaddress;

		if (!$from) {
			$from = $header->senderaddress;
		}

		if (!$from) {
			$from = $header->reply_toaddress;
		}

		$pattern = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';
		preg_match($pattern, $from, $matches);
		$from = isset($matches[0]) ? $matches[0] : '';

		if (!$from) {
			$from = $header->from[0]->mailbox . '@' . $header->from[0]->host;
		}

		if (!$from) {
			$from = $header->sender[0]->mailbox . '@' . $header->sender[0]->host;
		}

		if (!$from) {
			$from = $header->reply_to[0]->mailbox . '@' . $header->reply_to[0]->host;
		}

		$headers->fromemail	= $from;

		return $headers;
	}

	/**
	 * Get the number of messages in the current mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMessageCount()
	{
		return imap_num_msg($this->stream);
	}

	/**
	 * Get information about the current mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getInfo()
	{
		// note: imap_mailboxmsginfo not quite support pop3
		if ($this->service == 'imap') {
			$this->info	= imap_mailboxmsginfo($this->stream);
		} else {
			$this->info	= imap_status($this->stream, $this->mailbox_params, $option);
		}

		return $this->info;
	}

	public function getCount($label)
	{
		if (!$this->info) {
			$this->getInfo();
		}

		$label	= strtolower($label);
		$count	= 0;

		switch ($label) {
			case 'unread':
				$count	= ($this->service == 'imap') ? $this->info->Unread : $this->info->unseen;
				break;
			case 'recent':
				$count	= ($this->service == 'imap') ? $this->info->Recent : $this->info->recent;
				break;
			case 'deleted':
				$count	= ($this->service == 'imap') ? $this->info->Deleted : false;
				break;
			case 'size':
				$count	= ($this->service == 'imap') ? $this->info->Size : false;
				break;
			default:
				break;
		}

		return $count;
	}

	/**
	 * Causes a store to add the specified flag to the flags set for the messages in the specified sequence.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function setMessageFlag($sequence, $flag)
	{
		return imap_setflag_full($this->stream, $sequence, $flag);
	}

	/**
	 * Clears flags on messages
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function clearMessageFlag($sequence, $flag, $options = 0)
	{
		return imap_clearflag_full($this->stream, $sequence, $flag, $options);
	}

	/**
	 * Create a new mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createMailbox($mailbox)
	{
		return imap_createmailbox($this->stream, $mailbox);
	}

	/**
	 * Mark a message for deletion from current mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteMailbox($mailbox)
	{
		return imap_deletemailbox($this->stream, $mailbox);
	}

	/**
	 * Mark a message for deletion from current mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteMessage($sequence, $options = 0)
	{
		// Only for POP3
		// Mark email to be deleted.
		$state = imap_delete($this->stream, $sequence);

		if ($state) {
			// Delete the emails that are marked
			imap_expunge($this->stream);
		}

		return $state;
	}	

	/**
	 * Move specified messages to a mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function moveMessage($msglist, $mailbox)
	{
		return imap_mail_move($this->stream, $msglist, $mailbox);
	}

	/**
	 * Send an email message
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function sendMessage($to, $subject, $message)
	{
		return imap_mail($to, $subject, $message);
	}

	/**
	 * Subscribe to a mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function subscribe($mailbox)
	{
		return imap_subscribe($this->stream, $mailbox);
	}

	/**
	 * Unsubscribe from a mailbox
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unsubscribe($mailbox)
	{
		return imap_unsubscribe($this->stream, $mailbox);
	}
}

/**
 * PHP IMAP Class for Mailbox Messages
 */
class EasyBlogMailboxMessage extends JObject
{
	protected $stream		= null;
	protected $sequence		= 0;
	protected $structure	= null;
	protected $body			= null;
	protected $plain_data	= '';
	protected $html_data	= '';
	protected $parameters	= array();
	protected $attachment	= array();

	public function __construct($stream, $sequence)
	{
		$this->stream	= $stream;
		$this->sequence	= $sequence;

		return parent::__construct();
	}

	/**
	 * Retrieves the message data
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getMessage()
	{
		// Get the structure of the message
		$structure = $this->fetchStructure();

		// dump($structure);

		// Something went wrong here
		if (!$structure) {
			return false;
		}

		// There should be identification of the parts here
		if (!isset($this->structure->parts)) {
			// lets further check if this structure->type is a text type or not. if yes, let manually create the parts.
			if ($this->structure->type == '0') {
				$this->structure->parts[] = clone $structure;
			} else {
				return false;
			}
		}

		// Count and see if it's multipart message
		$count = count($this->structure->parts);

		if ($count > 0) {

			for ($i=0; $i<$count; $i++) {
				$section = $i + 1;
				$this->getParts($this->structure->parts[$i], $section);
			}

		} else {
			$this->getParts($this->structure);
		}

		return true;
	}

	/**
	 * Fetches the structure of the message
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function fetchStructure()
	{
		$this->structure = @imap_fetchstructure($this->stream, $this->sequence);

		return $this->structure;
	}

	private function fetchBody($section)
	{
		if ($section) {
			$data	= @imap_fetchbody($this->stream, $this->sequence, $section);
		} else {
			$data	= @imap_body($this->stream, $this->sequence);
		}

		return $data;
	}

	/**
	 * Retrieve a part from the mail message
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function getParts($part, $section = 0)
	{
		$data = $this->fetchBody($section);

		// Extract the data
		$this->extractPart($part, $data);

		// Sub parts
		if (!empty($part->parts)) {

			foreach ($part->parts as $index => $subpart) {
				$this->getParts($subpart, $section.'.'.($index+1));
			}
		}

		return;
	}

	/**
	 * Extract parts of a message
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function extractPart($part, $data)
	{
		switch ($part->encoding) {
			case '3':
				// base 64
				$data = base64_decode($data);
				break;
			case '4':
				// quoted-printable
				$data = quoted_printable_decode($data);
				break;
			case '5': // other
			case '0': // 7bit
			case '1': // 8 bit
			case '2': // binary
			default:
				break;
		}

		// Get the params
		$params = EasyBlogMailboxMessage::getformatedParams($part);

		$encoding = 'UTF-8';

		if (isset($params['charset'])) {
			$encoding = $params['charset'];
		}

		$type = $part->type;
		$subtype = strtolower($part->subtype);

		// Get the part id
		$id = isset($part->id) ? $part->id : '';

		// Plain text messages
		if ($type == 0 && $subtype == 'plain') {
			$this->plain_data .= EasyblogMailboxMessage::stringToUTF8($encoding, trim($data));
		}

		// Html messages
		if ($type == 0 && $subtype == 'html') {
			$this->html_data .= EasyblogMailboxMessage::stringToUTF8($encoding, trim($data));
		}

		// What type is this?
		if ($type == 2) {
			$this->plain_data .= EasyblogMailboxMessage::stringToUTF8($encoding, trim($data));
		}

		// PDF files
		if ($type == 3 && $subtype == 'pdf') {
			$file = [];
			$file['mime'] = $subtype;
			$file['data'] = $data;
			$file['name'] = isset($params['name']) ? $params['name'] : $params['filename'];
			$file['id'] = $id;
			$file['size'] = $part->bytes;

			$this->attachment[] = $file;
		}

		// Image parts
		if ($type == 5) {
			$image = [];
			$image['mime'] = isset($params['mime']) ? $params['mime'] : $subtype; // GIF
			$image['type'] = isset($params['type']) ? $params['type'] : 'image/' . $subtype;
			$image['data'] = $data; // binary
			$image['name'] = isset($params['name']) ? $params['name'] : $params['filename']; // 35D.gif
			$image['id'] = $id; // <35D@goomoji.gmail>
			$image['size'] = isset($params['size']) ? $params['size'] : $part->bytes;

			$this->attachment[] = $image;
		}

		return;
	}

	private static function getformatedParams($part)
	{
		$parameters	= [];

		if (!$part->parameters) {
			return $parameters;
		}

		if ($part->ifparameters) {
			foreach ($part->parameters as $param) {
				$parameters[strtolower($param->attribute)] = $param->value;
			}
		}
		if ($part->ifdparameters) {
			foreach ($part->dparameters as $param) {
				$parameters[strtolower($param->attribute)] = $param->value;
			}
		}

		return $parameters;
	}

	private static function stringToUTF8($in_charset, $string)
	{
		if (function_exists('iconv')) {
			return iconv($in_charset, 'UTF-8', $string);
		}

		return $string;
	}

	public function getHTML()
	{
		return $this->html_data;
	}

	public function getPlain()
	{
		return $this->plain_data;
	}

	public function getAttachment()
	{
		return $this->attachment;
	}
}
