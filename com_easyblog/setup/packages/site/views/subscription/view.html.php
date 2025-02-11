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

class EasyBlogViewSubscription extends EasyBlogView
{
	public function display($tpl = null)
	{
		// Check for acl
		$this->checkAcl('allow_subscription');

		// Do not allow guests to access this page
		if ($this->my->guest) {
			return EB::requireLogin();
		}

		// Set the title of the page
		$this->doc->setTitle(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PAGE_TITLE'));

		// Add pathway
		if (!EBR::isCurrentActiveMenu('subscription')) {
			$this->setPathway(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_BREADCRUMB'));
		}

		// Ensure that the user has access to manage subscriptions
		if (!$this->acl->get('allow_subscription')) {
			throw EB::exception(JText::_('COM_EASYBLOG_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW'), 500);
		}

		// Get a list of subscriptions the user has
		$model = EB::model('Subscriptions');
		$result = $model->getSubscriptionsByUser($this->my->id);
		$subscriptions = array();
		$groups = array();

		if ($result) {
			foreach ($result as $row) {
				
				$type = $row->utype;
				$groups[] = $type;

				if (!isset($subscriptions[$type])) {
					$subscriptions[$row->utype] = array();
				}

				// Get the formatted type title
				$row->object = $row->getObject();
				
				$subscriptions[$row->utype][] = $row;
			}
		}

		// Ensure that the groups are unique
		$groups = array_unique($groups);

		$this->set('groups', $groups);
		$this->set('subscriptions', $subscriptions);

		parent::display('subscription/default');
	}
}
