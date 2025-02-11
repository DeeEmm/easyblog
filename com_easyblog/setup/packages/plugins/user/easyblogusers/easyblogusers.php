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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgUserEasyBlogUsers extends JPlugin
{
	protected $autoloadLanguage = true;

	public $app = null;
	public $input = null;
	public $config = null;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		if (!$this->exists()) {
			return false;
		}

		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = EB::config();
	}

	/**
	 * Adds a new subscriber records
	 *
	 * @since	5.4.5
	 * @access	public
	 */
	public function addSubscriber($userId, $email, $name = '')
	{
		$model = EB::model('Subscription');
		$exists = $model->isSiteSubscribedUser($userId , $email);

		if ($exists) {
			$model->updateSiteSubscriptionEmail($exists, $userId, $email);
			return true;
		}

		$model->addSiteSubscription($email, $userId, $name);
		return true;
	}

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {

			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$exists = JFile::exists($file);

			if (!$exists) {
				return false;
			}

			require_once($file);

			if (!EB::isFoundryEnabled()) {
				$exists = false;
			}
		}

		return $exists;
	}

	/**
	 * Triggered when saving a user in Joomla
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if (!$this->exists()) {
			return false;
		}

		//j.16
		$this->updateSubscriptions($data);

		$userId	= EBArrayHelper::getValue($data, 'id', 0, 'int');

		// Ensure that there is a record created for the author in the seo section
		if ($userId && $isNew) {

			// Ensure that the user has authoring rights
			$acl = EB::acl($userId);

			if ($acl->get('add_entry')) {
				$model = EB::model('Metas');
				$model->createMeta($userId, META_TYPE_BLOGGER);

				// We should show the user into the author listing page immediately
				// If the new user has add_entry acl #1986
				$table = EB::table('Profile');
				$table->createDefault($userId);
			}

			if ($this->config->get('notification_autosubscribe')) {
				$this->addSubscriber($userId, $data['email'], $data['name']);
			}
		}

		// Process user subscription
		if ($userId && $result && isset($data['easyblogusers']) && (count($data['easyblogusers']))) {
			if (!empty($data['easyblogusers']['subscribe']) && $data['easyblogusers']['subscribe'] == '1') {
				$this->addSubscriber($userId, $data['email'], $data['name']);
			}
		}
	}

	/**
	 * Update user subscription
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateSubscriptions($user)
	{
		$db = EB::db();

		if (is_object($user)) {
			$user = get_object_vars($user);
		}

		if (!isset($user['id']) && empty($user['id'])) {
			return;
		}

		//update subscription tables.
		$userId = $user['id'];
		$userFullname = $user['name'];
		$userEmail = $user['email'];

		// user subscriptions
		$query = 'UPDATE `#__easyblog_subscriptions` SET';
		$query .= ' `user_id` = ' . $db->Quote($userId);
		$query .= ', `fullname` = ' . $db->Quote($userFullname);
		$query .= ' WHERE `email` = ' . $db->Quote($userEmail);
		$query .= ' AND `user_id` = ' . $db->Quote('0');

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Invoked before deleting a user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onUserBeforeDelete($user)
	{
		if (! $this->exists()) {
			return false;
		}

		if (is_object($user)) {
			$user = get_object_vars($user);
		}

		$userId = $user['id'];
		$newOwnerShip = $this->_getnewOwnerShip($userId);

		// Perform all necessary actions
		$this->ownerTransferCategory($userId, $newOwnerShip);
		$this->ownerTransferTag($userId, $newOwnerShip);
		$this->onwerTransferComment($userId, $newOwnerShip);
		$this->ownerTransferPost($userId, $newOwnerShip);
		$this->removeAssignedACLGroup($userId);
		$this->removeAdsenseSetting($userId);
		$this->removeFeedburnerSetting($userId);
		$this->removeOAuthSetting($userId);
		$this->removeFeaturedBlogger($userId);
		$this->removeTeamBlogUser($userId);
		$this->removeBloggerSubscription($userId);
		$this->removeSubscriptions($userId);
		$this->removeEmailNotifications($user);

		// Lastly, delete user from easyblog
		$this->removeEasyBlogUser($userId);
	}

	/**
	 * Get new ownership for the orphan post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function _getnewOwnerShip($curUserId)
	{
		// Predefined, default super admin id
		$user_id = '42';

		$newOwnerShip = $this->config->get('main_orphanitem_ownership', $user_id);

		// we check if the tobe deleted user is the same user id as the saved user id in config.
		if ($curUserId == $newOwnerShip) {

			// this is no no a big no! try to get the next admin.
			$saUsersId = EB::getSAUsersIds();

			if (count($saUsersId) > 0) {

				for ($i = 0; $i < count($saUsersId); $i++) {

					if ($saUsersId[$i] != $curUserId) {

						// New owner found. Let's stop here
						$newOwnerShip = $saUsersId[$i];
						break;
					}
				}
			}
		}

		return $newOwnerShip;
	}

	/**
	 * Transfer owner of the category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function ownerTransferCategory($userId, $newOwnerShip)
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_category`';
		$query .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Transfer the owner of the tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function ownerTransferTag($userId, $newOwnerShip)
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_tag`';
		$query .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Transfer the owner of the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function ownerTransferPost($userId, $newOwnerShip)
	{
		jimport('joomla.filesystem.folder');

		$db = EB::db();
		$postCount = 0;
		$hasUserMediaFolder = false;

		// before we transfer the posts, we need to check if this users
		// has any blog posts that are currently being.
		$query = "select count(1) from `#__easyblog_post`";
		$query .= " where `created_by` = " . $db->Quote($userId);
		$query .= " and `published` != " . $db->Quote(EASYBLOG_POST_BLANK);
		$db->setQuery($query);
		$postCount = $db->loadResult();

		// now lets check if this user has media folder or not.
		$userMediaFolder = JPATH_ROOT . '/' . rtrim($this->config->get('main_image_path'), '/') . '/' . $userId;
		$hasUserMediaFolder = JFolder::exists($userMediaFolder);

		// now lets update the onwer ship
		$query = 'UPDATE `#__easyblog_post`';
		$query .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}

		// now we know this user has not created any blog posts but there are images
		// under this user media folder. lets remove these images.
		if ($postCount == 0 && $hasUserMediaFolder) {
			@JFolder::delete($userMediaFolder);
		}

	}

	/**
	 * Transfer the owner of the comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function onwerTransferComment($userId, $newOwnerShip)
	{
		$db = EB::db();

		$query = 'UPDATE `#__easyblog_comment`';
		$query .= ' SET `created_by` = ' . $db->Quote($newOwnerShip);
		$query .= ' WHERE `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Removed assigned user acl group
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeAssignedACLGroup($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_acl_group`';
		$query .= ' WHERE `content_id` = ' . $db->Quote($userId);
		$query .= ' AND `type` = ' . $db->Quote('assigned');

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Removed adsense configuration
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeAdsenseSetting($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_adsense`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove any feedburner configuration of the user
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeFeedburnerSetting($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_feedburner`';
		$query .= ' WHERE `userid` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove oauth settings that related to autoposting
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeOAuthSetting($userId)
	{
		$db = EB::db();

		// removing oauth posts
		$query = 'DELETE FROM `#__easyblog_oauth_posts`';
		$query .= ' WHERE `oauth_id` IN (';
		$query .= ' select `id` from `#__easyblog_oauth` where `user_id` = ' . $db->Quote($userId);
		$query .= ')';

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}

		// removing oauth
		$query = 'DELETE FROM `#__easyblog_oauth`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove this blogger from the featured listing
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeFeaturedBlogger($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_featured`';
		$query .= ' WHERE `content_id` = ' . $db->Quote($userId);
		$query .= ' AND `type` = ' . $db->Quote('blogger');

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove this author from all teams
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeTeamBlogUser($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_team_users`';
		$query .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * When a user is deleted from the site, we should also remove all subscriptions
	 * related to the user
	 *
	 * @since	5.3.3
	 * @access	public
	 */
	public function removeSubscriptions($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_subscriptions` WHERE `user_id`=' . $db->Quote($userId);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * When a user is deleted from the site, we should also remove all email notification
	 * related to the user
	 *
	 * @since	5.4.6
	 * @access	public
	 */
	public function removeEmailNotifications($user)
	{
		if (!isset($user['email']) && !$user['email']) {
			return;
		}

		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_mailq` WHERE `recipient` = ' . $db->Quote($user['email']);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Remove blogger subscription
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeBloggerSubscription($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` = ' . $db->Quote($userId);
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Remove all the author data from easyblog table
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeEasyBlogUser($userId)
	{
		$db = EB::db();

		$query = 'DELETE FROM `#__easyblog_users`';
		$query .= ' WHERE `id` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			throw EB::exception($db->stderr(), 500);
		}
	}

	/**
	 * Displays a subscribe to blog checkbox field.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function onContentPrepareData($context , $data)
	{
		if (!$this->exists()) {
			return true;
		}

		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile'))) {
			return true;
		}

		return true;
	}


	/**
	 * Displays necessary fields for EasyBlog.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!$this->exists()) {
			return true;
		}

		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return true;
		}

		if (!$this->params->get('show_subscribe', false)) {
			return true;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'))) {
			return true;
		}

		JFactory::getLanguage()->load('plg_easyblogusers' , JPATH_ROOT . '/administrator/');

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/profiles');
		$state = $form->loadFile('easyblog', false);

		return true;
	}
}
