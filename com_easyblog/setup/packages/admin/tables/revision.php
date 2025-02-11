<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableRevision extends EasyBlogTable
{
	public $id = null;
	public $post_id = null;
	public $title = null;
	public $created = null;
	public $modified = null;
	public $created_by = null;
	public $content = '';
	public $state = null;
	public $ordering = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_revisions', 'id', $db);
	}

	/**
	 * Fixes any properties that should not be left empty / invalid.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalize()
	{
		// If there is no title, assign a default title.
		if (empty($title)) {
			$title = JText::sprintf('COM_EASYBLOG_REVISION_DEFAULT_TITLE', EB::date()->format());
		}

		// If there is no creation date assigned, assign current date.
		if (!isset($this->created)) {
			$this->created = EB::date()->toSql();
		}

		// If there is no creator assigned, assign current logged in user.
		if (! isset($this->created_by)) {
			$this->created_by = EB::user()->id;
		}

		// If there is no revisio state assigned, assign draft state.
		if (is_null($this->state)) {
			$this->state = EASYBLOG_REVISION_DRAFT;
		}

		if (!$this->ordering) {
			$this->getNextOrdering();
		}
	}

	/**
	 * Retrieves the next ordering for this post's series
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getNextOrdering()
	{
		$model = EB::model('Revisions');
		$this->ordering = $model->getNextOrder($this->post_id);
	}

	/**
	 * Saves the revision data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($pks = null)
	{
		// Normalize data
		$this->normalize();

		// Update modification time
		$this->modified = EB::date()->toSql();

		$state = parent::store();

		return $state;
	}

	/**
	 * Retrieves the document object for this revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDocument()
	{
		static $documents = array();

		if (!isset($documents[$this->id])) {
			$content = $this->getContent();

			$document = EB::document($content->document);

			$documents[$this->id] = $document;
		}

		return $documents[$this->id];
	}

	/**
	 * Determines if this revision is the current revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isCurrent(EasyBlogPost $post)
	{
		return $post->post->revision_id == $this->id;
	}

	/**
	 * Determines if this revision is a draft.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isDraft()
	{
		return $this->state == EASYBLOG_REVISION_DRAFT;
	}

	/**
	 * Determines if this revision is pending upon submission for approvals.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPending()
	{
		return $this->state==EASYBLOG_REVISION_PENDING;
	}

	/**
	 * Determines if the revision has been finalized
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFinalized()
	{
		return $this->state == EASYBLOG_REVISION_FINALIZED;
	}

	/**
	 * Retrieves the title of the revision
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTitle()
	{
		if ($this->title) {
			return $this->title;
		}

		$title = JText::sprintf('COM_EASYBLOG_COMPOSER_REVISION_TITLE', $this->ordering);

		return $title;
	}

	/**
	 * Retrieves the author of the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthor()
	{
		$user = EB::user($this->created_by);
		return $user;
	}

	/**
	 * Retrieves the categories of the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategories($output = false)
	{
		$content = $this->getContent();
		$categories = $content->categories;

		$items = array();
		$string = '';

		if ($categories) {
			foreach($categories as $catId) {
				$cat = EB::table('Category');
				$cat->load($catId);

				$items[] = $cat;

				$string = ($string) ? ', ' . $cat->title : $cat->title;
			}
		}

		if ($output) {
			return $string;
		}

		return $items;
	}

	/**
	 * Determines the current viewer can delete the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canDelete($pk = null, $joins = null)
	{
		$user = JFactory::getUser();

		// If this revision is being used as a post, it shouldn't be delete-able
		$post = EB::post($this->post_id);

		if ($post->revision_id == $this->id) {
			return false;
		}

		if ($this->created_by == $user->id) {
			return true;
		}

		if (FH::isSiteAdmin()) {
			return true;
		}



		return false;
	}

	/**
	 * delete revision
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		// before we delete, we need to know if this revision is a draft or not.
		// if yes, we need to check is this revision is a brand new doc or not. if yes,
		// we need to delete item in easyblog_post as well.

		$postId = $this->post_id;

		// we need to load from jtable directly instead of post lib to avoid
		// same revision item being treated as blog item.
		$post = EB::table('Post');
		$post->load($postId);

		$postLib = EB::post($postId);

		if ($post->published == EASYBLOG_POST_BLANK || $post->published == EASYBLOG_POST_DRAFT) {
			// Relocate media file
			$postLib->relocateMediaFiles();
			$post->delete();
		}

		$state = parent::delete($pk);

		return $state;
	}

	/**
	 * Retrieves the creation date of this revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCreationDate()
	{
		$date = EB::date($this->created, true);

		return $date;
	}

	/**
	 * Returns JSON decoded data of the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContent()
	{
		return json_decode($this->content);
	}

	/**
	 * Retrieve the post object for this revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPost()
	{
		static $posts = array();

		if (!isset($posts[$this->post_id])) {
			$post = EB::post($this->post_id);

			$posts[$this->post_id] = $post;
		}


		return $posts[$this->post_id];
	}

	/**
	 * Sets the snapshot of the post into the revision data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setContent($content)
	{
		$this->content = json_encode($content);
	}

	/**
	 * Retrieves the current state of the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getState()
	{
		if ($this->isPending()) {
			return JText::_('Pending');
		}

		if ($this->isDraft()) {
			return JText::_('Draft');
		}

		$post = $this->getPost();

		if ($this->post_id == $post->id) {
			return JText::_('Published');
		}
	}

	/**
	 * Retrieves the css class state for the revision's state
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCssState()
	{
		if ($this->state == EASYBLOG_REVISION_FINALIZED) {
			return 'revision-finalized';
		}

		if ($this->state == EASYBLOG_REVISION_PENDING) {
			return 'revision-pending';
		}

		return 'revision-draft';
	}

	/**
	 * Retrieves the post content that is used for diff
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDiffContent(EasyBlogTableRevision $target)
	{

		$targetContentObj = $target->getContent();
		$currentContentObj = $this->getContent();

		$targetHtml = $targetContentObj->intro . $targetContentObj->content;
        $targetHtml = EBString::str_ireplace('&nbsp;', ' ', $targetHtml);

		$currentHtml = $currentContentObj->intro . $currentContentObj->content;
        $currentHtml = EBString::str_ireplace('&nbsp;', ' ', $currentHtml);

		$html = EB::revisions()->compare($currentHtml, $targetHtml);

		$theme = EB::themes();
		$theme->set('html', $html);
		$output = $theme->output('site/composer/revisions/compare.blocks');

		return $output;
	}


}
