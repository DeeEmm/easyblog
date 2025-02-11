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

class EasyBlogControllerTags extends EasyBlogController
{
	/**
	 * Allows caller to update an existing tag
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false);

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG', 'error');
			return $this->app->redirect($return);
		}

		// Possibility is that this tag is being edited.
		$id = $this->input->get('id', 0, 'int');

		$table = EB::table('Tag');
		$table->load($id);

		if (!$table->id) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TAG_INVALID', 'error');
			return $this->app->redirect($return);
		}

		$tag = $this->input->get('tags', '', 'string');
		$language = $this->input->get('tag_language', '', 'default');

		$exists = $table->exists($tag, false);

		if ($exists) {
			$this->info->set('COM_EASYBLOG_TAG_ALREADY_EXISTS', 'info');

			return $this->app->redirect($return);
		}

		$table->title = $tag;
		$table->language = $language != '*' ? $language : '';
		$table->store();

		$this->info->set('COM_EASYBLOG_DASHBOARD_TAGS_UPDATED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to create new tags on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function create()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false);

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !FH::isSiteAdmin()) {
			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG', 'error');
			return $this->app->redirect($return);
		}

		// Get the tags list
		$tags = $this->input->get('tags', '', 'default');
		$language = $this->input->get('tag_language', '', 'default');
		$description = $this->input->get('tag_description', '', 'default');

		$tags = EBString::trim($tags);

		if (!$tags) {
			$this->info->set('COM_EASYBLOG_DASHBOARD_TAG_INVALID', 'error');
			return $this->app->redirect($return);
		}

		// Since it could be comma separated, we need to match it.
		$tags = explode(',', $tags);
		$allTagsExist = true;

		foreach ($tags as $tag) {
			$tag = EBString::trim($tag);

			if (empty($tag)) {
				continue;
			}

			$table = EB::table('Tag');
			$exists = $table->exists($tag);

			if (!$exists) {
				$allTagsExist = false;
				$table->title = $tag;
				$table->description = $description;
				$table->created_by = $this->my->id;
				$table->published = EASYBLOG_POST_PUBLISHED;
				$table->language = $language != '*' ? $language : '';
				$table->store();
			}
		}

		// If all tags is already exist in system, we show different message
		if ($allTagsExist) {
			$this->info->set('COM_EASYBLOG_TAG_ALREADY_EXISTS', 'info');
			return $this->app->redirect(EB::_('index.php?option=com_easyblog&view=dashboard&layout=tagform', false));
		}

		$this->info->set('COM_EASYBLOG_DASHBOARD_TAGS_CREATED_SUCCESSFULLY', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Deletes a tag from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Default return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false);

		// Ensure that the user has access to create tags
		if (!$this->acl->get('create_tag') && !FH::isSiteAdmin()) {

			$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG', 'error');
			return $this->app->redirect($return);
		}

		// Get the list of tags id's
		$ids = $this->input->get('ids', '', 'array');

		foreach ($ids as $id) {
			$tag = EB::table('Tag');
			$tag->load($id);

			if (!$id || !$tag->id) {
				$this->info->set('COM_EASYBLOG_TAG_INVALID_ID', 'error');
				return $this->app->redirect($return);
			}

			// Ensure that the user owns this tag
			if ($tag->created_by != $this->my->id && !FH::isSiteAdmin()) {
				$this->info->set('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_TAG', 'error');
				return $this->app->redirect($return);
			}

			$tag->delete();
		}

		$this->info->set('COM_EASYBLOG_TAG_DELETED', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Search tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the query
		$search = $this->input->get('filter-tags', '', 'string');

		$url = EB::_('index.php?option=com_easyblog&view=tags&search=' . $search, false);


		$this->app->redirect($url);
	}

}
