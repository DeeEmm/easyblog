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

class EasyBlogPolls extends EasyBlog
{
	private $nextlimit = 0;

	public $acl = null;
	public $table = null;

	public function __construct($id = null)
	{
		parent::__construct();

		$this->acl = EB::acl();
		$this->table = EB::table('Polls');

		if ($id && is_numeric($id)) {
			$this->table->load($id);
		}

		if ($id && is_array($id)) {
			$items = [];

			foreach ($id as $key => $value) {
				$items[$key] = $value;
			}

			$this->table->load($items);
		}
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Perform vote action on the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function vote($itemId, $userId)
	{
		$pollItem = EB::table('PollItems');
		$state = $pollItem->load($itemId);

		// Do not proceed if there is no such item
		if (!$state || !$this->table->id) {
			$this->error = JText::_('COM_EB_POLL_ITEM_ERROR_INVALID');
			return false;
		}

		$pollUsers = EB::table('PollUsers');
		$pollUsers->load(['poll_id' => $this->table->id, 'item_id' => $itemId, 'user_id' => $userId]);

		$model = EB::model('Polls');
		$hasVoted = $model->hasVoted($this->table->id, $userId);

		// User might want to change the vote
		if ($hasVoted && !$this->multiple) {
			// Unvote the item that the user voted previously first
			$itemVoted = $model->getUserVoted($this->table->id, $userId);

			if (!empty($itemVoted)) {
				$votedId = $itemVoted[0]->item_id;

				$this->unvote($votedId, $userId, true);
			}
		}

		if (!$pollUsers->id) {
			$pollUsers->poll_id = $this->table->id;
			$pollUsers->item_id = $itemId;
			$pollUsers->user_id = $userId;

			$pollUsers->store();
		}

		$pollItem->count++;

		$pollItem->store();

		return true;
	}

	/**
	 * Perform unvote action on the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function unvote($itemId, $userId, $isPrevious = false)
	{
		$pollItem = EB::table('PollItems');
		$state = $pollItem->load($itemId);

		// Default error message
		$this->error = JText::_('COM_EB_POLL_ITEM_ERROR_INVALID');

		// Do not proceed if there is no such item
		if (!$state || !$this->table->id) {
			return false;
		}

		if (!$this->table->allow_unvote && !$isPrevious) {
			$this->error = JText::_('COM_EB_POLL_ITEM_ERROR_UNVOTE_NOT_ALLOWED');
			return false;
		}

		$pollUsers = EB::table('PollUsers');
		$pollUsers->load(['poll_id' => $this->table->id, 'item_id' => $itemId, 'user_id' => $userId]);

		if (!$pollUsers->id) {
			return false;
		}

		$pollUsers->delete();

		// Minus the count if the count is not zero yet
		if ($pollItem->count) {
			$pollItem->count--;

			$pollItem->store();
		}

		return true;
	}

	/**
	 * Retrieve the error message
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Retrieve the voters of the poll/poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getVoters($itemId, $options = [])
	{
		$model = EB::model('Polls');
		$results = $model->getVoterIds($this->id, $itemId, $options);
		$this->nextlimit = $model->getNextLimit();

		if (!$results) {
			return false;
		}

		$voters = [];

		foreach ($results as $voterId) {
			$user = EB::user($voterId);

			$voters[] = $user;
		}

		return $voters;
	}

	/**
	 * Retrieve the voters of the poll/poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getNextLimit()
	{
		return (int) $this->nextlimit;
	}

	/**
	 * Retrieve the total votes of the poll/poll item
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTotalVotes($itemId = null)
	{
		$model = EB::model('Polls');
		$total = $model->getTotalVotes($this->id, $itemId);

		return $total;
	}

	/**
	 * Retrieve the items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getItems()
	{
		$model = EB::model('Polls');
		$items = $model->getItems($this->id);

		return $items;
	}

	/**
	 * Retrieve the total number of the items of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getTotalItems()
	{
		$model = EB::model('Polls');
		$total = $model->getTotalItems($this->id);

		return $total;
	}

	/**
	 * Determine if the poll has expired
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasExpired()
	{
		$model = EB::model('Polls');
		$hasExpired = $model->hasExpired($this->expiry_date);

		return $hasExpired;
	}

	/**
	 * Determine if the poll has expiration date
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hasExpirationDate()
	{
		$model = EB::model('Polls');
		$hasExpirationDate = $model->hasExpirationDate($this->expiry_date);

		return $hasExpirationDate;
	}

	/**
	 * Saves a poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function savePoll($data)
	{
		$state = $this->table->savePoll($data);

		return $state;
	}

	/**
	 * Determine if the poll can be created by the current user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canCreate()
	{
		return $this->acl->get('polls_create') || FH::isSiteAdmin();
	}

	/**
	 * Determine if the poll can be edited by the current user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canEdit()
	{
		return ($this->acl->get('polls_edit') && $this->user_id == (int) $this->my->id) || FH::isSiteAdmin();
	}

	/**
	 * Determine if the poll can be deleted by the current user
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function canDelete()
	{
		return ($this->acl->get('polls_manage') && $this->user_id == (int) $this->my->id) || FH::isSiteAdmin();
	}

	/**
	 * Delete the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function delete()
	{
		$this->table->delete();
	}

	/**
	 * Retrieve the formatted items for the block data
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getFormattedItems()
	{
		$results = $this->getItems();

		if (!$results) {
			return [];
		}

		$items = [];

		foreach ($results as $row) {
			$item = new stdClass();
			$item->id = $row->id;
			$item->content = $row->value;

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Retrieve the html of the poll block
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getBlockHtml()
	{
		$items = $this->getFormattedItems();

		$themes = EB::themes();
		$themes->set('pollId', $this->id);
		$themes->set('canEdit', $this->canEdit());
		$themes->set('items', $items ? $items : false);
		$themes->set('isMultiple', $this->multiple);
		$themes->set('pollTitle', $this->title);
		$themes->set('defaultItem', JText::_('COM_EB_POLL_DEFAULT_OPTION_TITLE'));

		$html = $themes->output('site/composer/blocks/handlers/polls/html.content');

		return $html;
	}

	/**
	 * Stores the table
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function store()
	{
		$state = $this->table->store();

		return $state;
	}

	/**
	 * Retrieve the default object data of a fresh poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getDefaultObject()
	{
		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = '';
		$obj->items = [(object) ['id' => '', 'content' => '']];
		$obj->expiry_date = '';
		$obj->hasExpirationDate = false;
		$obj->allow_unvote = true;
		$obj->multiple = false;

		return $obj;
	}

	/**
	 * Retrieve the formatted object data of the poll
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFormattedObject($options = [])
	{
		$string = EB::string();
		$canVote = EB::normalize($options, 'canVote', true);

		$obj = new stdClass();
		$obj->id = $this->id;
		$obj->canVote = true;

		$obj->title = $this->title;
		$obj->items = $this->getItems();
		$obj->multiple = $this->multiple;
		$obj->allow_unvote = $this->allow_unvote;
		$obj->totalVotes = 0;
		$obj->hasExpired = $this->hasExpired() ? 1 : 0;
		$obj->hasExpirationDate = $this->hasExpirationDate() ? 1 : 0;
		$obj->expiry_date = $obj->hasExpirationDate ? JText::sprintf('COM_EB_POLL_EXPIRY_MESSAGE', EB::date($this->expiry_date)->format(JText::_('DATE_FORMAT_LC1'))) : '';
		$obj->expired = $obj->hasExpired ? JText::sprintf('COM_EB_POLL_EXPIRED_MESSAGE', EB::date($this->expiry_date)->format(JText::_('DATE_FORMAT_LC1'))) : '';

		foreach ($obj->items as $item) {
			$obj->totalVotes = $obj->totalVotes + (int) $item->count;

			$item->votesText = $string->getNoun('COM_EB_POLL_VOTE', $item->count, true);
		}

		$obj->totalVotesText = $string->getNoun('COM_EB_POLL_VOTE', $obj->totalVotes, true);

		// User can only vote the poll when the poll is not expired and on entry page
		if (!$canVote || $obj->hasExpired || !JFactory::getUser()->id) {
			$obj->canVote = false;
		}

		$obj->html = '';

		if ($this->isPublished()) {
			$themes = EB::themes();
			$themes->set('poll', $obj);
			$obj->html = $themes->output('site/blocks/polls/content');
		}

		return $obj;
	}

	/**
	 * Determine if the poll is published or not
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isPublished()
	{
		return $this->state == EB_PUBLISHED;
	}
}
