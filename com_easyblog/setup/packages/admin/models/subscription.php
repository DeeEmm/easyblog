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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelSubscription extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->jConfig->get('list_limit') == 0) ? 5 : $this->jConfig->get('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Generic API to subscribe a user
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function subscribe($type, $id, $email, $name, $userId = '0')
	{
		$my = JFactory::getUser();

		if ($type == 'blog') {
			$this->addBlogSubscription($id, $email, $userId, $name);
		}
	}

	/**
	 * Add / Update an existing blog subscription
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function addBlogSubscription($blogId, $email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		// If user is not allowed here, skip this
		if (!$acl->get('allow_subscription') || (!$my->id && !$config->get('main_allowguestsubscribe'))) {
			return false;
		}

		// Check if user already has an existing subscription
		$subscriptionId = $this->isBlogSubscribed($blogId, $email, $userId);

		// Get the subscriber object
		$subscriber = EB::table('Subscriptions');

		// Try to load the subscription data
		if ($subscriptionId) {
			$subscriber->load($subscriptionId);
		}

		$subscriber->utype = EBLOG_SUBSCRIPTION_ENTRY;
		$subscriber->uid = $blogId;
		$subscriber->email = $email;
		$subscriber->fullname = $fullname;
		$subscriber->user_id = $userId;

		$date = EB::date();
		$subscriber->created = $date->toSql();

		$state = $subscriber->store();

		if (!$state) {
			return false;
		}

		return $subscriber;
	}


	/**
	 * Determines if the email is subscribed to the blog post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isBlogSubscribed($id, $email, $userId = '0')
	{
		$db = EB::db();

		$query = 'SELECT `id` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` = ' . $db->Quote($id);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);

		if ($userId) {
			$query .= ' AND (`user_id` = ' . $db->Quote($userId);
			$query .= ' OR `email` = ' . $db->Quote($email) .')';
		} else {
			$query .= ' AND `email` = ' . $db->Quote($email);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Notify subscriber
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function notify(EasyBlogTableSubscriptions $subscriber)
	{
		$post = EB::post($subscriber->uid);

		// Send confirmation email to subscriber.
		$helper = EB::subscription();
		$template = $helper->getTemplate();

		$template->uid = $subscriber->id;
		$template->utype = 'subscription';
		$template->user_id = $subscriber->user_id;
		$template->uemail = $subscriber->email;
		$template->ufullname = $subscriber->fullname;
		$template->ucreated = $subscriber->created;
		$template->targetname = $post->title;
		$template->targetlink = $post->getExternalPermalink();

		// Do not notify author of blog post
		if ($post->created_by != $subscriber->user_id) {
			$helper->addMailQueue($template);
		}

		return true;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	public function isSiteSubscribedUser($userId, $email)
	{
		$db = EB::db();

		$query = 'select `id` from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE);
		$query .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves the subscription id
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getSubscriptionId($email, $uid, $type = EBLOG_SUBSCRIPTION_SITE)
	{
		$db = EB::db();

		$query = 'select `id` from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote($type);
		$query .= ' AND `email` = ' . $db->Quote($email) ;

		if ($type != EBLOG_SUBSCRIPTION_SITE) {
			$query .= ' AND `uid`=' . $db->Quote($uid);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function isSiteSubscribedEmail($email)
	{
		$db = EB::db();

		$query = 'select `id` from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE);
		$query .= ' AND `email` = ' . $db->Quote($email) ;

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function addSiteSubscription($email, $userId = '0', $fullname = '')
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if (!$acl->get('allow_subscription') && (!$my->id && !$config->get('main_allowguestsubscribe'))) {
			return false;
		}

		$date = EB::date();

		$subscription = EB::table('Subscriptions');

		$subscription->uid = '0';
		$subscription->utype = EBLOG_SUBSCRIPTION_SITE;
		$subscription->email = $email;

		if ($userId) {
			$subscription->user_id = $userId;
		}

		$subscription->fullname = $fullname;
		$subscription->created = EB::date()->toSql();

		$state = $subscription->store();

		if ($state === EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN) {
			return EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN;
		}

		return $subscription;
	}

	public function updateSiteSubscriptionEmail($sid, $userid, $email)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->user_id = $userid;
			$subscriber->email = $email;
			$subscriber->store();
		}
	}

	/**
	 * Retrieves a list of subscribers for a particular coposite item
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getSubscribers($type, $uid)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->qn('utype') . '=' . $db->Quote($type);
		$query[] = 'AND ' . $db->qn('uid') . '=' . $db->Quote($uid);

		$db->setQuery($query);

		$result = $db->loadObjectList();
		$subscribers = array();
		$subscribers['guests'] = array();
		$subscribers['users'] = array();

		if (!$result) {
			return $subscribers;
		}

		foreach ($result as $row) {

			if ($row->user_id) {
				$user = EB::user($row->user_id);
				$subscribers['users'][] = $user;
			} else {
				$subscribers['guests'][] = $row;
			}

		}

		return $subscribers;
	}

	/**
	 * Retrieve total number of subscribers on the site
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getTotalSiteSubscribers()
	{
		$db = EB::db();

		$query = 'select count(1) from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE);

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	function getSiteSubscribers()
	{
		$db = EB::db();

		$query = 'select *, ' . $db->Quote('sitesubscription') . ' as `type` from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getMembersAndSubscribers()
	{
		$db = EB::db();
		$saUsersIds = EB::getSAUsersIds();

		$query  = "(select `id`, `id` as `user_id`, `name` as `fullname`, `email`, now() as `created`, 'member' as `type` from `#__users`";
		$query  .= " where `block` = 0";
		// do not get superadmin users.
		if ($saUsersIds) {
			$query  .= " and `id` NOT IN (" . implode(',', $saUsersIds) . ")";
		}

		$query .= ")";
		$query .= " union ";
		$query .= '(select `id`, `user_id`, `fullname`, `email`, `created`,';
		$query .= ' (case';
		$query .= '     when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE) . ' then ' . $db->Quote('sitesubscription');
		$query .= '     when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER) . ' then ' . $db->Quote('bloggersubscription');
		$query .= '     when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY) . ' then ' . $db->Quote('categorysubscription');
		$query .= '     when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG) . ' then ' . $db->Quote('teamsubscription');
		$query .= '     when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY) . ' then ' . $db->Quote('subscription');
		$query .= ' end) as `type` from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `user_id` = 0)';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function massAssignSubscriber($file)
	{
		$delimiter = ',';
		$data = array();

		// Open the csv file for reading.
		$contents = file_get_contents($file);
		$data = explode("\n", $contents);

		if (!$data) {
			return false;
		}

		// Collect the list of failed and successfull items
		$failed = array();
		$success = array();
		$subscribed = array();

		foreach ($data as $row) {
			$tmp = explode(',', $row);
			$name = '';
			$email = '';

			// If there's only 1 item in this row, we know that it's just the email only.
			if (count($tmp) == 1) {
				$email = $tmp[0];
			}

			if (count($tmp) == 2) {
				list($name, $email) = $tmp;
			}

			// Skip this
			if (!$email || EBString::strpos($email, "@") === false) {
				$failed[] = $email;
				continue;
			}

			// we assume the userid is always 0 (guest)
			if ($this->isSiteSubscribedEmail($email)) {
				$subscribed[] = $email;
				continue;
			}

			$this->addSiteSubscription($email, '', $name);
			$success[] = $email;
		}

		return $success;
	}

	/**
	 * Allow callers to export subscribers
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function export($type = 'site')
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT fullname, email, utype, uid FROM ' . $db->qn('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->qn('utype') . '=' . $db->Quote($type);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		return $result;
	}

	/**
	 * Retrieve current email data whether it already subscribed
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function subscriptionExist($email, $uid, $type = EBLOG_SUBSCRIPTION_SITE)
	{
		$db = EB::db();

		$query = 'select * from ' . $db->qn('#__easyblog_subscriptions');
		$query .= ' where `utype` = ' . $db->Quote($type);
		$query .= ' AND `email` = ' . $db->Quote($email);
		$query .= ' AND `uid`=' . $db->Quote($uid);

		$db->setQuery($query);
		$result = $db->loadResult() > 0 ? true : false;

		return $result;
	}

}
