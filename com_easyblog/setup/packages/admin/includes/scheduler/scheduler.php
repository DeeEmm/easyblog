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

class EasyBlogScheduler extends EasyBlog
{
	/**
	 * Processes scheduled blog posts to be unpublished
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		$model = EB::model('Blogs');

		$items = $model->getUnpublishScheduledPosts();

		if ($items) {

			foreach ($items as $item) {
				$post = EB::post($item->id, $item->created_by);

				$options = [
					'isScheduleProcess' => true
				];

				$post->unpublish($options);
			}

			// TODO: Turn this into language string.
			return EB::exception('Executed query to process posts to be unpublished.', EASYBLOG_MSG_SUCCESS);
		}
	}

	/**
	 * Processes scheduled blog posts to be published
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish()
	{
		// Retrieve a list of scheduled posts
		$model = EB::model('Blogs');
		$items = $model->getScheduledPosts();

		if (!$items) {
			return EB::exception('No scheduled posts to process currently.', EASYBLOG_MSG_INFO);
		}

		foreach ($items as $item) {
			$post = EB::post($item->id, $item->created_by);

			// We know that all scheduled posts regardless if the author has access to publish or not should be published.
			// Since pending posts that are being scheduled needs to be approved by the admin first.
			$options = [
				'checkAcl' => false,
				'isScheduleProcess' => true
			];

			try {
				$post->publish($options, true);
			} catch(Exception $e) {

			}
		}
	}

	/**
	 * Process auto posting reposts for blog posts
	 *
	 * @since	5.2.8
	 * @access	public
	 */
	public function reposting()
	{

		$model = EB::model('Blogs');
		$items = $model->getCategoryRepostingPosts();

		if (!$items) {
			return EB::exception('COM_EB_SCHEDULER_CATEGORY_REPOSTING_NO_POSTS', EASYBLOG_MSG_INFO);
		}

		$ids = array();
		$autopostCount = 0;
		foreach ($items as $item) {

			$post = EB::post($item->id);

			try {

				// we only auto post to centralized social providers
				EB::autoposting()->shareSystem($post, null, true, true);
				$ids[] = $item->id;

				$autopostCount++;
			} catch(Exception $e) {

			}
		}

		// now update the reautopost flag so that the next cycle will not repick these data.
		$model->updateCategoryRepostingFlag($ids);

		return EB::exception(JText::sprintf('COM_EB_SCHEDULER_CATEGORY_REPOSTING_SUCCESS', $autopostCount), EASYBLOG_MSG_SUCCESS);
	}

