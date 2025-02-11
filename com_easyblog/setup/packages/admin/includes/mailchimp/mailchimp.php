<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyBlogMailchimp extends EasyBlog
{
	public $key = null;
	public $url = 'api.mailchimp.com/3.0/';

	public function __construct()
	{
		parent::__construct();

		$this->key = $this->config->get('subscription_mailchimp_key');

		if ($this->key) {
			$this->exp_apikey = explode('-', $this->key);
			$this->auth = array('Authorization: apikey ' . $this->exp_apikey[0] . '-' . $this->exp_apikey[1]); 
			$this->url = 'https://' . $this->exp_apikey[1] . '.' . $this->url;
		}
	}

	/**
	 * Creates a new campaign and send it immediately.
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	public function notify($emailTitle, $emailData, &$blog)
	{
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
		$config = EB::config();

		if (!function_exists('curl_init')) {
			echo JText::_('COM_EASYBLOG_CURL_DOES_NOT_EXIST');
		}

		if (!$config->get('subscription_mailchimp')) {
			return;
		}

		$listId	= $config->get('subscription_mailchimp_listid');

		if (!$listId) {
			return;
		}

		$jconfig = EB::jconfig();
		$defaultEmailFrom = $jconfig->get('mailfrom');
		$defaultFromName = $jconfig->get('fromname');

		$fromEmail = $config->get('mailchimp_from_email', $defaultEmailFrom);
		$fromName = $config->get('mailchimp_from_name', $defaultFromName);

		$blogContent = $blog->getIntro(true);
		$previewText = EBString::substr($blogContent, 0, 150);

		$settings = array();
		$settings['title'] = $blog->title;
		$settings['subject_line'] = $emailTitle;
		$settings['from_name'] = $fromName;
		$settings['reply_to'] = $fromEmail;
		$settings['authenticate'] = true;
		$settings['preview_text'] = $previewText;

		$campaign = array();
		$campaign['recipients'] = array('list_id' => $listId);
		$campaign['type'] = 'regular';
		$campaign['settings'] = $settings;
		$campaign['tracking'] = array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);

		$campaignJson = json_encode($campaign);

		$campaignId = $this->createCampaign($campaignJson);

		if (!$campaignId) {
			return;
		}

		$content = array('html' => EB::notification()->getTemplateContents('post.new', $emailData));

		$contentJson = json_encode($content);
		
		$response = $this->addCampaignContent($campaignId, $contentJson);

		if (!$response) {
			return;
		}

		$this->sendCampaign($campaignId);
	}

	/**
	 * Allows caller to create a campaign
	 *
	 * @since   5.2.10
	 * @access  public
	 */
	public function createCampaign($jsonData)
	{
		$url = $this->url . 'campaigns';
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($result, false);

		if (!isset($response->id)) {
			return false;
		}

		return $response->id;
	}

	/**
	 * Allows caller to add content to campaign
	 *
	 * @since   5.2.10
	 * @access  public
	 */
	public function addCampaignContent($campaignId, $jsonData)
	{
		$url = $this->url . '/campaigns/' . $campaignId . '/content';
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($result, false);

		if (isset($response->status)) {
			return false;
		}

		return true;
	}

	/**
	 * Allow callers to send campaign
	 *
	 * @since   5.2.10
	 * @access  public
	 */
	public function sendCampaign($campaignId)
	{
		$url = $this->url . '/campaigns/' . $campaignId . '/actions/send';
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($result, false);
	}

	/**
	 * Allows caller to send a subscribe IPN to mailchimp
	 *
	 * @since	5.2.10
	 * @access	public
	 */
	public function subscribe($email, $firstName, $lastName = '')
	{
		EB::loadLanguages();

		if (!function_exists('curl_init')) {
			return false;
		}

		if (!$this->config->get('subscription_mailchimp')) {
			return false;
		}

		// Get the list id
		$listId	= $this->config->get('subscription_mailchimp_listid');

		if (!$listId) {
			return false;
		}

		$firstName = urlencode($firstName);
		$lastName = urlencode($lastName);

		$fields = array('FNAME' => $firstName, 'LNAME' => $lastName);

		$members = array('email_address' => $email, 'status'=> 'pending', 'merge_fields' => $fields);
		
		$json = json_encode($members);

		$url = $this->url . '/lists/' . $listId . '/members';
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($result, false);

		return true;
	}
}
