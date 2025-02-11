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

require_once(__DIR__ . '/consumer.php');

class EasyBlogClientFacebook extends EasyBlogFacebookConsumer
{
	public $callback 	= '';
	public $token 		= '';
	public $apiKey 		= '';
	public $apiSecret 	= '';

	public $jConfig = null;
	public $config = null;
	public $app = null;
	public $input = null;
	public $redirect = null;

	public function __construct($options = array())
	{
		$this->jConfig = EB::jConfig();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->config = EB::config();
		$this->apiKey = $this->config->get('integrations_facebook_api_key');
		$this->apiSecret = $this->config->get('integrations_facebook_secret_key');

		// Default redirection url
		$this->redirect = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&task=facebook.grant';

		// Determines if there's a "system" in the url
		$system = $this->input->get('system', false, 'bool');

		if ($system) {
			$this->redirect .= '&system=1';
		}

		// #1087 no longer need this userId if sign in via Facebook from backend,
		// because it will cause that facebook oauth redirect URI error not allow to have dynamic parameter id

		// Determines if there's a "userId" in the url
		// $userId = $this->input->get('userId', null, 'default');

		// if ($userId) {
		// 	$this->redirect .= '&userId=' . $userId;
		// }

		parent::__construct(array('appId' => $this->apiKey, 'secret' => $this->apiSecret));
	}

	public function setCallback($url)
	{
		$this->redirect = $url;
	}

	/**
	 * Facebook does not need the request tokens
	 *
	 * @since 	5.0
	 * @access	public
	 **/
	public function getRequestToken()
	{
		$obj = new stdClass();
		$obj->token = 'facebook';
		$obj->secret = 'facebook';

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier	= $this->input->get( 'code' , '' );
		return $verifier;
	}

	/**
	 * Retrieves the revoke access button
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRevokeButton($return, $system = false, $userId = false)
	{
		$theme = EB::themes();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=facebook.revoke';

		if ($system) {
			$url .= '&system=1';
		}

		$url .= '&return=' . base64_encode($return);

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);

		$output = $theme->output('admin/oauth/facebook/revoke');

		return $output;
	}

	/**
	 * Retrieves the loggin button for Facebook
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLoginButton($return, $system = false, $userId = false)
	{
		$theme = EB::themes();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=facebook.facebookAuthorize';

		if ($system) {
			$url .= '&system=1';
		}

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);
		$theme->set('return', $return);

		$output = $theme->output('admin/oauth/facebook/button');

		return $output;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	5.2.7
	 * @access	public
	 */
	public function getAuthorizeURL()
	{
		$scopes = $this->config->get('integrations_facebook_scope_permissions');

		$redirect = $this->redirect;
		$redirect = urlencode($redirect);

		$url = 'https://facebook.com/dialog/oauth?scope=' . $scopes . '&client_id=' . $this->apiKey . '&redirect_uri=' . $redirect . '&display=popup';

		return $url;
	}

	/**
	 * Javascript to close dialog when call=doneLogin is specified in the URI.
	 *
	 * @access	public
	 */
	public function doneLogin()
	{
		ob_start();
	?>
		<script type="text/javascript">
		window.opener.doneLogin();
		window.close();
		</script>
	<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;

		exit;
	}

