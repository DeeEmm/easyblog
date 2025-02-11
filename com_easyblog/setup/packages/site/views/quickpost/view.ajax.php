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

class EasyBlogViewQuickpost extends EasyBlogView
{
	/**
	 * Saves a quick post item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Get the quickpost type
		$type = $this->input->get('type', '', 'cmd');

		// Test if microblogging is allowed
		if (!$this->config->get('main_microblog')) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'), 'error');

			return $this->ajax->reject($exception);
		}

		// Let's test if the user is a valid user.
		if (!$this->acl->get('add_entry') || $this->my->guest) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'), 'error');

			return $this->ajax->reject($exception);
		}

		// Ensure that the type is provided otherwise we wouldn't know how to process this
		if (!$type) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_SPECIFY_POST_TYPE'), 'error');
			return $this->ajax->reject($exception);
		}

		// Check if category is set
		$category = $this->input->get('category', '', 'int');

		if (!$category) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_SELECT_CATEGORY_FOR_POST'), 'error');
			return $this->ajax->reject($exception);
		}

		// Get the quickpost object
		$quickpost = EB::quickpost()->getAdapter($type);

		if ($quickpost === false) {
			$exception = EB::exception(JText::_('COM_EASYBLOG_INVALID_POST_TYPE'), 'error');
			return $ajax->reject($exception);
		}

		// Type validations are done here
		$state = $quickpost->validate();

		if ($state !== true) {
			return $this->ajax->reject($state);
		}

		// Load up the blog object
		$data = array();
		$arrData = $this->input->getArray('post');

		// need to prepare the data before binding with post lib for quick post item
		// quick post has limited property. we will just manually assign.
		$data['category_id'] = $arrData['category'];
		$data['categories'] = array($arrData['category']);

		if (is_array($arrData['category'])) {
			$data['category_id'] = $arrData['category'][0];
			$data['categories'] = $arrData['category'];
		}

		$data['created'] = EB::date()->toSql();
		$data['modified'] = EB::date()->toSql();
		$data['publish_up'] = EB::date()->toSql();
		$data['created_by'] = $this->my->id;
		$data['access'] = $this->acl->get('enable_privacy') ? EB::normalize($arrData, 'privacy', 0) : 0;
		$data['frontpage'] = $this->acl->get('contribute_frontpage') ? 1 : 0;

		// If user does not have privilege to store, we need to mark as pending review
		$data['published'] = EASYBLOG_POST_PENDING;

		if ($this->acl->get('publish_entry')) {
			$data['published'] = EASYBLOG_POST_PUBLISHED;
		}

		// quick post is always a sitewide item
		$data['source_id'] = 0;
		$data['source_type'] = EASYBLOG_POST_SOURCE_SITEWIDE;

		// we need to set this as legacy post as the post did not go through composer.
		$data['doctype'] = EASYBLOG_POST_DOCTYPE_LEGACY;

		$data['tags'] = $arrData['tags'];

		// we will let the quickpost adapther to handle the title, content, intro text and .
		$data['title'] = isset($arrData['title']) ? $arrData['title'] : '';
		$data['content'] = '';
		$data['intro'] = '';
		$data['posttype'] = '';
		$data['allowcomment'] = 1;
		$data['autoposting'] = isset($arrData['autopost']) ? $arrData['autopost'] : array();

		// since the quick post already did the validation, we no longer need to do another data validation as this might cause issue to quote post. let ignore validation.
		$saveOptions = array('applyDateOffset' => false, 'skipCustomFields' => true, 'validateData' => false);

		$post = EB::post();

		// Create post revision
		$post->create($saveOptions);

		// binding
		$post->bind($data);

		// process the content
		$quickpost->bind($post);

		try {
			$post->save($saveOptions);

		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return $this->ajax->reject($exception);
		}

		//save assets * for now only applied to link post
		if ($post->posttype == 'link') {
			$quickpost->saveAssets($post);
		}

		$message = $quickpost->getSuccessMessage();

		if ($post->isPending()) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_SAVED_REQUIRE_MODERATION');
		}

		return $this->ajax->resolve(EB::exception($message, 'success'));
	}

	/**
	 * Retrieves the video embed content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getVideo()
	{
		$link = $this->input->get('link', '', 'default');

		// Get the embedded codes for the video
		$lib = EB::videos();
		$embed = $lib->getProviderEmbedCodes($link);
		$domain = $lib->getDomain($link);

		$showNoCookies = $domain == 'youtube.com' ? true : false;

		return $this->ajax->resolve($embed, $showNoCookies);
	}
}
