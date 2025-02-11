<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableRatings extends EasyBlogTable
{
	public $id = null;
	public $uid = null;
	public $type = null;
	public $created_by = null;
	public $sessionid = null;
	public $value = null;
	public $ip = null;
	public $published = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_ratings', 'id', $db);
	}

	/**
	 * Determines if the user has already voted
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasVoted($userId, $postId, $type, $hash = '')
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName($this->_tbl);
		$query[] = 'WHERE ' . $db->quoteName('created_by') . '=' . $db->Quote($userId);
		$query[] = 'AND ' . $db->quoteName('uid') . '=' . $db->Quote($postId);

		if (!empty($hash)) {
			$query[] = 'AND ' . $db->quoteName('sessionid') . '=' . $db->Quote($hash);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		return $result;
	}

	/**
	 * Saves a new rating item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		$config = EB::config();
		$state = parent::store();

		if ($this->type == 'entry' && $this->created_by) {

			// Load the post item
			$post = EB::post($this->uid);

			// Get the author of the post
			$author = $post->getAuthor();

			// Get the link to the post
			$link = $post->getExternalPermalink();

			// Notify EasySocial users that someone rated their post
			EB::easysocial()->notifySubscribers($post, 'ratings.add');

			// Assign EasySocial points
			EB::easysocial()->assignPoints('blog.rate');
			EB::easysocial()->assignPoints('blog.rated', $post->created_by);

			// Assign badge for users that report blog post.
			// Only give points if the viewer is viewing another person's blog post.
			EB::easysocial()->assignBadge('blog.rate', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_RATED_BLOG'));

			// Assign point for altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_rate_blog', $post->created_by, JText::_('COM_EASYBLOG_AUP_BLOG_RATED'));

			// Add notifications for easydiscuss
			if ($config->get('integrations_jomsocial_notification_rating')) {
				$target	= array($post->created_by);

				EB::easydiscuss()->addNotification($post,
									JText::sprintf('COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_RATING_FOR_YOUR_BLOG', $author->getName(), $post->title),
									EBLOG_NOTIFICATIONS_TYPE_RATING,
									array($post->created_by),
									$this->created_by,
									$link);
			}
		}

		return $state;
	}
}
