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

class EasyBlogPush extends EasyBlog
{
	public $lib = null;

	/**
	 * Determines if onesignal is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isEnabled()
	{
		$enabled = false;
		$appId = $this->config->get('onesignal_app_id');
		$apiKey = $this->config->get('onesignal_rest_key');

		if ($this->config->get('onesignal_enabled') && $appId && $apiKey) {
			$enabled = true;
		}

		return $enabled;
	}

	/**
	 * Generates the necessary script to activate subscription
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function generateScripts()
	{
		$subdomain = $this->config->get('onesignal_subdomain', '');

		if ($subdomain && stristr($subdomain, 'https://') === false) {
			$subdomain = 'https://' . $subdomain;
		}

		$theme = EB::themes();
		$theme->set('subdomain', $subdomain);
		$output = $theme->output('site/integrations/onesignal/default');

		return $output;
	}

	/**
	 * Create a filter rule
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createFilter($field, $key, $relation, $value)
	{
		$filter = new stdClass();
		$filter->field = $field;
		$filter->key = $key;
		$filter->relation = $relation;
		$filter->value = $value;

		return $filter;
	}

	/**
	 * Create an operator rule
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createOperator($operator)
	{
		$filter = new stdClass();
		$filter->operator = $operator;

		return $filter;
	}

	/**
	 * Notifies the push api (onesignal)
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function notify(EasyBlogPost $post)
	{
		// Ensure that this feature is enabled
		if (!$this->isEnabled()) {
			return false;
		}

		// Prepare the contents to be pushed
		$heading = array("en" => $post->title);
		$content = array("en" => JText::sprintf('COM_EASYBLOG_PUSH_NOTIFICATION_INFO', $post->getAuthor()->getName()));
		$filters = array();

		// Exclude the author from being notified
		$filters[] = $this->createFilter('tag', 'id', '!=', $post->getAuthor()->id);

		$fields = array(
						'app_id' => $this->config->get('onesignal_app_id'),
						'headings' => $heading,
						'contents' => $content,
						'url' => $post->getPermalink(false, true),
						'chrome_web_icon' => $post->getAuthor()->getAvatar()
				);

		if ($filters) {
			$fields['filters'] = $filters;
		}

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ' . $this->config->get('onesignal_rest_key')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}

}
