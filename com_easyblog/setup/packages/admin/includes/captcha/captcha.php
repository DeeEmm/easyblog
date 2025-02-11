<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCaptcha extends EasyBlog
{
	protected $adapter = null;

	public function __construct($uid = null, $userId = null)
	{
		parent::__construct();

		// default to false
		$this->adapter = false;

		if ($this->config->get('comment_captcha_type') == 'none') {
			$this->adapter = false;
		}

		// Test if captcha should be enabled for registered users.
		if (!$this->config->get('comment_captcha_registered') && $this->my->id > 0) {
			$this->adapter = false;
		} else {

			if ($this->config->get('comment_captcha_type') == 'recaptcha' && $this->config->get('comment_recaptcha_public')) {
				$this->adapter = $this->getAdapter('recaptcha');
			} else if ($this->config->get('comment_captcha_type') == 'builtin') {
				$this->adapter = $this->getAdapter('captcha');
			}
		}

	}

	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function getHTML()
	{
		if ($this->adapter === false) {
			return false;
		}

		return $this->adapter->getHTML();
	}

	/**
	 * Retrieves the captcha adapter
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function getAdapter($type)
	{
		$folder	= dirname(__FILE__) . '/adapters';

		$file = $folder . '/' . strtolower($type) . '.php';

		require_once($file);

		$className = 'EasyBlogCaptchaAdapter' . ucfirst($type);

		$obj = new $className();

		return $obj;
	}

	/**
	 * Verifies the captcha codes
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function verify()
	{
		// if captcha disabled, just return true.
		if ($this->adapter === false) {
			return true;
		}

		$className = get_class($this->adapter);

		// Check if recaptcha is used
		if (strpos(strtolower($className), 'recaptcha') !== false) {

			$response = $this->input->get('recaptcha', '', 'default');
			$ip = @$_SERVER['REMOTE_ADDR'];

			$valid = $this->adapter->verifyResponse($ip, $response);

			return $valid;
		}

		// If recaptcha is not enabled, we assume that the built in captcha is used.
		$response = $this->input->get('captcha-response', '', 'default');
		$id = $this->input->get('captcha-id', '', 'default');

		return $this->adapter->verify($response, $id);
	}

	/**
	 * Retrieve recaptcha language from json file
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getRecaptchaLanguages()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/recaptcha.json';

		$contents = file_get_contents($file);
		$languages = json_decode($contents);

		return $languages;
	}

}
