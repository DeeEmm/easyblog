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

class EasyBlogPrivacy
{
	/**
	 * Generates a list of privacy options
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function getOptions($type = '', $userId = '')
	{
		$config = EB::config();
		$options = [];

		if ($type == 'teamblog') {
			$options['1'] = 'COM_EASYBLOG_TEAM_MEMBER_ONLY';
			$options['2'] = 'COM_EASYBLOG_ALL_REGISTERED_USERS';
			$options['3'] = 'COM_EASYBLOG_EVERYONE';

			return $options;
		}

		if ($type != 'category') {

			if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists()) {
				$options['0'] = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_ALL';
				$options['20'] = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_MEMBERS';
				$options['30'] = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_FRIENDS';
				$options['40'] = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_ONLY_ME';
			}

			$easysocial = EB::easysocial();

			if ($config->get('integrations_es_privacy') && $easysocial->exists()) {
				$my = JFactory::getUser();
				$userId = ($userId) ? $userId : $my->id;

				$privacyLib = ES::privacy($userId, 'user');
				$esOptions = $privacyLib->getOption( '0', 'blog', $userId, 'easyblog.blog.view' );

				if (isset($esOptions->option) && count($esOptions->option) > 0) {
					foreach ($esOptions->option as $optionKey => $optionVal) {
						$valueInt = $privacyLib->toValue($optionKey);
						$options[$valueInt] = 'COM_EASYBLOG_PRIVACY_EASYSOCIAL_' . strtoupper($optionKey);
					}
				}
			}
		}

		// Default values
		if (empty($options)) {
			$options['0'] = 'COM_EASYBLOG_PRIVACY_VIEWABLE_ALL';
			$options['1'] = 'COM_EASYBLOG_PRIVACY_VIEWABLE_MEMBERS';

			if ($type == 'category') {
				$options['2'] = 'COM_EASYBLOG_PRIVACY_VIEWABLE_CATEGORY_ACL';
			}
		}

		return $options;
	}

	/**
	 * Check for privacy of the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function checkPrivacy($blog)
	{
		$obj = new stdClass();
		$obj->allowed = EB::isLoggedIn();
		$obj->message = '';

		// If it is public or site amdin, always allow browser to access.
		if (!$blog->access || FH::isSiteAdmin()) {
			$obj->allowed = true;
			return $obj;
		}

		$my = JFactory::getUser();
		$config = EB::config();

		// EasySocial integrations
		if ($config->get('integrations_es_privacy') && EB::easysocial()->exists()) {

			// Post can be seen by anyone logged in
			if ($blog->access == '10') {
				$obj->allowed = EB::isLoggedIn();
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML();
			}

			// Post can only be seen by friends
			if ($blog->access == '30') {
				// if user is the blog author, we always allow.
				$obj->allowed = $my->id == $blog->created_by ? true : false;

				if (!$obj->allowed) {
					$obj->allowed = ES::user($my->id)->isFriends($blog->created_by);
				}
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML('privacy.friends');
			}

			// User can only view their own posts
			if ($blog->access == '40') {
				$obj->allowed = $my->id == $blog->created_by;
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML( 'privacy.owner' );
			}

			return $obj;
		}

		// Jomsocial integrations
		if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists()) {

			if ($blog->access == '20') {
				$obj->allowed = EB::isLoggedIn();
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML();
			}

			if ($blog->access == '30') {
				$obj->allowed = CFactory::getUser($my->id)->isFriendWith($blog->created_by);
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML('privacy.friends');
			}

			if ($blog->access == '40') {
				$obj->allowed = $my->id == $blog->created_by;
				$obj->error = $obj->allowed ? '' : $this->getErrorHTML('privacy.owner');
			}

			return $obj;
		}

		if ($blog->access) {
			$obj->allowed = EB::isLoggedIn();
			$obj->error = $obj->allowed ? '' : $this->getErrorHTML();
		}

		// If not integrated with any privacy providers, we assume that the blog
		// is private.
		return $obj;
	}

	public function getErrorHTML($type = '')
	{
		// Default text
		$text = 'COM_EASYBLOG_PRIVACY_NOT_AUTHORIZED_ERROR';

		if ($type == 'privacy.friends') {
			$text = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_FRIENDS_ERROR';
		}

		if ($type == 'privacy.owner') {
			$text = 'COM_EASYBLOG_PRIVACY_JOMSOCIAL_NOT_AUTHORIZED_ERROR';
		}

		$text = JText::_($text);

		return $text;
	}
}