	/**
	 * Exchanges the code with Facebook to get the access token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function exchangeToken($code)
	{
		$params = array( 'client_id' 	=> $this->apiKey,
						 'redirect_uri'	=> $this->redirect,
						 'client_secret'=> $this->apiSecret,
						 'code'			=> $code
						);

		$token = parent::_oauthRequest(parent::getUrl('graph', '/oauth/access_token'), $params);

		$token = json_decode($token);

		if (!isset($token->access_token)) {
			return false;
		}

		$date = EB::date($token->expires_in);

		// Get current date
		$currentDate = EB::date()->toUnix();

		// Add expiry date from current date
		$expires = $currentDate + $token->expires_in;

		$expires = EB::date($expires)->toSql();

		$obj = new stdClass();
		$obj->token = $token->access_token;
		$obj->expires = $expires;

		return $obj;
	}

	/**
	 * Retrieve the extracted content of a blog post that can be formatted to Facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function extractPostData(EasyBlogPost &$post)
	{
		// Prepare the result data
		$data = new stdClass();

		// Get the content's source
		$source = $this->config->get('integrations_facebook_source');

		// Get the blog's image to be pushed to Facebook
		$data->image = $post->getImage('large', false , true);

		// If there's no blog image, try to get the image from the content
		if (!$data->image) {

			// lets get full content.
			$fullcontent = $post->getContent('entry');
			$data->image = EB::string()->getImage($fullcontent);
		}

		// If there's still no image, use author's avatar
		if (!$data->image && $this->config->get('main_facebook_opengraph_imageavatar', false)) {
			$author = $post->getAuthor();
			$data->image = $author->getAvatar();
		}

		// if still no image. lets try to get from placeholder.
		// Since author avatar is 100% can get it, so i exchange the position between author and placeholder image in this fix #578
		// so system can pass the placeholder image to Facebook
		if (!$data->image) {
			$data->image = EB::getPlaceholderImage();
		}

		// Format the content so that it respects the length
		$charLimit = $this->config->get('integrations_facebook_blogs_length');

		// Normalize the autopost content
		$data->content = $post->normalizeAutopostContent($post, $charLimit, $source);

		// Get the url to the blog
		$data->url = $post->getExternalBlogLink();

		return $data;
	}


	/**
	 * Exchanges the request token with the access token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAccess($code = false)
	{
		$params = array( 'client_id' => $this->apiKey,
						 'redirect_uri'	=> $this->redirect,
						 'client_secret'=> $this->apiSecret,
						 'code' => $code
						);

		$token = parent::_oauthRequest(parent::getUrl('graph', '/oauth/access_token'), $params);

		$token = json_decode($token);

		if (!isset($token->access_token)) {
			return false;
		}

		$expires = '';

		// if Facebook return expired date
		if (isset($token->expires_in) && $token->expires_in) {

			$date = EB::date($token->expires_in);

			// Get current date
			$currentDate = EB::date()->toUnix();

			// Add expiry date from current date
			$expires = $currentDate + $token->expires_in;

			$expires = EB::date($expires)->toSql();
		}

		$obj = new stdClass();
		$obj->token = $token->access_token;
		$obj->secret = true;
		$obj->expires = $expires;
		$obj->params = '';

		return $obj;
	}

	/**
	 * Shares the data to facebook
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function share(EasyBlogPost &$post, EasyBlogTableOAuth &$oauth, $system = true, $reposting = false)
	{
		// Get the post data
		$data = $this->extractPostData($post);
		$logs = array();
		$state = false;

		// Construct the params that should be sent to facebook
		// In Facebook 2.11 no longer need to pass in those name, thumbnail, description and etc
		$params = array(
						'link' => $data->url,
						'access_token' => $this->token
					);

		if ($this->config->get('integrations_facebook_introtext_message') && $data->content) {
			$params['message'] = $data->content;
		}

		$isNew = $post->isNew();

		// if detected that is post update, we need to send a POST request to Facebook crawler for re-scrape in order to get the latest post content during autopost.
		if (!$isNew || $reposting) {

			$scrapeObj = array(
							'id' => $data->url,
							'access_token' => $this->token,
							'scrape' => true
						 );

			// re-scrape again if update the post
			$rescrapeResponse = parent::api('/', 'POST', $scrapeObj);
		}

		// If this is not a system auto posting or it isn't impersonating anyone, post it
		if ((!$this->config->get('integrations_facebook_impersonate_group') && !$this->config->get('integrations_facebook_impersonate_page')) || !$system) {
			$response = parent::api('/me/feed', 'post', $params);
			$state = isset($response['id']) ? true : false;
			EB::oauth()->log($oauth, $post, $state, $response);
		}

		// Need to check here if the primary category has pages autoposting set up.
		$categoryParams = $post->getPrimaryCategory()->getParams();

		// If it passes here, we know that this is a system posting already. Check if we should impersonate as a group
		if ($this->config->get('integrations_facebook_impersonate_group') && $system) {
			$groups = $this->config->get('integrations_facebook_group_id');
			$categoryGroups = $categoryParams->get('facebook_group_id', array());

			// if the category has its own autoposting group, we use that one instead.
			if ($categoryGroups) {
				$groups = $categoryGroups;
			}

			$groups = explode(',', $groups);

			// Get a list of groups the user can access
			$groupAccess = parent::api('/me/groups', 'GET', array('access_token' => $this->token, 'limit' => 500));

			// Now we need to find the access for the particular group that they want to share
			if (isset($groupAccess['data']) && $groupAccess) {

				// We need to ensure that the user really has access to the group
				foreach ($groups as $group) {
					foreach ($groupAccess['data'] as $access) {
						if ($access['id'] == $group) {
							$response = parent::api('/' . $group . '/feed', 'post', $params);

							$state = isset($response['id']) ? true : false;

							EB::oauth()->log($oauth, $post, $state, $response);
						}
					}
				}
			}
		}

		// Determines if we should auto post to a facebook page
		if ($this->config->get('integrations_facebook_impersonate_page') && $system) {
			$pages = $this->config->get('integrations_facebook_page_id');
			$categoryPages = $categoryParams->get('facebook_page_id', array());

			// if the category has its own autoposting page, we use that one instead.
			if ($categoryPages) {
				$pages = $categoryPages;
			}

			$pages = explode(',', $pages);

			// Get a list of pages the user can access
			$pageAccess = parent::api('/me/accounts', array('access_token' => $this->token, 'limit' => 500));

			foreach ($pages as $page) {

				if (isset($pageAccess['data'])) {
					foreach ($pageAccess['data'] as $access) {
						if ($access['id'] == $page) {

							// We need to set the access now to the page's access
							$params['access_token'] = $access['access_token'];

							$response = parent::api('/' . $page . '/feed', 'post', $params);

							$state = isset($response['id']) ? true : false;

							EB::oauth()->log($oauth, $post, $state, $response);
						}
					}
				}
			}
		}

		return $state;
	}

	/**
	 * Retrieves a list of pages
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPages()
	{
		// Get a list of accounts associated to this user
		$result	= parent::api('/me/accounts', array('access_token' => $this->token, 'limit' => 500));

		$pages 	= array();

		if (!$result) {
			return $pages;
		}

		if (isset($result['error']) && $result['error']) {
			return $pages;
		}

		foreach ($result['data'] as $page) {
			$pages[] = (object) $page;
		}

		return $pages;
	}

	/**
	 * Retrieves a list of groups
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getGroups()
	{
		// Get a list of groups associated to this user
		$result	= parent::api('/me/groups', 'GET', array('access_token' => $this->token, 'limit' => 500));
		$groups = array();

		if (!$result) {
			return $groups;
		}

		if (isset($result['error']) && $result['error']) {
			return $groups;
		}

		foreach ($result['data'] as $group) {
			$groups[] = (object) $group;
		}

		return $groups;
	}

	/**
	 * Sets the request token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setRequestToken($token, $secret)
	{
	}

	/**
	 * Sets the access token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setAccess($access)
	{
		$access = new JRegistry($access);
		$this->token = $this->normalizeToken($access->get('token'));
	}

	/**
	 * Normalize the token access
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function normalizeToken($token)
	{
		// Double check if this token is still in encoded version
		$tmpToken = json_decode($token);

		if ($tmpToken && isset($tmpToken->access_token)) {
			$token = $tmpToken->access_token;
		}

		return $token;
	}

	/**
	 * Revokes application access from Facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function revoke()
	{
		try {
			$result = parent::api('/me/permissions', 'DELETE', array('access_token' => $this->token));
		} catch(Exception $e) {
			$result = false;
		}

		return $result;
	}


	/**
	 * Revokes application access from Facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function notifyAdminTokenExpiry($days = 7)
	{
		$jconfig = EB::jconfig();

		if (!$this->config->get('integrations_facebook') || !$this->apiKey || !$this->apiSecret) {
			// facebook autoposting disabled. no further action required.
			return EB::exception('Facebook autoposting disabled. Nothing to process.', EASYBLOG_MSG_SUCCESS);
		}

		$days = (int) $days;

		if (! $days) {
			// default to 7 days
			$days = 7;
		}

		$model = EB::model('Oauth');
		$results = $model->getSoonTobeExpired('facebook', $days);

		if ($results) {

			$data = array();
			$data['site'] = $jconfig->get('sitename');
			$data['type'] = 'Facebook';

			$emails = array();
			$oauthIds = array();

			foreach ($results as $result) {

				$email = new stdClass();
				$email->unsubscribe = false;
				$email->email = $result->email;
				$email->name = $result->name;
				$emails[] = $email;

				$oauthIds[] = $result->id;
			}

			// update the oauth's notify column to true so that the next cycle it will not pick up the same entry
			$model->markNotify($oauthIds);

			// now add into mailq
			$notification = EB::notification();
			$notification->send($emails, 'COM_EASYBLOG_EMAIL_FACEBOOK_TITLE_TOKEN_EXPIRY', 'oauth.token.expiry', $data);

			return EB::exception('Notification on Facebook token expiration processed.', EASYBLOG_MSG_SUCCESS);
		}

		return EB::exception('No notification to be processed on Facebook token expiration.', EASYBLOG_MSG_SUCCESS);
	}



	/**
	 * Overrides the exception method so that we can silently fail
	 *
	 * @since	5.0
	 * @access	public
	 */
	protected function throwAPIException($result)
	{
		$e = new EasyBlogFacebookApiException($result);

		$message = $e->getMessage();

		$exception = EB::exception($message);

		$this->error = $exception;
	}
}
