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

class EasyBlogRegistration
{
	public function validate($options = array())
	{
		$db = EB::db();

		$returnStr  = '';
		$query 		= '';

		foreach($options as $opt => $val)
		{
			$query  = 'SELECT COUNT(1) FROM `#__users` WHERE UPPER(' . $db->nameQuote($opt) . ') = UPPER(' . $db->Quote($val) . ')';
			$db->setQuery($query);

			$result = $db->loadResult();

			if($result > 0)
			{
				switch(strtolower($opt))
				{
					case 'username':
						$returnStr  = JText::sprintf('COM_EASYBLOG_REGISTRATION_USERNAME_ALREADY_EXISTS', $val);
						break;
					case 'email':
						$returnStr  = JText::sprintf('COM_EASYBLOG_REGISTRATION_EMAIL_ALREADY_EXISTS', $val);
						break;
					default:
						$returnStr  = JText::sprintf('COM_EASYBLOG_REGISTRATION_SOMETHING_ALREADY_EXISTS', $opt);
						break;
				}

				return $returnStr;
			}
		}

		return true;
	}

	public function addUser($values)
	{
		$userComponent = 'com_users';
		$config = EB::config();
		$usersConfig = JComponentHelper::getParams('com_users');

		if (!$usersConfig->get('allowUserRegistration')) {
			return JText::_('COM_EASYBLOG_REGISTRATION_DISABLED');
		}

		$username = $values['username'];
		$email = $values['email'];
		$fullname = $values['name'];
		$password = isset($values['password']) ? $values['password'] : '';

		$mainframe = JFactory::getApplication();
		$jConfig = EB::jconfig();
		$authorize = JFactory::getACL();
		$document = JFactory::getDocument();

		$user = clone(JFactory::getUser());

		if (empty($password)) {
			$password = $username . '123';
		}

		$newUsertype = $usersConfig->get('new_usertype', 2);
		$userArr = [
						'username' => $username,
						'name' => $fullname,
						'email' => $email,
						'password' => $password,
						'password2'	=> $password,
						'groups' => array($newUsertype),
						'gid' => '0',
						'id' => '0'
					];

		if (!$user->bind($userArr, 'usertype')) {
			return $user->getError();
		}

		$date = EB::date();
		$user->set('registerDate', $date->toSql());

		//check if user require to activate the acct
		$useractivation = $usersConfig->get('useractivation');

		if ($useractivation == '1' || $useractivation == '2') {
			jimport('joomla.user.helper');
			$user->set('activation', md5( JUserHelper::genRandomPassword()));
			$user->set('block', '1');
		}

		JPluginHelper::importPlugin('user');
		$user->save();

		// Send registration confirmation mail
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email

		//load com_user language file
		$lang = JFactory::getLanguage();
		$lang->load('com_users');

		// Get the user id.
		$userId = $user->id;

		$this->sendMail($user, $password);

		return $userId;
	}

	private function sendMail(&$user, $password)
	{
		$mainframe	= JFactory::getApplication();
		$db			= EB::db();
		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= JComponentHelper::getParams('com_users');
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= JText::sprintf( 'COM_EASYBLOG_REGISTER_MAIL_ACCOUNT_DETAILS', $name, $sitename );
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ($useractivation == 1) {
			$task = 'registration.activate';
			$key = 'token';

			$message = sprintf ( JText::_( 'COM_EASYBLOG_REGISTER_MAIL_ACTIVATE' ), $name, $sitename, $siteURL."index.php?option=com_users&task=".$task."&".$key."=".$user->get('activation'), $siteURL, $username, $password);
		}
		else
		{
			$message = sprintf ( JText::_( 'COM_EASYBLOG_REGISTER_MAIL' ), $name, $sitename, $siteURL, $username, $password);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		$ids = EB::getSAUsersIds();
		$rows = array();

		foreach ($ids as $id) {
			$row = new stdClass();

			$user = JFactory::getUser($id);
			$row->name 	= $user->name;
			$row->email	= $user->email;
			$row->sendEmail = $user->sendEmail;

			$rows[] 	= $row;
		}

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$mail = JFactory::getMailer();
		$mail->sendMail($mailfrom, $fromname, $email, $subject, $message, true);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ($rows as $row) {
			if ($row->sendEmail) {
				$message2 = sprintf ( JText::_( 'COM_EASYBLOG_REGISTER_MAIL_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);

				$mail = JFactory::getMailer();
				$mail->sendMail($mailfrom, $fromname, $row->email, $subject2, $message2, true);
			}
		}
	}
}
