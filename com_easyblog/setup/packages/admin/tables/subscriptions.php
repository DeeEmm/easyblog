<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableSubscriptions extends EasyBlogTable
{
	public $id = null;
	public $uid = null;
	public $utype = null;
	public $user_id = null;
	public $fullname = '';
	public $email = '';
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_subscriptions', 'id', $db);
	}

	/**
	 * Override the parents store method so we can send confirmation email
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function store($updateNulls = false, $forceUpdate = false)
	{
		$config = EB::config();

		// We need to ensure that the language constants can be translated. #2073
		EB::loadLanguages();

		$isNew = !$this->id ? true : false;

		// Ensure that the `created` column is valid
		if (!$this->created) {
			$this->created = JFactory::getDate()->toSql();
		}

		// Send confirmation email to subscribers
		$state = $this->processConfirmationEmail($isNew, $forceUpdate, $updateNulls);

		// skip following process if the subscription behaviour is double opt-in
		if ($state === EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN) {
			return EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN;
		}

		// Notify site admins when there is a new subscriber
		if ($isNew && $config->get('main_subscription_admin_notification')) {
			$this->notifyAdmin();
		}

		// Notify author when someone subscribe on their own author page.
		if ($isNew && $this->utype == EBLOG_SUBSCRIPTION_BLOGGER && $config->get('main_subscription_author_notification')) {
			$this->notifyAuthor();
		}

		// Notify author when someone subscribe on their blog post.
		if ($isNew && $this->utype == EBLOG_SUBSCRIPTION_ENTRY && $config->get('main_subscription_author_post_notification')) {
			$this->notifyAuthor(EBLOG_SUBSCRIPTION_ENTRY);
		}

		return $state;
	}

	/**
	 * Process subscription confirmation email
	 *
	 * @since	5.2.2
	 * @access	public
	 */
	public function processConfirmationEmail($isNew, $forceUpdate, $updateNulls)
	{
		$subscription = EB::subscription();
		$template = $subscription->getTemplate();

		$template->uid = $this->uid;
		$template->utype = $this->utype;
		$template->user_id = $this->user_id;
		$template->uemail = $this->email;
		$template->ufullname = $this->fullname;
		$template->ucreated = $this->created;

		// retrieve item title and the permalink
		$target = $this->getObject();

		$template->targetname = $target->title;
		$template->targetlink = $target->objPermalink;

		// update the subscription data
		if ($forceUpdate) {
			$state = parent::store($updateNulls);

			// notify the user already subscribed successfully
			// this only for double-opt-in process
			if ($state) {
				$subscription->addMailQueue($template, true);
			}

			return $state;
		}

		// If do not need to send subscription confirmation email then we skip it here
		if ($subscription->isWithoutConfirmation()) {
			// store the user subscription data
			$state = parent::store($updateNulls);
			return $state;
		}

		if ($isNew) {

			$post = EB::post($this->uid);

			// Do not notify author of blog post
			if ($this->utype == 'entry' && ($post->created_by != $this->user_id)) {
				$subscription->addMailQueue($template);
	
			} elseif ($this->utype != 'entry') {
				$subscription->addMailQueue($template);
			}

			// if the subscription process type is double opt-in, do not store the subscription user data first
			if ($subscription->isDoubleOptIn()) {

				// some of the cases the user will subscribe to his own article 
				if ($this->utype == 'entry' && ($post->created_by == $this->user_id)) {
					$subscription->addMailQueue($template);
				}

				return EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN;
			}

			// if already come here mean this part should be under single opt-in process type
			// when the user receive this confirmation email, this user already subscribed.
			$state = parent::store($updateNulls);

			return $state;
		}

		return false;
	}

	/**
	 * Notifies author when a new user subscribes to their blog
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function notifyAuthor($subscriptionType = EBLOG_SUBSCRIPTION_BLOGGER)
	{
		$data = array(
						'subscriber' => $this->fullname,
						'subscriberDate' => EB::date()->format(JText::_('DATE_FORMAT_LC1')),
						'type' => $this->utype
					);

		$data['itemName'] = '';
		$data['heading'] = JText::_('COM_EASYBLOG_MAIL_TEMPLATE_NEW_SUBSCRIBER_HEADING_FOR_AUTHOR');
		$title = JText::_('COM_EASYBLOG_MAIL_TEMPLATE_NEW_SUBSCRIBER_HEADING_FOR_AUTHOR');

		// two possibilities blogger id and entry id
		$subscriptionUid = $this->uid;

		// if someone subscribe on the author single blog post
		if ($subscriptionType == EBLOG_SUBSCRIPTION_ENTRY) {
			$post = EB::post($this->uid);
			$permalink = $post->getPermalink(true, true);

			// retrieve the post owner user id
			$subscriptionUid = $post->created_by;
			$data['heading'] = JText::sprintf('COM_EB_MAIL_TEMPLATE_NEW_SUBSCRIBER_HEADING_FOR_AUTHOR_POST', $permalink);
			$title = JText::_('COM_EB_MAIL_TEMPLATE_NEW_SUBSCRIBER_HEADING_FOR_AUTHOR_POST_HEADING');
		}

		$author = EB::user($subscriptionUid);

		$obj = new StdClass();
		$obj->unsubscribe = false;
		$obj->email = $author->user->email;

		$emails = array($obj);

		$lib = EB::notification();
		$lib->send($emails, $title, 'subscription.notification', $data);	
	}

	/**
	 * Notifies site admin when a new user subscribes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function notifyAdmin()
	{
		$data = [
			'subscriber' => $this->fullname,
			'subscriberDate' => EB::date()->format(JText::_('DATE_FORMAT_LC1')),
			'type' => $this->utype
		];

		$data['itemName'] = '';

		if ($this->utype == 'entry') {
			$post = EB::post($this->uid);

			$data['itemName'] = $post->title;
		}

		if ($this->utype == 'category') {
			$category = EB::table('Category');
			$category->load($this->uid);

			$data['itemName'] = $category->title;
		}

		if ($this->utype == 'blogger') {
			$blogger = EB::user($this->uid);

			$data['itemName'] = $blogger->getName();
		}

		$title = JText::_('COM_EASYBLOG_SUBSCRIPTION_NEW_' . strtoupper($this->utype));
		$emails = array();

		$lib = EB::notification();

		// If custom email addresses is specified, use that instead
		$config = EB::config();
		
		if ($config->get('custom_email_as_admin')) {
			$lib->getCustomEmails($emails);
		} else {
			$lib->getAdminEmails($emails);
		}

		$lib->send($emails, $title, 'subscription.notification', $data);
	}

	/**
	 * Retrieves the object of the subscription item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getObject()
	{
		if ($this->utype == 'category') {
			$obj = EB::table('Category');
			$obj->load($this->uid);
			$obj->objAvatar = $obj->getAvatar();
			$obj->objPermalink = $obj->getExternalPermalink();
		}

		if ($this->utype == 'blogger') {
			$obj = EB::user($this->uid);
			$obj->title = $obj->getName();
			$obj->objAvatar = $obj->getAvatar();
			$obj->objPermalink = $obj->getExternalPermalink();
		}

		if ($this->utype == 'site') {
			$obj = new stdClass();
			$obj->title = EB::config()->get('main_title');
			$obj->permalink = EBR::getRoutedURL('index.php?option=com_easyblog', false, true);
			$obj->objAvatar = '';
			$obj->objPermalink = $obj->permalink;
		}

		if ($this->utype == 'teamblog' || $this->utype == 'team') {

			$team = EB::table('Teamblog');
			$team->load($this->uid);

			$obj = new stdClass();
			$obj->title = $team->title;
			$obj->objAvatar = $team->getAvatar();
			$obj->objPermalink = $team->getExternalPermalink();
		}

		if ($this->utype == 'entry') {

			// Get the post object
			$post = EB::post($this->uid);

			$obj = new stdClass();
			$obj->title = $post->title;
			$obj->objPermalink = $post->getExternalPermalink();
			$obj->objAvatar = $post->getImage('medium');
		}
		
		return $obj;
	}

	/**
	 * Retrieves the date object for the creation date of this subscription
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getSubscriptionDate()
	{
		$date = EB::date($this->created);

		return $date;
	}

	/**
	 * Determine this current email whether already subscribed 
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isSubscribed($email, $uid, $type)
	{
		$model = EB::model('Subscription');
		$result = $model->subscriptionExist($email, $uid, $type);

		return $result;
	}
}