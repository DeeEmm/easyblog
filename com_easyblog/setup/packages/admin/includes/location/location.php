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

require_once(dirname(__FILE__) . '/providers.php');

class EasyBlogLocation
{
	static $providers = array();

	protected $provider;

	public $providersBaseClassname = 'EasyBlogLocationProviders';

	public function __construct($provider = null)
	{
		$this->loadProvider($provider);
	}

	public function loadProvider($provider = null)
	{
		// If provider is empty, then we get it based on settings
		if (empty($provider)) {
			$provider = EB::config()->get('location_service_provider', '');
		}

		$this->provider = $this->getProvider($provider);

		return $this->provider;
	}

	/**
	 * Retrieves the location provider
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return  
	 */
	public function getProvider($provider)
	{
		if (!isset(self::$providers[$provider])) {

			$providerFile = dirname(__FILE__) . '/providers/' . $provider . '.php';

			if (!JFile::exists($providerFile)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_PROVIDER_FILE_NOT_FOUND'));
				return $fallback;
			}

			require_once($providerFile);

			$providerClassname = $this->providersBaseClassname . ucfirst($provider);

			if (!class_exists($providerClassname)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_PROVIDER_CLASS_NOT_FOUND'));
				return $fallback;
			}

			$providerClass = new $providerClassname;

			// If provider is not a extended class from abstract class, we do not want it
			if (!is_a($providerClass, $this->providersBaseClassname)) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError(JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_PROVIDER_INVALID_CLASS'));
				return $this->provider;
			}

			// Now we check if the provider constructed properly
			if ($providerClass->hasErrors()) {
				$fallback = $this->getProvider('fallback');
				$fallback->setError($providerClass->getError());
				return $fallback;
			}

			self::$providers[$provider] = $providerClass;
		}

		return self::$providers[$provider];
	}

	public function __call($method, $arguments)
	{
		if (!isset($this->provider)) {
			$this->loadFallbackProvider();
		}

		return call_user_func_array(array($this->provider, $method), $arguments);
	}
}
