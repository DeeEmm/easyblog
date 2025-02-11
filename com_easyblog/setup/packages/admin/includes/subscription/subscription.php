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

class EasyBlogSubscription extends EasyBlog
{
	/**
	 * Retrieves the html codes for the subscription confirmation email
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function addMailQueue(EasyBlogSubscriptionItem $item, $sentSubscribeSuccessEmail = false)
	{
		// Build the variables for the template
		$data = array();
		$data['fullname'] = $item->ufullname;
		$data['target'] = $item->targetname;

		// To append joomla url inside the link.
		$domain = rtrim(JURI::root(), '/');

		if (stripos($item->targetlink, $domain) === false) {
			$item->targetlink = rtrim(JURI::root(), '/') . '/'. ltrim($item->targetlink, '/');
		}

		$data['targetlink'] = $item->targetlink;
		$data['type'] = $item->utype;

		$recipient = new stdClass();
		$recipient->email = $item->uemail;
		$recipient->unsubscribe = $this->getUnsubscribeLink($item, true);

		$subscription = EB::subscription();
		$isDoubleOptIn = false;

		// if the subscription process type is double opt-in
		if ($subscription->isDoubleOptIn()) {
			$isDoubleOptIn = true;
			$recipient->subscribeLink = $this->getSubscribeLink($item, true);
		}

		$title = $isDoubleOptIn && !$sentSubscribeSuccessEmail ? JText::_('COM_EB_PLEASE_CONFIRM_SUBSCRIPTION_EMAIL') : JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_CONFIRMATION');
		$templateName = $isDoubleOptIn && !$sentSubscribeSuccessEmail ? 'subscription.verification' : 'subscription.confirmation';

		$notification = EB::notification();
		$state = $notification->send(array($recipient), $title, $templateName, $data);

		return $state; 
	}

	public function getTemplate()
	{
		$template = new EasyBlogSubscriptionItem();
		return $template;
	}

	/**
	 * Generates the unsubscribe link for the email
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getUnsubscribeLink($data, $external = false)
	{
		$itemId = EBR::getItemId('latest');

		// Generate the unsubscribe hash
		$hash = base64_encode(json_encode($data->export()));

		$link = EBR::getRoutedURL('index.php?option=com_easyblog&task=subscription.unsubscribe&data=' . $hash . '&Itemid=' . $itemId, false, $external);

		return $link;
	}

	/**
	 * Generates the subscribe link for the email
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getSubscribeLink($data, $external = false)
	{
		$itemId = EBR::getItemId('latest');

		$data = array(
				'uid' => $data->uid,
				'utype' => $data->utype,
				'user_id' => $data->user_id,
				'email' => $data->uemail,
				'fullname' => $data->ufullname,
				'created' => $data->ucreated
				);

		// Generate the subscribe hash
		$hash = base64_encode(json_encode($data));

		$link = EBR::getRoutedURL('index.php?option=com_easyblog&task=subscription.confirmSubscribe&data=' . $hash . '&Itemid=' . $itemId, false, $external);

		return $link;
	}

	/**
	 * Retrieves the html codes for the subscription confirmation email
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function processConfirmationEmail()
	{
		$config = EB::config();

		$gdprEnabled = $config->get('gdpr_enabled');
		$subscriptionConfirmationEnabled = $config->get('main_subscription_confirmation');

		// System will send out the subscription confirmation email to the user, regarding this process user haven't subscribe on the site yet.
		if ($gdprEnabled && $subscriptionConfirmationEnabled) {
			return EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN;
		}

		// System will send out the subscription confirmation email to the user, regarding this process user already subscribed on the site.
		if (!$gdprEnabled && $subscriptionConfirmationEnabled) {
			return EASYBLOG_SUBSCRIPTION_SINGLE_OPT_IN;
		}

		// System will not send out subscription confirmation email to the user
		if ($gdprEnabled && !$subscriptionConfirmationEnabled) {
			return EASYBLOG_SUBSCRIPTION_WITHOUT_CONFIRMATION_EMAIL;
		}

		// System will not send out subscription confirmation email to the user
		if (!$gdprEnabled && !$subscriptionConfirmationEnabled) {
			return EASYBLOG_SUBSCRIPTION_WITHOUT_CONFIRMATION_EMAIL;
		}

		return EASYBLOG_SUBSCRIPTION_WITHOUT_CONFIRMATION_EMAIL;
	}

	/**
	 * Retrieves the html codes for the subscription confirmation email
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function isSingleOptIn()
	{
		return $this->processConfirmationEmail() === EASYBLOG_SUBSCRIPTION_SINGLE_OPT_IN;
	}

	/**
	 * Retrieves the html codes for the subscription confirmation email
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function isDoubleOptIn()
	{
		return $this->processConfirmationEmail() === EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN;
	}

	/**
	 * Retrieves the html codes for the subscription confirmation email
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function isWithoutConfirmation()
	{
		return $this->processConfirmationEmail() === EASYBLOG_SUBSCRIPTION_WITHOUT_CONFIRMATION_EMAIL;
	}
}

class EasyBlogSubscriptionItem
{
	public $uid = null;
	public $utype = null;
	public $user_id = null;
	public $uemail = null;
	public $ufullname = null;
	public $ucreated = null;

	// eg. blog post title and link
	// eg. blogger name and link
	// eg. category name and link
	// and etc
	public $targetname 	= null;
	public $targetlink 	= null;

	public function export()
	{
		$data = array(
				'uid' => $this->uid,
				'utype' => $this->utype,
				'user_id' => $this->user_id,
				'created' => $this->ucreated
				);

		return $data;
	}
}