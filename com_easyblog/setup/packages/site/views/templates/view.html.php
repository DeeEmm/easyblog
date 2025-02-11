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

require_once(EB_ROOT . '/views/views.php');

class EasyBlogViewTemplates extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Handles backend
		if (EB::isFromAdmin() && method_exists($this, $this->getLayout())) {
			$layoutName = $this->getLayout();
			return $this->$layoutName();
		}

		// Default redirection
		$redirect = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=templates', false);

		// If there is no tmpl=component, we will enforce this
		$tmpl = $this->input->get('tmpl', '');

		if ($tmpl != 'component') {
			$this->input->set('tmpl', 'component');
		}

		// Ensure that the user is logged in.
		EB::requireLogin();

		$uid = $this->input->getVar('uid', null);
		$post = EB::post($uid);

		// If there's no id provided, we will need to create the initial revision for the post.
		if (!$uid) {
			$post->create();
			$uid = $post->uid;
		}

		// Determines if we should show the sidebars by default
		$templateId = $this->input->get('id', 0, 'int');
		$template = EB::table('PostTemplate');
		$template->load($templateId);

		if ($template->id) {
			// Check if the user can really view the existing post template
			if (!$template->canView()) {
				$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_NOT_ALLOW_TO_VIEW');
				$this->info->set($message, 'error');

				return $this->app->redirect($redirect);
			}

		} else {
			// Check if user can create a new post template
			if (!FH::isSiteAdmin() && !$this->acl->get('create_post_templates')) {
				$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_NOT_ALLOW_TO_CREATE');
				$this->info->set($message, 'error');

				return $this->app->redirect($redirect);
			}
		}

		if (!$post->title) {
			$this->doc->setTitle(JText::_('COM_EASYBLOG_COMPOSER_POST_UNTITLED'));
		}

		$composer = EB::composer();

		// Detect the keep alive interval that we should use
		$keepAlive = (int) $this->jconfig->get('lifetime');
		$keepAlive = round($keepAlive / 2);
		$keepAlive = $keepAlive > 16 ? 16 : $keepAlive;
		$keepAlive = $keepAlive * 10000;

		$isWebview = EB::responsive()->isEBTabletWevbiew();

		$this->set('templateId', $templateId);
		$this->set('keepAlive', $keepAlive);
		$this->set('composer', $composer);
		$this->set('template', $template);
		$this->set('post', $post);
		$this->set('isWebview', $isWebview);

		return parent::display('templates/form');
	}
}
