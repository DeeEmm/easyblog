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

class EasyBlogViewComments extends EasyBlogView
{
	/**
	 * Processes comment saving
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Test if user is really allowed to post comments
		if (!$this->acl->get('allow_comment')) {
			return $ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_POST_COMMENT'));
		}

		// Default values
		$moderated = false;
		$parentId = $this->input->get('parentId', 0, 'int');
		$depth = $this->input->get('depth', 0, 'int');
		$subscribe = $this->input->get('subscribe', false, 'bool');
		$email = $this->input->get('email', '', 'email');
		$message = $this->input->get('comment', '', 'default');
		$name = $this->input->get('name', '', 'default');
		$title = $this->input->get('title', '', 'default');
		$terms = $this->input->get('terms', false, 'bool');
		$blogId = $this->input->get('blogId', 0, 'int');
		$isCB = $this->input->get('iscb', 0, 'int');
		$website = $this->input->get('website', '', 'default');

		// If there is no name, and the current user is logged in, use their name instead
		if (!$name && $this->my->id) {
			$user = EB::user($this->my->id);
			$name = $user->getName();
		}

		// Validate the email
		$data = [
			'post_id' => $blogId,
			'comment' => $message,
			'title' => $title,
			'email' => $email,
			'name' => $name,
			'terms' => $terms,
			'url' => $website
		];

		// Load up comment table
		$comment = EB::table('Comment');
		$state = $comment->validatePost($data);

		if (!$state) {
			return $this->ajax->reject($comment->getError());
		}

		// Bind the data on the comment table now
		$comment->bindPost($data);

		// Check for spams
		if ($comment->isSpam()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_SPAM_DETECTED_IN_COMMENT'));
		}

		$captchaResponse = EB::captcha()->verify();

		// Perform captcha verification
		if (isset($captchaResponse->success) && $captchaResponse->success == false) {
			return $this->ajax->reject($captchaResponse->errorCodes);
		}

		// Get current date
		$date = EB::date();

		// Set other attributes for the comment
		$comment->created = $date->toSql();
		$comment->modified = $date->toSql();
		$comment->published = true;
		$comment->parent_id = $parentId;
		$comment->created_by = $this->my->id;

		$totalComments = $this->input->get('totalComment', 0, 'int');


		// Determines if comment moderation is enabled
		if ($this->config->get('comment_moderatecomment') == 1 || ($this->my->guest && $this->config->get('comment_moderateguestcomment'))) {
			$comment->published = EBLOG_COMMENT_STATUS_MODERATED;
		}

		// Load up the blog table
		$blog = EB::table('Post');
		$blog->load($comment->post_id);

		// If moderation for author is disabled, ensure that the comment is also published automatically.
		if ((!$this->config->get('comment_moderateauthorcomment') && $blog->created_by == $this->my->id) || FH::isSiteAdmin()) {
			$comment->published = true;
		}

		// Update the ordering of the comment before storing
		$comment->updateOrdering();

		// Save the comment
		$state = $comment->store();

		if (!$state) {
			return $this->ajax->reject($comment->getError());
		}

		$resultMessage = JText::_('COM_EASYBLOG_COMMENTS_POSTED_SUCCESS');
		$resultState = 'success';

		if ($comment->isModerated()) {
			$resultMessage = JText::_('COM_EASYBLOG_COMMENT_POSTED_UNDER_MODERATION');
			$resultState = 'info';
		}

		// Process comment subscription
		if ($subscribe && $this->config->get('main_subscription') && $blog->subscription) {
			$subscribeModel = EB::model('Subscription');
			$subscribeModel->subscribe('blog', $blog->id, $email, $name, $this->my->id);
		}

		// Process comment notifications
		$comment->processEmails($comment->isModerated(), $blog);

		// Set the comment depth
		$comment->depth = $this->input->get('depth', 0, 'int');

		// Update the sent flag.
		// Do not update flag when saving moderated comment. We'll need to let publish to send notification. #2752
		if (!$comment->isModerated()) {
			$comment->updateSent();
		}

		// Format the comments
		$result = EB::comment()->format(array($comment));
		$comment = $result[0];

		$language = JFactory::getLanguage();
		$rtl = $language->isRTL();

		$theme = EB::themes();
		$theme->set('comment', $comment);
		$theme->set('rtl', $rtl);

		$output = '';

		if ($isCB) {
			// if the is saving from CB plugin, then we need to display the output using different template.
			$output = $theme->output('site/comments/cb.item');
		} else {
			$output = $theme->output('site/comments/default.item');
		}

		return $this->ajax->resolve($output, $resultMessage, $resultState);
	}

	/**
	 * Allows caller to reload recaptcha provided that the previous recaptcha reference
	 * is given. This is to avoid any spams on the system.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reloadCaptcha()
	{
		// If captcha has been disabled, just resolve this
		if ($this->config->get('comment_captcha_type') == 'none') {
			return $this->ajax->reject();
		}

		// Get the previous captcha id.
		$id = $this->input->get('previousId', 0, 'int');

		$captcha = EB::table('Captcha');
		$state = $captcha->load($id);

		if ($state) {
			$captcha->delete();
		}

		// Generate a new captcha
		$captcha = EB::table('Captcha');
		$captcha->response = '';
		$captcha->created = EB::date()->toSql();
		$captcha->store();

		$image = EB::_('index.php?option=com_easyblog&task=captcha.generate&tmpl=component&no_html=1&id=' . $captcha->id, false);

		return $this->ajax->resolve($image, $captcha->id);
	}

	/**
	 * Allows caller to update comments via ajax
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function update()
	{
		// Check for request forgeries
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$comment = EB::table('Comment');
		$comment->load($id);

		if (!$id || !$comment->id) {
			return $this->ajax->reject(JText::_('COM_EB_NO_PERMISSIONS'));
		}

		if ($this->acl->get('manage_comment') || ($this->acl->get('edit_comment') && $this->my->id == $comment->created_by) || FH::isSiteAdmin()) {

			// Get the updated comment
			$message = $this->input->get('message', '', 'default');

			// Get the comment title
			$title = $this->input->get('title', '', 'default');

			if (!$title && $this->config->get('comment_requiretitle')) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY'));
			}

			if (!$message) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_COMMENTS_EMPTY_COMMENT_NOT_ALLOWED'));
			}

			$comment->comment = $message;

			if (!empty($title)) {
				$comment->title = $title;
			}

			// Update the comment
			$comment->store();

			// Format the output back
			$output = nl2br($message);
			$output = EB::comment()->parseBBCode($output);

			// display the comment title as well after edited
			if (!empty($title)) {
				$output = '<b>' . $title . '</b><br />' . $output;
			}

			return $this->ajax->resolve($output, $message);
		}

		return $this->ajax->reject(JText::_('COM_EB_NO_PERMISSIONS'));
	}

	/**
	 * Confirm comment deletion
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		$comment = EB::table('Comment');
		$comment->load($id);

		// Check if the user has access to delete comments
		if (($this->my->id == 0 || $this->my->id != $comment->created_by || !$this->acl->get('delete_comment')) && !$this->acl->get('manage_comment') && !FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EB_NO_PERMISSIONS'));
		}

		// Get the return url
		$post = EB::post($comment->post_id);
		$return = base64_encode($post->getExternalPermalink(false));

		$theme = EB::themes();
		$theme->set('return', $return);
		$theme->set('comment', $comment);

		$output = $theme->output('site/comments/dialog.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to like a comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function like()
	{
		if ($this->my->guest) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get the comment id
		$id = $this->input->get('id', 0, 'int');

		// Add likes
		$model = EB::model('Comment');
		$likes = $model->like($id, $this->my->id);

		// Get the tooltip string
		$data = EB::comment()->getLikesAuthors($id, 'comment', $this->my->id);


		return $this->ajax->resolve($data->string, $data->count);
	}

	/**
	 * Allows caller to unlike a comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function unlike()
	{
		if ($this->my->guest) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get the comment id
		$id = $this->input->get('id', 0, 'int');

		// Add likes
		$model = EB::model('Comment');
		$likes = $model->unlike($id, $this->my->id);

		// Get the tooltip string
		$data = EB::comment()->getLikesAuthors($id, 'comment', $this->my->id);

		return $this->ajax->resolve($data->string, $data->count);
	}

	/**
	 * Displays the terms and condition popup
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function terms()
	{
		$text = '';

		if ($this->config->get('comment_tnc_article') && $this->config->get('comment_tnc_articleid')) {
			$article = JTable::getInstance('Content');
			$article->load((int) $this->config->get('comment_tnc_articleid'));

			$text = $article->introtext . $article->fulltext;
		}

		if (!$text) {
			$text = $this->config->get('comment_tnctext');
			$text = nl2br($text);
		}

		$theme = EB::themes();
		$theme->set('text', $text);
		$output = $theme->output('site/comments/dialog.terms');

		return $this->ajax->resolve($output);
	}

	/**
	 * Render Disqus comment system on the comment form
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getDisqusCommentHTML()
	{
		$code = $this->config->get('comment_disqus_code');

		if (!$code) {
			return '';
		}

		$theme = EB::themes();
		$theme->set('code', $code);
		$output = $theme->output('site/comments/disqus');

		return $this->ajax->resolve($output);
	}

	/**
	 * Load the replies of the parent comment
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function loadReplies()
	{
		// Get the parent comment id
		$id = $this->input->get('id', 0, 'int');

		$parent = EB::table('Comment');
		$parent->load($id);

		if (!$parent->id) {
			return $this->ajax->reject(JText::_('COM_EB_COMMENTS_INVALID_PARENT_COMMENT_ID'));
		}

		$options = array();
		$options['replies'] = true;
		$options['lft'] = $parent->lft;
		$options['rgt'] = $parent->rgt;

		$model = EB::model('Blog');
		$replies = $model->getBlogComment($parent->post_id, 0, 'asc', false, $options);

		$language = JFactory::getLanguage();
		$rtl = $language->isRTL();

		$html = "";

		foreach ($replies as $reply) {
			$theme = EB::themes();
			$theme->set('comment', $reply);
			$theme->set('rtl', $rtl);
			$html .= $theme->output('site/comments/default.item');
		}

		return $this->ajax->resolve($html);
	}
}