	/**
	 * Processes scheduled autopost
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function autopost()
	{
		$model = EB::model('Blogs');
		$items = $model->getScheduledAutoposts();

		if (!$items) {
			return EB::exception('COM_EB_SCHEDULER_NO_SCHEDULED_AUTOPOST', EASYBLOG_MSG_INFO);
		}

		$autopostCount = 0;
		foreach ($items as $item) {
			$post = EB::post($item->id);

			try {
				$post->autopost('schedule', true);

				$autopostCount++;
			} catch(Exception $e) {

			}
		}

		return EB::exception(JText::sprintf('COM_EB_SCHEDULER_SCHEDULED_AUTOPOSTED_SUCCESS', $autopostCount), EASYBLOG_MSG_SUCCESS);
	}

	/**
	 * Processes the garbage collector
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeTmpFiles()
	{
		$db = EB::db();
		$now = EB::date()->toSql();

		// Define how long the file will be in database before it gets deleted.
		$minutes = 120;

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_uploader_tmp') . ' '
				. 'WHERE ' . $db->quoteName('created') . ' <= DATE_SUB(' . $db->Quote($now). ' , INTERVAL ' . $minutes . ' MINUTE)';

		$db->setQuery($query);
		$file = $db->loadObject();

		if ($file) {
			$query	= 'DELETE FROM ' . $db->nameQuote('#__easyblog_uploader_tmp') . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' = ' . $file->id;

			$db->setQuery($query);
			$db->query();

			JFile::delete(JPATH_ROOT . '/' . $file->path);

			return EB::exception('Temp files removed', EASYBLOG_MSG_INFO);
		}
	}

	/**
	 * Processes the garbage collector on blank posts that already passed 3 days
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeBlankPosts()
	{
		$db = EB::db();
		$now = EB::date()->toSql();

		// Define how long the file will be in database before it gets deleted.
		$days = 3;

		$queryId = 'SELECT a.`id`';
		$queryDelete = 'DELETE a, b';

		$query = ' FROM ' . $db->qn('#__easyblog_post') . ' as a';
		$query .= '	inner join ' . $db->qn('#__easyblog_revisions') . ' as b on a.' . $db->qn('id') . ' = b.' . $db->qn('post_id');
		$query .= ' where a.' . $db->qn('published') . ' = ' . $db->Quote(EASYBLOG_POST_BLANK);
		$query .= ' and a.' . $db->qn('created') . ' <= DATE_SUB(' . $db->Quote($now) . ', INTERVAL ' . $days . ' DAY)';

		$queryId = $queryId . $query;
		$queryDelete = $queryDelete . $query;

		// Get lists of id first
		$db->setQuery($queryId);
		$rows = $db->loadObjectList();

		// Relocate all media files from blank post to user folder
		if ($rows) {
			foreach ($rows as $row) {
				$post = EB::post($row->id);

				// Relocate media file
				$post->relocateMediaFiles();
			}
		}

		$db->setQuery($queryDelete);
		$db->query();

		return EB::exception('Blank posts removed', EASYBLOG_MSG_INFO);
	}

	/**
	 * Processes the garbage collector on expired download request
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function purgeExpiredDownload()
	{
		if (!$this->config->get('gdpr_enabled')) {
			return EB::exception('GDPR download disabled. Nothing to process.', EASYBLOG_MSG_INFO);
		}

		$gdpr = EB::gdpr();
		$gdpr->purgeExpired();

		return EB::exception('Expired download requests removed', EASYBLOG_MSG_INFO);
	}

	/**
	 * Process auto blog posts archiving.
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function archive()
	{
		if (!$this->config->get('main_archiving_enabled')) {
			return EB::exception('COM_EB_SCHEDULER_AUTO_ARCHIVING_DISABLED', EASYBLOG_MSG_INFO);
		}

		$model = EB::model('Blogs');
		$items = $model->getAutoAchivingPosts();

		if (!$items) {
			return EB::exception('COM_EB_SCHEDULER_AUTO_ARCHIVING_NO_POSTS', EASYBLOG_MSG_INFO);
		}

		$count = 0;

		foreach ($items as $id) {

			$post = EB::post($id);

			try {
				$post->archive();
				$count++;
			} catch(Exception $e) {

			}
		}

		return EB::exception(JText::sprintf('COM_EB_SCHEDULER_AUTO_ARCHIVING_SUCCESS', $count), EASYBLOG_MSG_SUCCESS);
	}

	/**
	 * Process uploaded images optimization
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function optimizeImages()
	{
		if (!$this->config->get('main_media_compression') || !$this->config->get('main_media_compression_key') || !$this->config->get('main_media_compression_cron')) {
			return EB::exception('COM_EB_IMAGE_OPTIMIZER_CRON_DISABLED', EASYBLOG_MSG_INFO);
		}

		$model = EB::model('Optimizer');

		// Get a list of images that are not processed by yet
		$images = $model->getPhotosToOptimize();

		if (! $images) {
			return EB::exception('COM_EB_IMAGE_OPTIMIZER_CRON_NO_IMAGES', EASYBLOG_MSG_INFO);
		}

		$count = 0;

		$optimizer = EB::imageoptimizer();

		foreach ($images as $photo) {
			try {
				$optimizer->optimizeCron($photo, $count);
			} catch(Exception $e) {
				// do nothing.
			}
		}

		return EB::exception(JText::sprintf('COM_EB_IMAGE_OPTIMIZER_CRON_IMAGES_SUCCESS', $count), EASYBLOG_MSG_SUCCESS);
	}
}
