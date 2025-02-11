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

class EasyBlogAvatar extends EasyBlog
{

	public function getAvatarURL($profile, $fromOpengraph = false)
	{
		// Get the avatar type
		$type = $this->config->get('layout_avatarIntegration');

		$class = 'EasyBlogAvatar' . ucfirst($type);

		if (!$this->loadLibrary($type)) {
			return false;
		}

		// Try to get the avatar
		$obj = new $class();

		// so far only 2 component get the current logged in user avatar when the comment avatar as guest
		if (($class == 'EasyBlogAvatarEasysocial' || $class == 'EasyBlogAvatarJomsocial') && $profile->id == null) {

			// If we get a failed status, just render default avatar
			$this->loadLibrary('default');
			$obj = new EasyBlogAvatarDefault();
			$link = $obj->getAvatar($profile);
			return $link;
		}

		$link = $obj->getAvatar($profile, $fromOpengraph);

		// If we get a failed status, just render default avatar
		if ($link === false) {
			$this->loadLibrary('default');
			$obj = new EasyBlogAvatarDefault();
			$link = $obj->getAvatar($profile);
		}

		return $link;
	}

	private function loadLibrary($type)
	{
		$file = __DIR__ . '/adapters/' . strtolower($type) . '/client.php';

		require_once($file);

		return true;
	}

	/**
	 * Uploads a user avatar
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function upload($fileData, $userId = false)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Check if the user is allowed to upload avatar
		$acl = EB::acl();

		// Ensure that the user really has access to upload avatar
		if (!$acl->get('upload_avatar')) {
			$this->setError('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR');
			return false;
		}

		// Get the current user
		$user = JFactory::getUser();

		// If there is userId passed, means this is from backend. 
		// We cannot get the current logged in user because it will always be the admin.
		if ($userId) {
			$user = JFactory::getUser($userId);
		}

		$app = JFactory::getApplication();
		$config = EB::config();

		$path = $config->get('main_avatarpath');
		$path = rtrim($path, '/');

		$relativePath = $path;
		$absolutePath = JPATH_ROOT . '/' . $path;

		// If the absolute path does not exist, create it first
		if (!JFolder::exists($absolutePath)) {
			JFolder::create($absolutePath);
		}

		// The file data should have a name
		if (!isset($fileData['name'])) {
			return false;
		}

		// Make sure the avatar filename do not have space
		$fileData['name'] = str_replace(' ', '_', $fileData['name']);

		// Generate a better name for the file
		$fileData['name'] = $user->id . '_' . JFile::makeSafe($fileData['name']);

		// Get the relative path
		$relativeFile = $relativePath . '/' . $fileData['name'];

		// Get the absolute file path
		$absoluteFile = $absolutePath . '/' . $fileData['name'];

		// Test if the file is upload-able
		$message = '';

		if (!EB::image()->canUpload($fileData, $message)) {

			$this->setError($message);
			return false;
		}

		// Determines if the web server is generating errors
		if ($fileData['error'] != 0) {
			$this->setError($fileData['error']);
			return false;
		}

		// We need to delete the old avatar
		$profile = EB::user($user->id);

		// Get the old avatar
		$oldAvatar = $profile->avatar;
		$isNew = false;

		// Delete the old avatar first
		if ($oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png') {
			$session	= JFactory::getSession();
			$sessionId	= $session->getToken();

			$oldAvatarPath = $absolutePath . '/' . $oldAvatar;

			if (JFile::exists($oldAvatarPath)) {
				JFile::delete($oldAvatarPath);
			}
		} else {
			$isNew = true;
		}

		$image = EB::imagelib();
		$image->load($fileData['tmp_name']);
		$image->resize(EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT, false);
		$image->save($absoluteFile);

		if ($isNew && $config->get('main_jomsocial_userpoint')) {
			EB::jomsocial()->assignPoints('com_easyblog.avatar.upload', $user->id);
		}

		return $fileData['name'];
	}
}
