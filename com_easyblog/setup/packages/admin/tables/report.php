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

require_once(__DIR__ . '/table.php');

class EasyBlogTableReport extends EasyBlogTable
{
	public $id = null;
	public $obj_id = null;
	public $obj_type = null;
	public $created_by = null;
	public $created	= null;
	public $reason = null;
	public $ip = null;
	private $author = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_reports', 'id', $db);
	}

	public function getAuthor()
	{
		if (!isset($this->author) || is_null($this->author)) {
			$this->author = EB::user($this->created_by);
		}

		return $this->author;
	}

	/**
	 * Retrieve the permalink to the comment
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getPermalink($xhtml = false)
	{
		$post = EB::post($this->obj_id);
		$url = $post->getPermalink();

		return $url;
	}

	public function store( $updateNulls = false )
	{
		$config 	= EB::config();
		$maxTimes 	= $config->get( 'main_reporting_maxip' );

		// @task: Run some checks on reported items and
		if( $maxTimes > 0 )
		{
			$db 	= EB::db();
			$query 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . $db->nameQuote( 'obj_id' ) . ' = ' . $db->Quote( $this->obj_id ) . ' '
					. 'AND ' . $db->nameQuote( 'obj_type' ) . ' = ' . $db->Quote( $this->obj_type ) . ' '
					. 'AND ' . $db->nameQuote( 'ip' ) . ' = ' . $db->Quote( $this->ip );

			$db->setQuery( $query );
			$total 	= (int) $db->loadResult();

			if( $total >= $maxTimes )
			{
				EB::loadLanguages();
				JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
				$this->setError( JText::_( 'COM_EASYBLOG_REPORT_ALREADY_REPORTED' ) );
				return false;
			}
		}

		// Assign badge for users that report blog post.
		// Only give points if the viewer is viewing another person's blog post.
		EB::easysocial()->assignBadge( 'blog.report' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_REPORT_BLOG' ) );

		return parent::store();
	}

	/**
	 * Notifies the site owners when a new report is made on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function notify(EasyBlogPost &$post)
	{
		$config = EB::config();

		// Send notification to site admins when a new blog post is reported
		$data = array();
		$data['blogTitle'] = $post->title;
		$data['blogLink'] = $post->getExternalPermalink();

		// Get the author of this reporter
		$author = $this->getAuthor();

		$data['reporterAvatar'] = $author->getAvatar();
		$data['reporterName'] = $author->getName();
		$data['reporterLink'] = $author->getProfileLink();
		$data['reason'] = $this->reason;
		$data['reportDate'] = EB::date()->format(JText::_('DATE_FORMAT_LC2'));

		// Set the title of the email
		$subject = EBString::substr($post->title, 0, $config->get('main_mailtitle_length'));
		$subject = JText::sprintf('COM_EASYBLOG_EMAIL_TITLE_NEW_REPORT', $subject) . ' ...';

		// Get the notification library
		$notification = EB::notification();
		$recipients = array();

		// Fetch custom emails defined at the back end.
		if ($config->get('notification_blogadmin')) {

			if ($config->get('custom_email_as_admin')) {
				$notification->getCustomEmails($recipients);
			} else {
				$notification->getAdminEmails($recipients);
			}
		}

		if (!empty($recipients)) {
			$notification->send($recipients, $subject, 'post.reported', $data);
		}
	}
}
