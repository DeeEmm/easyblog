<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableReactionHistory extends EasyBlogTable
{
	public $id = null;
	public $post_id = null;
	public $reaction_id = null;
	public $user_id = null;
	public $token_id = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_reactions_history', 'id', $db);
	}

	/**
	 * Retrieves the post object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPost()
	{
		$post = EB::post($this->post_id);

		return $post;
	}

	/**
	 * Retrieves the user object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUser()
	{
		$user = EB::user($this->user_id);

		return $user;
	}
}
