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

class EasyBlogThemesHelperPolls extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders the poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function item($poll)
	{
		$disabled = false;

		// Disable the options if vote is not allowed
		if (!$poll->canVote) {
			$disabled = true;
		}

		$themes = EB::themes();
		$themes->set('poll', $poll);
		$themes->set('isMultiple', $poll->multiple);
		$themes->set('totalVotes', $poll->totalVotes);
		$themes->set('hasExpirationDate', $poll->hasExpirationDate);
		$themes->set('hasExpired', $poll->hasExpired);
		$themes->set('disabled', $disabled);

		$output = $themes->output('site/helpers/polls/item');

		return $output;
	}

	/**
	 * Renders the poll form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function form(EasyBlogPolls $poll, $options = [])
	{
		$obj = $poll->getDefaultObject();

		if ($poll->id) {
			$obj->id = $poll->id;
			$obj->title = $poll->title;
			$obj->items = $poll->getFormattedItems();
			$obj->expiry_date = $poll->expiry_date == '0000-00-00 00:00:00' ? '' : $poll->expiry_date;
			$obj->hasExpirationDate = $poll->hasExpirationDate();
			$obj->allow_unvote = (int) $poll->allow_unvote;
			$obj->multiple = (int) $poll->multiple;
		}

		$isComposer = EB::normalize($options, 'isComposer', false);

		$userPolls = [];
		$userPollsList = [];

		if ($isComposer) {
			$model = EB::model('Polls');

			$options = [];
			$options['userId'] = $this->my->id;

			$userPolls = $model->getPolls($options);
			$userPollsList = ['-1' => JText::_('COM_EB_COMPOSER_SELECT_POLL_DROPDOWN_DEFAULT')];

			foreach ($userPolls as $row) {
				$userPollsList[$row->id] = $row->title;
			}
		}

		$showNewPollForm = $poll->id || !$isComposer || empty($userPolls);
		$pollFormSectionTitle = $poll->id ? 'COM_EB_POLL_FORM_UPDATE_SECTION' : 'COM_EB_POLL_FORM_CREATE_SECTION';

		$themes = EB::themes();
		$themes->set('poll', $obj);
		$themes->set('userPolls', $userPolls);
		$themes->set('isComposer', $isComposer);
		$themes->set('userPollsList', $userPollsList);
		$themes->set('showNewPollForm', $showNewPollForm);
		$themes->set('pollFormSectionTitle', $pollFormSectionTitle);

		$output = $themes->output('site/helpers/polls/form');

		return $output;
	}
}