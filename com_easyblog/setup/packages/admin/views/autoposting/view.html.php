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

class EasyBlogViewAutoposting extends EasyBlogAdminView
{
	/**
	 * Default autoposting display
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for user access
		$this->checkAccess('easyblog.manage.autoposting');

		$layout = $this->getLayout();
		$step = $this->input->get('step', 0, 'int');

		$this->set('step', $step);

		if (method_exists($this, $layout)) {
			return $this->$layout($tpl);
		}

		// Default redirect to facebook if no layouts provided
		return $this->app->redirect('index.php?option=com_easyblog&view=autoposting&layout=facebook');
	}

	/**
	 * Displays the facebook process to setup auto posting for Facebook
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function facebook()
	{
		// Add the button
		JToolbarHelper::apply('facebook.save');

		// Determines if facebook has already been associated
		$associated = EB::oauth()->associated('facebook');

		// Load the oauth table
		$oauth = EB::table('Oauth');
		$oauth->load(array('type' => 'facebook', 'system' => true));

		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_FB_TITLE');
		$this->addHelpButton('/administrators/autoposting/facebook-autoposting');

		// Default expire values
		$expire = '';

		if (isset($oauth->expires)) {
			$expire = $oauth->expires;
		}

		// Legacy codes will contain an "expires" property in the json object
		if ($oauth->id && isset($oauth->expires) && !$oauth->expires) {

			$legacyExpires	= $oauth->getAccessTokenValue('expires');

			if ($legacyExpires) {
				$created = strtotime($oauth->created);
				$expire = $legacyExpires + $created;
			}
		}

		// Format the expiry date
		if ($expire) {
			$expire = EB::date($expire)->format(JText::_('DATE_FORMAT_LC1'));
		}


		// Get the facebook client
		$client = EB::oauth()->getClient('Facebook');

		// Get a list of pages
		$pages = array();

		if ($associated && $oauth->access_token) {
			$client->setAccess($oauth->access_token);

			// Get pages that are available
			try {
				$pages = $client->getPages();
			} catch(Exception $e) {
				$pages = array();
			}
		}

		// Get a list of stored pages
		$storedPages = $this->config->get('integrations_facebook_page_id');

		if ($storedPages) {
			$storedPages = explode(',', $storedPages);
		}

		// Get a list of groups
		$groups = array();

		if ($associated && $oauth->access_token) {
			$client = EB::oauth()->getClient('Facebook');
			$client->setAccess($oauth->access_token);

			// Get groups that are available
			$groups = [];

			try {
				$groups = $client->getGroups();
			} catch(Exception $e) {

			}
		}

		// Get a list of stored groups
		$storedGroups = $this->config->get('integrations_facebook_group_id', array());

		if ($storedGroups) {
			$storedGroups = explode(',', $storedGroups);
		}

		// Retrieve Facebook oauth valid URIs
		$oauthURIs = EB::oauth()->getOauthRedirectURI();

		// Retrieve Facebook scopes permission
		$selectedScopePermissions = $this->config->get('integrations_facebook_scope_permissions');

		if ($selectedScopePermissions) {
			$selectedScopePermissions = explode(',', $selectedScopePermissions);
		}

		$this->set('client', $client);
		$this->set('storedGroups', $storedGroups);
		$this->set('groups', $groups);
		$this->set('storedPages', $storedPages);
		$this->set('pages', $pages);
		$this->set('expire', $expire);
		$this->set('associated', $associated);
		$this->set('oauthURIs', $oauthURIs);
		$this->set('selectedScopePermissions', $selectedScopePermissions);

		parent::display('autoposting/facebook/default');
	}

	/**
	 * Displays the twitter process to setup auto posting for Facebook
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function twitter()
	{
		JToolbarHelper::apply('twitter.save');

		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_TWITTER_TITLE');
		$this->addHelpButton('/administrators/autoposting/twitter-autoposting');

		$client = EB::oauth()->getClient('twitter');
		$associated = EB::oauth()->associated('twitter');

		// retrieve Twitter oauth callback URIs
		$oauthURIs = EB::oauth()->getOauthRedirectURI('twitter');

		$this->set('client', $client);
		$this->set('associated', $associated);
		$this->set('oauthURIs', $oauthURIs);

		parent::display('autoposting/twitter/default');
	}

	/**
	 * Displays the linkedin process to setup auto posting
	 *
	 * @since	5.2.14
	 * @access	public
	 */
	public function linkedin()
	{
		JToolbarHelper::apply('linkedin.save');

		$this->setHeading('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_TITLE');
		$this->addHelpButton('/administrators/autoposting/linkedin-autoposting');

		$associated = EB::oauth()->associated('linkedin');

		// Initialize the default value
		$companies = array();

		$client = EB::oauth()->getClient('linkedin');

		if ($associated) {

			$oauth = EB::table('oauth');
			$oauth->load(array('type' => 'linkedin', 'system' => true));

			$client->setAccess($oauth->access_token);

			$organizationalRoles = "role=ADMINISTRATOR&state=APPROVED&projection=(elements*(*,roleAssignee~(localizedFirstName,%20localizedLastName),%20organizationalTarget~(localizedName)))";

			// Get the company data
			$data = $client->getCompanyLists($organizationalRoles);
			$linkedinResult = json_decode($data['linkedin']);
			$infoResult = $data['info'];

			$companies = array();

			if ($infoResult['http_code'] == 200) {

				$elements = $linkedinResult->elements;

				foreach ($elements as $element) {
					$element = EB::makeArray($element);

					$company = new stdClass();

					$organizationalTarget = $element['organizationalTarget'];
					$organizationalTarget = explode(':', $organizationalTarget);
					$companyId = $organizationalTarget[3];

					$company->id = $companyId;
					$company->name = $element['organizationalTarget~']['localizedName'];

					$companies[] = $company;
				}
			}
		}

		$storedCompanies = explode(',', $this->config->get('integrations_linkedin_company', ''));

		$oauthURIs = EB::oauth()->getOauthRedirectURI('linkedin');

		// Retrieve Linkedin scopes permission
		$selectedScopePermissions = $this->config->get('integrations_linkedin_scope_permissions');

		if ($selectedScopePermissions) {
			$selectedScopePermissions = explode(',', $selectedScopePermissions);
		}

		$this->set('client', $client);
		$this->set('storedCompanies', $storedCompanies);
		$this->set('companies', $companies);
		$this->set('associated', $associated);
		$this->set('oauthURIs', $oauthURIs);
		$this->set('selectedScopePermissions', $selectedScopePermissions);

		parent::display('autoposting/linkedin/default');
	}

	/**
	 * Renders the logs for auto posting objects
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function logs()
	{
		$this->setHeading('COM_EASYBLOG_AUTOPOST_LOGS');

		$model = EB::model('oauth');
		$status = $this->input->get('status', '', 'word');

		$logs = $model->getLogs($status);
		$pagination = $model->pagination;
		$limit = $model->getState('limit');

		if ($logs) {
			foreach ($logs as &$log) {
				$log->post = EB::post($log->post_id);
				$log->oauth = EB::table('OAuth');
				$log->oauth->load($log->oauth_id);
			}
		}

		JToolBarHelper::custom('autoposting.purge', 'trash', '', JText::_('COM_EASYBLOG_PURGE_LOGS'), false);

		$this->set('status', $status);
		$this->set('pagination', $pagination);
		$this->set('logs', $logs);
		$this->set('limit', $limit);

		parent::display('autoposting/logs/default');
	}
}
