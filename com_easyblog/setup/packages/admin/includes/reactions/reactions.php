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

class EasyBlogReactions extends EasyBlog
{
	private $post = null;
	private $model = null;

	public function __construct(EasyBlogPost $post)
	{
		parent::__construct();

		$this->post = $post;
		$this->model = EB::model('Reactions');
	}

	/**
	 * Generates the token key that is stored on session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTokenKey()
	{
		$session = JFactory::getSession();
		$ip = $this->input->server->get('REMOTE_ADDR');

		$token = md5($session->getId() . $ip . $this->post->id);

		return $token;
	}

	/**
	 * Retrieves the user reactions to this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function enabled()
	{
		if (!$this->config->get('reactions_enabled')) {
			return false;
		}

		if (!$this->post->isPublished()) {
			return false;
		}

		return true;
	}

	/**
	 * updates current reaction counter and reset previously selected reactions if any
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function update($reactionId)
	{
		$model = EB::model('Reactions');

		// Get the token id in case this is a non logged in user
		$tokenId = $this->getTokenKey();

		// Get previous reaction by the user
		$previousReaction = $model->getUserReaction($this->post->id, $this->my->id, $tokenId);
		$previousReactionId = null;

		if ($previousReaction) {
			$previousReactionId = $previousReaction->id;
		}

		$state = $model->storeReaction($this->my->id, $tokenId, $this->post->id, $reactionId);

		if ($state) {
			// Get the reaction history
			$history = $model->getReactionHistory($this->post->id, $this->my->id, $tokenId);

			// Create stream in EasySocial
			EB::easysocial()->createReactionStream($this->post->id, $history->id);

			// Notify EasySocial users that someone reacted to their post
			EB::easysocial()->notifySubscribers($this->post, 'reaction.add');
		}

		$result = array('previousReactionId' => $previousReactionId, 'state' => $state);

		return $result;
	}

	/**
	 * Retrieve the reaction types for a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getReactions()
	{
		$reactions = $this->model->getReactions($this->post);

		return $reactions;
	}

	/**
	 * Retrieve the default reaction for a post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUserReaction()
	{
		$reaction = $this->model->getUserReaction($this->post->id, $this->my->id, $this->getTokenKey());

		return $reaction;
	}

	/**
	 * Checks admin setting to allow user react on post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canReact()
	{
		// Non members are not allowed
		if (!$this->config->get('reactions_guests') && !$this->my->id) {
			return false;
		}

		// Otherwise, we assume that everyone can react to the post
		return true;
	}

	/**
	 * Retrieves the user reactions to this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		if (!$this->enabled()) {
			return;
		}

		// Retrieve a list of available reactions on the system
		$reactions = $this->getReactions();

		// If there are no reactions, then what's the point of displaying this
		if (!$reactions) {
			return;
		}

		// Get user's reaction
		$userReaction = $this->getUserReaction();

		// Determines if the user is allowed to react to this post
		$canReact = $this->canReact();

		$theme = EB::themes();
		$theme->set('userReaction', $userReaction);
		$theme->set('post', $this->post);
		$theme->set('canReact', $canReact);
		$theme->set('reactions', $reactions);
		
		return $theme->output('site/reactions/default');
	}
}