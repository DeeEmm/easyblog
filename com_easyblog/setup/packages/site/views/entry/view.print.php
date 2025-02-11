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

class EasyBlogViewEntry extends EasyBlogView
{
	/**
	 * Main display for the blog entry view
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('id', 0, 'int');

		// Load the blog post now
		$post = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->guest && $this->config->get('main_login_read')) {
			return EB::requireLogin();
		}

		// Check if blog is password protected.
		if ($this->isProtected($post) !== false) {
			return;
		}

		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($post->isTrashed()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Check if the blog post is trashed
		if (!$post->isPublished() && $this->my->id != $post->created_by && !FH::isSiteAdmin()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Check for team's privacy
		$allowed = $this->checkTeamPrivacy($post);

		if ($allowed === false) {
			throw EB::exception(JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY'), 404);
		}

		// Check if the blog post is accessible.
		$accessible = $post->isAccessible();

		if (!$accessible->allowed) {
			echo $accessible->error;

			return;
		}

		// Increment the hit counter for the blog post.
		$post->hit();

		// Format the post
		$post = EB::formatter('entry', $post);
		$content = $post->getContent('entry', true, true);

		// Fix issue with missing slashes on the image relative path. #2965
		$content = EB::string()->relAddSlashes($content);

		// Determine whether current view is mobile or not
		$isMobile = EB::responsive()->isMobile();

		$themes = EB::themes();
		$themes->set('post', $post);
		$themes->set('content', $content);
		$themes->set('isMobile', $isMobile);
		$themes->set('requireLogin', false);

		$output = $themes->output('site/entry/default/default.print');
		echo $output;
		exit;
	}

	/**
	 * Determines if the user is allowed to view this post if this post is associated with a team.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function checkTeamPrivacy(EasyBlogPost &$blog)
	{
		$id = $blog->getTeamAssociation();

		// This post is not associated with any team, so we do not need to check anything on the privacy
		if (!$id) {
			return true;
		}

		$team = EB::table('TeamBlog');
		$team->load($id);

		// If the team access is restricted to members only
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember($this->my->id) && !FH::isSiteAdmin()) {
			return false;
		}

		// If the team access is restricted to registered users, ensure that they are logged in.
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED && $this->my->guest) {
			echo EB::showLogin();

			return false;
		}

		return true;
	}

	/**
	 * Determines if the current post is protected
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isProtected(EasyBlogPost $post)
	{
		if (!$this->config->get('main_password_protect') || !$post->isPasswordProtected() || FH::isSiteAdmin() || $post->verifyPassword()) {
			return false;
		}

		return true;
	}
}
