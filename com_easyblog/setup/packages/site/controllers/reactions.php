<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');
require_once(dirname(dirname(dirname(__DIR__))) . '/administrator/components/com_easyblog/includes/post/post.php'); 

class EasyBlogControllerReactions extends EasyBlogController
{
	private $model = null;

	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->model = EB::model('Reactions');
	}

	/**
	 * Allows caller to save a reaction for a post
	 *
	 * @since 5.1
	 * @access public
	 **/
	public function save()
	{
		FH::checkToken();

		$postId = $this->input->get('postId', 0, 'int');
		$reactionId = $this->input->get('reactionId', 0, 'int');

		$post = EB::post($postId);
		$reactions = EB::reactions($post);

		// If the user cannot react to the post, disallow it
		if (!$reactions->enabled() || !$reactions->canReact()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));
		}

		// Update the user's state
		$data = $reactions->update($reactionId);
		$previousReactionId = $data['previousReactionId'];

		return $this->ajax->resolve($previousReactionId);
	}
}
