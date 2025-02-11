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

// html template used in GDPR html generator
require_once(dirname(__FILE__) . '/types/template.php');

jimport('joomla.filesystem.archive');

class EasyBlogGdpr extends EasyBlog
{
	/**
	 * Method used by cron process
	 * @since 5.2.0
	 * @access public
	 */
	public function cron()
	{
		if (! $this->config->get('gdpr_enabled')) {
			return EB::exception(JText::_('COM_EB_GDPR_CRON_NO_RECORDS'), EASYBLOG_MSG_INFO);
		}

		// get records from request table.
		$model = EB::model('download');
		$items = $model->getCronDownloadReq();

		if (!$items) {
			// nothing to process
			return EB::exception(JText::_('COM_EB_GDPR_CRON_NO_RECORDS'), EASYBLOG_MSG_INFO);
		}

		$processed = 0;

		foreach ($items as $item) {
			$tbl = EB::table('download');
			$tbl->bind($item);

			// lock this request 1st.
			$tbl->updateState(EASYBLOG_DOWNLOAD_REQ_LOCKED);

			// Retrieve the params
			$params = EB::registry($tbl->params);

			// check if this user is valid or not.
			$user = EB::user($tbl->userid);

			if ($user->id) {
				$params = $this->process($user->id, $params);

				// this mean the process require next cycle to continue to to large data. lets mark this request as process
				if (!$params->get('complete')) {
					$tbl->updateState(EASYBLOG_DOWNLOAD_REQ_PROCESS);
				} else {

					// update state to ready
					$tbl->setFilePath($params->get('path'));
					$tbl->updateState(EASYBLOG_DOWNLOAD_REQ_READY);

					// prepare email and send notification to user.
					$tbl->sendNotification();

					$processed++;
				}
			}

			$tbl->params = $params->toString();
			$tbl->store();
		}

		$msg = JText::_('User data download requests processed and an email notification has sent to user.');

		if (!$processed) {
			$msg = JText::_('User data download is currently queued for next processing.');
		}

		return EB::exception(JText::_('User data download requests processed and an email notification has sent to user.'), EASYBLOG_MSG_INFO);
	}

	/**
	 * Method to process user data for download
	 *
	 * @since 5.2.0
	 * @access public
	 */
	public function process($userId, $params, $archive = true)
	{
		// here we control the ordering of the items appear in the index.html
		$items = array('post', 'comment', 'category', 'tag', 'subscription');

		// debug
		// $items = array('comment', 'category', 'tag', 'subscription');

		$data = array();

		$user = EB::user($userId);
		$complete = true;

		foreach ($items as $type) {
			$file = dirname(__FILE__) . '/types/' . $type . '.php';

			if (JFile::exists($file)) {

				// Always set to true first
				$params->set($type . '.status', true);

				require_once($file);
				$class = 'EasyBlogGdpr' . ucfirst($type);
				$adapter = new $class($userId, $params);

				$data[$type] = $adapter->execute();

				// // Since post return both post and media, we need to separate it
				if ($type == 'post') {
					$data['media'] = $data[$type]['media'];
					$data['post'] = $data[$type]['post'];
				}

				// Update the params
				$params = $adapter->params;

				// Determine if the process is not yet complete from the adapter
				if (!$params->get($type . '.status')) {
					$complete = false;
				}
			}
		}

		// We need to remove space to avoid invalid zip file
		$userName = str_replace(' ', '_', $user->user->name);

		$folderName = $userName . '_' . $user->id;
		$folderPath = JPATH_ROOT . '/tmp/' . $folderName;

		if (!JFolder::exists($folderPath)) {
			JFolder::create($folderPath);
		}

		// build the html files
		$this->generateHTML($user, $folderPath, $data);

		$actualFilePath = $folderPath;

		if ($complete) {
			// zip up the folder.
			$zipped = $this->createZipFile($folderPath, $folderName . '.zip');

			if ($zipped) {
				$actualFilePath = $zipped;
			}
		}

		// Tell the library the status of the process
		$params->set('complete', $complete);
		$params->set('path', $actualFilePath);

		return $params;
	}

	/**
	 * Method to generate the process data into html file
	 * @since 5.2.0
	 * @access public
	 */
	protected function generateHTML($user, $path, $data)
	{
		// Load front end's language file.
		EB::loadLanguages();

		// get tabs
		$tabs = array();

		foreach ($data as $type => $items) {
			$tabs[] = $type;
		}

		$theme = EB::themes();

		$theme->set('tabs', $tabs);
		$namespace = 'site/gdpr/sidebar';
		$sidebar = $theme->output($namespace);
		$theme->set('sidebar', $sidebar);

		// generating sub html files
		foreach ($data as $type => $items) {

			$content = '';

			if ($items) {
				foreach ($items as $item) {
					$preview = $item->preview;

					if ($item->link) {
						$preview = '<a href="' . $item->link . '">' . $item->preview . '</a>';

						if ($item->content) {
							$this->createHtmlFile($path, $item->link, $sidebar, $item->content, true);
						}

						if ($item->media) {
							$this->createMediaFile($path, $item->link, $item->media);
						}
					}

					$content .= '<div>' . $preview . '</div>';
				}
			}

			// Get the wrapper contents
			$filename = $type . '.html';
			$this->createHtmlFile($path, $filename, $sidebar, $content, true);
		}

		// user infos
		// Get meta info for this blogger
		$metasModel = EB::model('Metas');
		$meta = $metasModel->getMetaInfo(META_TYPE_BLOGGER, $user->id);

		// Get user's adsense code
		$adsense = EB::table('Adsense');
		$adsense->load($user->id);

		// Get feedburner data
		$feedburner	= EB::table('Feedburner');
		$feedburner->load($user->id);

		$theme->set('user', $user);
		$theme->set('userParams', $user->getParams());
		$theme->set('userMeta', $meta);
		$theme->set('userAdsense', $adsense);
		$theme->set('userFeedburner', $feedburner);
		$mainContent = $theme->output('site/gdpr/info');

		$mainFile = 'index.html';

		$this->createHtmlFile($path, $mainFile, $sidebar, $mainContent, false);

		return true;
	}

