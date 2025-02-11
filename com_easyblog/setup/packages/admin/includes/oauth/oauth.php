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

class EasyBlogOauth extends EasyBlog
{
	/**
	 * Determines if a respective oauth client has been setup in the system
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function associated($client)
	{
		$model = EB::model('OAuth');

		$state = $model->isAssociated($client);

		return $state;
	}

	/**
	 * Determines if the provided user is associated with the respective oauth client
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isUserAssociated($client, $userId = null)
	{
		$config = EB::config();
		$allowed = $config->get('integrations_' . strtolower($client) . '_centralized_and_own');

		if (!$allowed) {
			return false;
		}

		$allowed = $config->get('integrations_' . strtolower($client));

		if (!$allowed) {
			return false;
		}

		$oauth = EB::table('OAuth');
		$exists = $oauth->loadByUser($userId, constant('EBLOG_OAUTH_' . strtoupper($client)));

		return $exists;
	}

	/**
	 * Inserts a log into the database
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function log(EasyBlogTableOAuth $oauth, EasyBlogPost $post, $status, $response)
	{
		$table = EB::table('OAuthLog');
		$table->oauth_id = $oauth->id;
		$table->post_id = $post->id;
		$table->status = $status;
		$table->response = json_encode($response);
		$table->created = JFactory::getDate()->toSql();
		
		return $table->store();
	}

	/**
	 * Retrieve the Consumer API
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getClient($type, $options = array())
	{
		static $adapters = array();

		if (!isset($loaded[$type])) {

			$type = strtolower($type);
			$file = dirname(__FILE__) . '/adapters/' . $type . '/client.php';

			require_once($file);

			$class = 'EasyBlogClient' . ucfirst($type);
			$obj = new $class($options);

			$adapters[$type] = $obj;
		}

		return $adapters[$type];
	}

	/**
	 * Notify admin on the token expiry soon.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function notifyAdminTokenExpiry($type = 'facebook')
	{
		$client = $this->getClient($type);
		$result = $client->notifyAdminTokenExpiry();

		return $result;
	}

	/**
	 * Determines if autopost enabled on the site
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function isAutopostEnabled()
	{
		$config = EB::config();

		if ($config->get('integrations_facebook') || $config->get('integrations_twitter') || $config->get('integrations_linkedin')) {
			return true;
		}

		return false;
	}

	/**
	 * Method to show Facebook oauth redirect URI for frontend and backend
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getOauthRedirectURI($type = 'facebook')
	{
		$callbackUri = array();

		if ($type == 'facebook') {
			$callbackUri[] = JURI::root() . 'index.php?option=com_easyblog&task=oauth.grant&client=facebook';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=facebook.grant&system=1';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=facebook.grant';
		}

		if ($type == 'linkedin') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=linkedin.grant&system=1';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=linkedin.grant';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=auth&type=linkedin';
		}

		if ($type == 'twitter') {
			$callbackUri[] = rtrim(JURI::root(), '/') . '/administrator/index.php';
			$callbackUri[] = rtrim(JURI::root(), '/') . '/index.php';
		}

		return $callbackUri;
	}
}

class EasyBlogOAuthConsumer
{
	public function __construct()
	{
		$this->config = EB::config();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}
}