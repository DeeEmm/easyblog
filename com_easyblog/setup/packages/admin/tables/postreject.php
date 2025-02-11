<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTablePostReject extends EasyBlogTable
{
	public $id = null;
	public $post_id = null;
	public $created_by = null;
	public $message	= null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct( '#__easyblog_post_rejected' , 'id' , $db );
	}

	/**
	 * Retrieves the author that created this reject
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAuthor()
	{
		$user = EB::user($this->created_by);
		return $user;
	}


	/**
	 * Override the parent's store behavior
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		// @task: Load language file from the front end.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// Clear any previous messages
		$model = EB::model('PostReject');
		$model->clear($this->post_id);

		return parent::store();
	}

	/**
	 * Notifies the author when a post is rejected
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function notify()
	{
		$post = EB::post();
		$post->load($this->post_id);

		// Get the author
		$author = $this->getAuthor();

		$data = array();
		$data['blogTitle'] = $post->title;
		$data['blogAuthor'] = $author->getName();
		$data['blogAuthorAvatar'] = $author->getAvatar();
		$data['blogLink'] = $post->getExternalPermalink();
		$data['blogEditLink'] = $post->getEditLink(false, true);
		$data['blogAuthorEmail'] = $author->user->email;
		$data['rejectMessage'] = $this->message;

		$subject = JText::_('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_REJECTED');

		$obj = new stdClass();
		$obj->unsubscribe = false;
		$obj->email = $author->user->email;

		$emails = array($obj);

		$notification = EB::notification();
		return $notification->send($emails, $subject , 'post.rejected', $data);
	}
}