	/**
	 * Internal function used to zip up the processed folder
	 * @since  5.2
	 * @access public
	 */
	private function createZipFile($sourceFolder, $filename)
	{
		// Need to check if the download folder exists or not.
		if (!JFolder::exists(EBLOG_GDPR_DOWNLOADS)) {
			JFolder::create(EBLOG_GDPR_DOWNLOADS);
		}

		$destination = EBLOG_GDPR_DOWNLOADS . '/' . $filename;

		// Check if existing zip file exists or not.
		if (JFile::exists($destination)) {
			JFile::delete($destination);
		}

		$sourceFolder = str_ireplace(['\\' ,'/'] , '/' , $sourceFolder);

		// Get all files from
		$files = JFolder::files($sourceFolder, '', true, true);

		$data = [];

		if ($files) {
			foreach ($files as $file) {
				$file = str_ireplace(['\\' ,'/'] , '/' , $file);

				$tmp = [];
				$tmp['name'] = str_replace($sourceFolder, '', $file);
				$tmp['data'] = file_get_contents($file);
				$tmp['time'] = filemtime($file);
				$data[] = $tmp;
			}
		}

		$zip = JArchive::getAdapter('zip');

		// Now create the zip file
		$state = $zip->create($destination, $data);

		if ($state) {
			// Now delete from the tmp folder
			JFolder::delete($sourceFolder);

			return $destination;
		}

		return false;
	}

	/**
	 * internal function to create html file.
	 * @since 5.2
	 * @access private
	 */
	private function createHtmlFile($path, $filename, $sidebar, $content, $hasBack = false)
	{
		$theme = EB::themes();
		$baseUrl = '';

		// check if filename as subfolders or not.
		// if yes, then we need to check if sub folders exists.
		if (strpos($filename, '/') !== false) {
			$segments = explode('/', $filename);

			// remove the last segments as we know that is the filename.
			array_pop($segments);

			if ($segments) {

				$baseUrl = '../';

				foreach ($segments as $segment) {
					$fullpath = $path . '/' . $segment;

					if (!JFolder::exists($fullpath)) {
						JFolder::create($fullpath);
					}
				}
			}
		}

		$theme->set('baseUrl', $baseUrl);
		$theme->set('sidebar', $sidebar);
		$theme->set('contents', $content);
		$theme->set('hasBack', $hasBack);
		$output = $theme->output('site/gdpr/template');

		$filepath = $path . '/' . $filename;
		JFile::write($filepath, $output);
	}

	/**
	 * Save binary content to correct format
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function createMediaFile($path, $filename, $source)
	{
		$segments = explode('/', $filename);

		// remove the last segments as we know that is the filename.
		array_pop($segments);
		$fullpath = $path;

		if ($segments) {

			foreach ($segments as $segment) {
				$fullpath .= '/' . $segment;

				if (!JFolder::exists($fullpath)) {
					JFolder::create($fullpath);
				}
			}
		}

		$destination = $path . '/' . $filename;

		if (JFile::exists($source)) {
			$state = JFile::copy($source, $destination);
		}
	}

	/**
	 * Method used to delete expired data from download request.
	 * @since  5.2
	 * @access public
	 */
	public function purgeExpired($max = 10)
	{
		$model = EB::model('download');
		$items = $model->getExpiredRequest($max);

		if ($items) {
			foreach ($items as $item) {
				$tbl = EB::table('download');
				$tbl->bind($item);

				$tbl->delete();
			}
		}

		return true;
	}

	/**
	 * Remove user's personal details from the site
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function removeUserDetails($user)
	{
		$states = array();

		// Delete all user's comments
		$states[] = EB::model('Comments')->deleteUserComments($user->id);

		// Delete all user's posts
		$states[] = EB::model('blog')->deleteUserPosts($user->id);

		// Delete all user's tags
		$states[] = EB::model('Tags')->deleteUserTags($user->id);

		// Delete all user's subscriptions
		$states[] = EB::model('Subscriptions')->deleteUserSubscriptions($user->id);

		// Delete all subscriptions to this user
		$states[] = EB::model('Subscriptions')->deleteSubscriptions($user->id, EBLOG_SUBSCRIPTION_BLOGGER);

		// Delete user from Easyblog user table
		$states[] = EB::model('Users')->removeEasyBlogUser($user->id);

		// Delete user from Easyblog user email activities
		$states[] = EB::model('spools')->removeUserEmailActivities($user->email);

		if (in_array(false, $states)) {
			return false;
		}

		return true;
	}

	/**
	 * Remove guest's personal details from the site
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function removeGuestDetails($email)
	{
		$states = array();

		// Delete all user's comments
		$states[] = EB::model('Comments')->deleteUserComments($email, 'email');

		// Delete all user's subscriptions
		$states[] = EB::model('Subscriptions')->deleteUserSubscriptions($email, 'email');

		// Delete user from Easyblog user email activities
		$states[] = EB::model('spools')->removeUserEmailActivities($email);

		if (in_array(false, $states)) {
			return false;
		}

		return true;
	}


}
