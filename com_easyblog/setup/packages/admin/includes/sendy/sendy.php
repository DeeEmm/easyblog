<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogSendy
{
	public function __construct()
	{
		$config	= EB::config();

		$this->config = $config;
		$this->url = $config->get('subscription_sendy_url');
		$this->id  = $config->get('subscription_sendy_listid');
	}

	public function subscribe( $email , $firstName , $lastName = '' )
	{
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

		if (!function_exists('curl_init')) {
			return JText::_( 'COM_EASYBLOG_CURL_DOES_NOT_EXIST' );
		}

		if (!$this->config->get('subscription_sendy')) {
			return false;
		}

		if (!$this->id) {
			return false;
		}

		// Get the name
		$name = $firstName . ' ' . $lastName;

		// Prepare the url
		$url	= $this->url . '/subscribe';

		// Set the params
		$params = array('name' => $name, 'email' => $email, 'list' => $this->id, 'boolean' => 'true');

		$ch	= curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		curl_close($ch);


		return true;
	}
}
