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

class EasyBlogViewPolls extends EasyBlogView
{
	/**
	 * Perform vote action on the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function action()
	{
		if (!$this->config->get('main_polls')) {
			return $this->ajax->reject(JText::_('COM_EB_POLLS_FEATURE_DISABLED'));
		}

		EB::requireLogin();

		$pollId = $this->input->get('pollId', 0, 'int');
		$itemId = $this->input->get('itemId', 0, 'int');

		// Either 'vote' or 'unvote' only
		$actionType = $this->input->get('actionType', '', 'string');

		$allowed = ['vote', 'unvote'];

		if (!in_array($actionType, $allowed)) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ACTION'));
		}

		$poll = EB::polls($pollId);

		if (!$poll->id) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ID'));
		}

		$state = $poll->$actionType($itemId, $this->my->id);

		if ($state) {
			$data = $poll->getFormattedObject();

			return $this->ajax->resolve($data);
		}

		$message = $poll->getError();

		return $this->ajax->reject($message);
	}

	/**
	 * Display the voters of the poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function showVoters()
	{
		$pollId = $this->input->get('pollId', 0, 'int');
		$itemId = $this->input->get('itemId', 0, 'int');

		if (!$pollId || !$itemId) {
			return $this->ajax->resolve(JText::_('COM_EB_POLL_ITEM_ERROR_INVALID'));
		}

		$poll = EB::polls($pollId);

		if (!$poll->id) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ID'));
		}

		$voters = $poll->getVoters($itemId);
		$total = $poll->getTotalVotes($itemId);

		$themes = EB::themes();
		$themes->set('voters', $voters);
		$themes->set('limit', EB::getLimit());
		$themes->set('total', $total);
		$output = $themes->output('site/polls/dialogs/voters');

		return $this->ajax->resolve($output);
	}

	/**
	 * Load more the voters of the poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function loadVoters()
	{
		$pollId = $this->input->get('pollId', 0, 'int');
		$itemId = $this->input->get('itemId', 0, 'int');
		$start = $this->input->get('start', 0, 'int');

		$options = [
			'start' => $start,
			'limit' => 30
		];

		$poll = EB::polls($pollId);

		if (!$poll->id) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ID'));
		}

		$items = $poll->getVoters($itemId, $options);
		$nextlimit = $poll->getNextLimit();

		$output = '';

		if ($items) {
			$voters = [];

			foreach ($items as $voterId) {
				$voters[] = EB::user($voterId);
			}

			$theme = EB::themes();
			$theme->set('voters', $voters);
			$output = $theme->output('site/polls/item/voters');
		}

		return $this->ajax->resolve($output, $nextlimit);
	}

	/**
	 * Retrieve the result of the selected poll for a dialog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getResult()
	{
		$pollId = $this->input->get('pollId', 0, 'int');
		$poll = EB::polls($pollId);

		if (!$poll->id) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ID'));
		}

		$items = $poll->getItems();

		$themes = EB::themes();
		$themes->set('poll', $poll);
		$themes->set('items', $items);

		$content = $themes->output('site/polls/dialogs/result');

		return $this->ajax->resolve($content);
	}

	/**
	 * Display the dialog of confirmation of poll deletion
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		EB::requireLogin();

		$pollId = $this->input->get('pollId', 0, 'int');
		$poll = EB::polls($pollId);

		if (!$poll->id) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_INVALID_ID'));
		}

		$themes = EB::themes();
		$themes->set('poll', $poll);

		$content = $themes->output('site/polls/dialogs/delete');

		return $this->ajax->resolve($content);
	}

	/**
	 * Saves the changes for the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function save()
	{
		if (!$this->config->get('main_polls') && !FH::isSiteAdmin()) {
			return $this->ajax->reject(JText::_('COM_EB_POLLS_FEATURE_DISABLED'));
		}

		$pollId = $this->input->get('pollId', 0, 'int');
		$poll = EB::polls($pollId);

		if ($poll->id && !$poll->canEdit()) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_EDIT_NO_PERMISSION'));
		}

		$formOption = $this->input->get('formOption', EB_POLL_FORM_OPTION_SAVE, 'string');
		$postData = $this->input->get('postData', '', 'string');
		$postData = EB::makeArray(json_decode($postData));
		$state = false;

		// If this is the select existing poll option
		if ($formOption == EB_POLL_FORM_OPTION_SELECT) {
			$selectedPollId = (int) EB::normalize($postData, 'selectedPollId', -1);

			if ($selectedPollId == -1) {
				return $this->ajax->reject(JText::_('Please select a poll.'));
			}

			$newPoll = EB::polls($selectedPollId);

			if (!$newPoll->id) {
				return $this->ajax->reject(JText::_('Invalid selected poll.'));
			}

			$poll = $newPoll;

			$state = true;
		}

		// If this is the create/update option
		if ($formOption == EB_POLL_FORM_OPTION_SAVE) {
			if (!$poll->id && !$poll->canCreate()) {
				return $this->ajax->reject(JText::_('COM_EB_POLL_CREATE_NO_PERMISSION'));
			}

			$title = EB::normalize($postData, 'title', '');
			$multiple = (int) EB::normalize($postData, 'multiple', '');
			$unvote = (int) EB::normalize($postData, 'unvote', '');
			$expiry_date = EB::normalize($postData, 'expiry_date', '0000-00-00 00:00:00');
			$items = isset($postData['items']) ? EB::makeArray($postData['items']) : [];

			// Remove spaces
			$title = trim($title);

			if (!$title) {
				return $this->ajax->reject(JText::_('COM_EB_POLL_EMPTY_TITLE_MESSAGE'));
			}

			if (count($items) < 2 && !$items[0]['content']) {
				return $this->ajax->reject(JText::_('COM_EB_POLL_EMPTY_ITEMS_MESSAGE'));
			}

			$data = new stdClass();
			$data->title = $title;
			$data->items = $items;
			$data->isMultiple = $multiple;
			$data->unvoteAllowed = $unvote;
			$data->expiry_date = $expiry_date;

			$state = $poll->savePoll($data);
		}

		$reloadAfterSave = $this->input->get('reloadAfterSave', false, 'bool');

		if ($state) {
			if ($reloadAfterSave) {
				$message = $pollId ? 'COM_EB_POLL_UPDATE_SUCCESS_MESSAGE' : 'COM_EB_POLL_CREATE_SUCCESS_MESSAGE';
				$this->info->set($message, 'success');

				$redirect = EB::isFromAdmin() ? 'index.php?option=com_easyblog&view=polls' : EBR::_('index.php?option=com_easyblog&view=dashboard&layout=polls');
				return $this->ajax->redirect($redirect);
			}

			$obj = new stdClass();
			$obj->id = $poll->id;
			$obj->placeholder = JText::sprintf('COM_EB_POLL_POST_LEGACY_PLACEHOLDER', $poll->title);
			$obj->html = $poll->getBlockHtml();

			return $this->ajax->resolve($obj);
		}

		$message = 'COM_EB_POLL_UPDATE_FAILED_MESSAGE';

		if ($pollId) {
			$message = 'COM_EB_POLL_CREATE_FAILED_MESSAGE';
		}

		return $this->ajax->reject(JText::_($message));
	}

	/**
	 * Display a form to allow user to construct/edit a poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function form()
	{
		EB::requireLogin();

		$pollId = $this->input->get('pollId', 0, 'int');
		$poll = EB::polls($pollId);

		if (!$poll->id && !$poll->canCreate()) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_CREATE_NO_PERMISSION'));
		}

		if ($poll->id && !$poll->canEdit()) {
			return $this->ajax->reject(JText::_('COM_EB_POLL_EDIT_NO_PERMISSION'));
		}

		$isComposer = $this->input->get('isComposer', false, 'bool');

		$options = [];
		$options['isComposer'] = $isComposer;

		$themes = EB::themes();
		$themes->set('poll', $poll);
		$themes->set('options', $options);
		$html = $themes->output('site/polls/form/default');

		$saveButtonTitle = $isComposer ? 'COM_EB_POLL_INSERT_BUTTON' : 'COM_EASYBLOG_SAVE_BUTTON';

		$themes = EB::themes();
		$themes->set('saveButtonTitle', $saveButtonTitle);
		$themes->set('html', $html);
		$output = $themes->output('site/polls/dialogs/form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display a dialog of vote notice to the guest
	 *
	 * @since	6.0.4
	 * @access	public
	 */
	public function guestVoteNotice()
	{
		$themes = EB::themes();
		$output = $themes->output('site/polls/dialogs/guest.vote.notice');

		return $this->ajax->resolve($output);
	}
}
