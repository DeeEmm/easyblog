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

class EasyBlogException extends Exception
{
	public $type;
	public $html = '';

	private static $exceptions =array();

	private static $codeMap = array(
		EASYBLOG_MSG_ERROR   => 400,
		EASYBLOG_MSG_SUCCESS => 200,
		EASYBLOG_MSG_WARNING => 200,
		EASYBLOG_MSG_INFO    => 200
	);

	private static $messageMap = array(
		EASYBLOG_MSG_ERROR   => 'error',
		EASYBLOG_MSG_SUCCESS => 'message',
		EASYBLOG_MSG_WARNING => 'warning',
		EASYBLOG_MSG_INFO    => 'notice'
	);

	public $customErrorCode;

	public function __construct($message, $type = EASYBLOG_MSG_ERROR, $silent = false, $customErrorCode = null)
	{
		// For 404 errors, we use simple warnings by Joomla
		if ($type === 404 && FH::isJoomla31()) {
			return JError::raiseError(404, JText::_($message));
		}

		// EASYBLOG_MSG_ERROR
		if (is_string($type)) {
			$code = isset(self::$codeMap[$type]) ? self::$codeMap[$type] : null;
			$this->type = $type;
		}

		// array(400, EASYBLOG_MSG_ERROR)
		if (is_array($type)) {
			$code = $type[0];
			$this->type = $code[1];

		}

		if (is_int($type)) {
			$code = $type;
			$this->type = $type;
		}

		$previous = null;

		// We're riding the third param. Blame strict standards.
		if (!is_bool($silent)) {
			$silent = false;
			$previous = $silent;
		}

		$this->customErrorCode = $customErrorCode;

		EB::loadLanguages(JPATH_ADMINISTRATOR);
		EB::loadLanguages(JPATH_ROOT);

		// Translate message so a user can pass in the language string directly.
		$message = JText::_($message);

		// Construct exception so we can retrieve the rest of the properties
		parent::__construct($message, $code, $previous);

		// Add to our global list of exceptions
		self::$exceptions[] = $this;
	}

	public static function getAllExceptions()
	{
		return self::$exceptions;
	}

	public function toArray()
	{
		$data = array();
		$data['type'] = $this->type;
		$data['message'] = $this->getMessage();
		$data['code'] = $this->getCode();
		$data['html'] = $this->html;
		$data['customCode'] = $this->customErrorCode;

		// if (EB::configuration()->environment=='development') {
		// 	$data['file'] = $this->getFile();
		// 	$data['line'] = $this->getLine();
		// 	$data['trace'] = $this->getTrace();
		// }

		return $data;
	}

	public function toJSON()
	{
		return json_encode($this->toArray());
	}
}

class EasyBlogUploadException extends EasyBlogException
{
	public function __construct($code=null, $type=EASYBLOG_MSG_ERROR, $previous=null) {

		$code = $file['error'];

		switch ($code) {

			case UPLOAD_ERR_INI_SIZE:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_INI_SIZE';
				break;

			case UPLOAD_ERR_FORM_SIZE:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_FORM_SIZE';
				break;

			case UPLOAD_ERR_PARTIAL:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_PARTIAL';
				break;

			case UPLOAD_ERR_NO_FILE:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_NO_FILE';
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_NO_TMP_FILE';
				break;

			case UPLOAD_ERR_CANT_WRITE:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_CANT_WRITE';
				break;

			case UPLOAD_ERR_EXTENSION:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_EXTENSION';
				break;

			default:
				$message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_UNKNOWN';
				break;
		}

	    parent::__construct($message, $type, $previous);
	}
}
