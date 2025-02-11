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

class EasyBlogViewSubscription extends EasyBlogView
{
	/**
	 * Displays the ajax subscription form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		// Check for request forgeries
		FH::checkToken();

		// Guests are not allowed to subscribe
		if (!$this->my->id && !$this->config->get('main_allowguestsubscribe')) {
			return $this->ajax->resolve(JText::_('COM_EASYBLOG_SUBSCRIPTION_PLEASE_LOGIN'));
		}

		if (!$this->acl->get('allow_subscription') ) {
			return $this->ajax->resolve(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG'));
		}

		// Get the subscription type.
		$type = $this->input->get('type', '', 'word');
		$id = $this->input->get('id', '', 'int');

		$title = 'COM_EASYBLOG_SUBSCRIPTION_SITE_DIALOG_TITLE';
		$desc = 'COM_EASYBLOG_SUBSCRIPTION_SITE_DIALOG_CONTENT';

		if ($type === EBLOG_SUBSCRIPTION_BLOGGER) {
			$title = 'COM_EASYBLOG_SUBSCRIPTION_BLOGGER_DIALOG_TITLE';
			$desc = 'COM_EASYBLOG_SUBSCRIPTION_BLOGGER_DIALOG_CONTENT';
		}

		if ($type === EBLOG_SUBSCRIPTION_CATEGORY) {
			$title = 'COM_EB_SUBSCRIPTION_CATEGORY';
			$desc = 'COM_EB_SUBSCRIPTION_CATEGORY_CONTENT';
		}

		if ($type === EBLOG_SUBSCRIPTION_TEAMBLOG) {
			$title = 'COM_EASYBLOG_SUBSCRIPTION_TEAMBLOG_DIALOG_TITLE';
			$desc = 'COM_EASYBLOG_SUBSCRIBE_TEAM_INFORMATION';
		}

		$title = JText::_($title);
		$desc = JText::_($desc);

		$isDoubleOptIn = EB::subscription()->isDoubleOptIn();

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('id', $id);
		$theme->set('isDoubleOptIn', $isDoubleOptIn);

		$output = $theme->output('site/subscription/dialogs/form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to subscribe to the blog
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function subscribe()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that guests are allowed to subscribe
		if (!$this->acl->get('allow_subscription') && !$this->my->id && !$this->config->get('main_allowguestsubscribe')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG'));
		}

		if (!$this->my->id && !$this->config->get('main_allowguestsubscribe')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG'));
		}

		// Validate the email address
		$email = $this->input->get('email', '', 'default');

		if (!$email) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_EMPTY_ERROR'));
		}

		// Ensure that email do not contain any whitespace
		$email = trim($email);

		// Test if email is valid
		$valid = EB::string()->isValidEmail($email);

		if (!$valid) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_INVALID_ERROR'));
		}

		$name = $this->input->get('name', '', 'default');
		$uid = $this->input->get('uid', '', 'int');
		$type  = $this->input->get('type', '', 'string');
		$userId  = $this->input->get('userId', 0, 'int');

		// if the subscription type is a site, we need to make sure the id is always zero.
		if ($type === 'site') {
			$uid = 0;
		}

		if (!$name) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_NAME_EMPTY_ERROR'));
		}

		// Try to validate the username and email
		$options = ['email' => $email];

		// Add the user's name
		$options['name'] = $name;

		// Process mailchimp subscriptions here.
		$mailchimp = EB::mailchimp()->subscribe($email, $name);

		// Process mailchimp subscriptions here.
		$sendy = EB::sendy()->subscribe($email, $name);

		$isDoubleOptIn = false;
		$subscription = EB::table('Subscriptions');

		// Only use our built in subscription if mailchimp and sendy didn't send anything
		if (!$mailchimp && !$sendy) {
			// Since we have already merged all these tables into one, we don't need to use separate methods
			// to insert new subscriptions
			$options = [
				'email' => $email,
				'uid' => $uid,
				'utype' => $type,
				'user_id' => $userId ? $userId : 0
			];

			if ($name) {
				$options['fullname'] = $name;
			}

			$subscription->load($options);

			// check for the current email is it already subscribed
			$isSubscribed = $subscription->isSubscribed($email, $uid, $type);

			if ($isSubscribed) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR'));
			}

			// Bind the data
			$subscription->bind($options);

			// Try to save the record now
			$state = $subscription->store();

			// Mean it processing subscription double opt-in
			if ($state === EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN) {
				$isDoubleOptIn = true;
			}

			// We don't really need to do anythin
			// If the subscribed method returns false, we could assume that they are already subscribed previously
			if (!$state) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR'));
			}
		}

		$theme = EB::themes();
		$theme->set('email', $email);

		$namespace = 'site/subscription/dialogs/subscribed';

		if ($isDoubleOptIn) {
			$namespace = 'site/subscription/dialogs/subscription.progress';
		}

		$output = $theme->output($namespace);

		return $this->ajax->resolve($output, $subscription->id);
	}

	/**
	 * Displays a confirmation dialog to ask if the user really wants to unsubscribe
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function confirmUnsubscribe()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the subscription id
		$id = $this->input->get('id', 0, 'int');

		// We also allow caller to unsubscribe by uid and type
		$uid = $this->input->get('uid');
		$type = $this->input->get('type');

		// Get the return url
		// for the return link from unsubscribe form, the link is a normal string.
		// we canot do base64 here.
		$return = $this->input->get('return', '', 'default');

		// Try to load the subscription
		$subscription = EB::table('Subscriptions');
		$subscription->load($id);

		// Try to load by uid and type
		if (!$subscription->id || !$id) {
			$subscription->load(array('uid' => $uid, 'utype' => $type));
		}

		if (!$subscription->id || !$id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SUBSCRIPTION_INVALID_ID'));
		}

		$theme = EB::themes();
		$theme->set('subscription', $subscription);
		$theme->set('return', $return);

		$output = $theme->output('site/subscription/dialogs/unsubscribe');

		return $this->ajax->resolve($output);
	}
}
