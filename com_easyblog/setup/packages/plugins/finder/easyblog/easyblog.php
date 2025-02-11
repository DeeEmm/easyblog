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

jimport('joomla.filesystem.file');
$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

EB::initFoundry();

class plgFinderEasyBlog extends EBFinderBase
{
	protected $context = 'EasyBlog';
	protected $extension = 'com_easyblog';
	protected $layout = 'entry';
	protected $type_title = 'EasyBlog';
	protected $table = '#__easyblog_post';
	protected $autoloadLanguage = true;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Ensure that EasyBlog really exists on the site first
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function exists()
	{
		// First we check if the extension is enabled.
		if (EBComponentHelper::isEnabled($this->extension) == false) {
			return;
		}

		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		if (!EB::isFoundryEnabled()) {
			return false;
		}

		return true;
	}

	/**
	 * Delete a url from the cache
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function deleteFromCache($id)
	{
		if (!$this->exists()) {
			return;
		}

		$db = EB::db();

		$query = [];
		$query[] = 'SELECT ' . $db->qn('link_id') . ' FROM ' . $db->qn('#__finder_links');
		$query[] = 'WHERE ' . $db->qn('url') . ' LIKE ' . $db->Quote('%option=com_easyblog&view=entry&id=' . $id . '%');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$item = $db->loadResult();

		$state = $this->indexer->remove($item);

		return $state;
	}

	/**
	 * Remove link from the database once a post is deleted
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function onFinderAfterDelete($context, $table)
	{
		$allowed = ['easyblog.blog', 'com_finder.index'];

		if (!in_array($context, $allowed)) {
			return true;
		}

		if ($context === 'easyblog.blog') {
			$id = $this->deleteFromCache($table->id);
		}

		if ($context === 'com_finder.index') {
			$id = $table->link_id;
		}

		return $this->remove($id);
	}

	/**
	 * When a post's state is changed, we need to update it accordingly
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function onFinderAfterSave($context, $post, $isNew)
	{
		if (!$this->exists()) {
			return;
		}

		// Only process easyblog items here
		if ($context === 'easyblog.blog' && !$post->isBlank() && !$post->isDraft() && !$post->isPending() && !$post->isTrashed()) {
			$this->reindex($post->id);
		}

		if ($context === 'easyblog.blog' && $post->isTrashed()) {
			$this->deleteFromCache($post->id);
		}

		return true;
	}


	/**
	 * When a post's state is changed, we need to update it accordingly
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
		// We only want to handle articles here.
		if ($context === 'easyblog.blog') {
			$db = EB::db();

			$indexedURL = "index.php?option=com_easyblog&view=entry&id=$pks";

			$query = 'UPDATE `#__finder_links` SET ' . $db->nameQuote('state') . ' = ' . $db->Quote($value);
			$query .= ' , ' . $db->nameQuote('published') . ' = ' . $db->Quote($value);
			$query .= ' WHERE ' . $db->nameQuote('url') . ' = ' . $db->Quote($indexedURL);

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Indexes post on the site
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	protected function proxyIndex($item, $format = 'html')
	{
		if (!$this->exists() || !$item->id) {
			return;
		}

		// Load up the post item
		$post = EB::post();
		$post->load($item->id);

		// Build the necessary route and path information.
		$item->url = $post->normalizeBlogPermalink();
		$item->route = EBR::_($item->url, true, null, false, false, false);

		if (!FH::isJoomla4()) {
			// Get the content path only require in Joomla 3.x
			$item->path = FinderIndexerHelper::getContentPath($item->route);
		}

		// If there is access defined, just set it to 2 which is special privileges.
		if (!$item->access || $item->access == 0) {
			$item->access = 1;
		} else if ($item->access > 0) {
			$item->access = 2;
		}

		// Get the intro text of the content
		$item->summary = $post->getIntro(false, true, 'all', false, array('fromRss' => true));

		// Get the contents
		$item->body = $post->getContent('entry', false);

		$isProtected = $post->isPasswordProtected();

		// If the post is password protected, we do not want to display the contents
		if ($isProtected) {
			$item->summary = JText::_('PLG_FINDER_EASYBLOG_PASSWORD_PROTECTED');
		}

		if (!$isProtected) {
			// we want to get custom fields values.
			$fields = $post->getCustomFields();

			$fieldlib = EB::fields();
			$customfields = [];

			if ($fields) {
				foreach($fields as $field) {
					if ($field->group->hasValues($post)) {
						foreach ($field->fields as $customField) {
							$eachField = $fieldlib->get($customField->type);
							$customfields[] = $eachField->text($customField, $post);
						}
					}
				}

				$customfieldvalues = implode(' ', $customfields);
				$item->body = $item->body . ' ' . $customfieldvalues;
			}
		}

		$item->start_date = $post->created;
		$item->publish_start_date = $post->publish_up;

		// Add the author's meta data
		$item->metaauthor = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;
		$item->author = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;

		// If the post has an image, use it
		$image = $post->getImage('thumbnail', false, true);

		// If there's no image, try to scan the contents for an image to be used
		if (!$image && $post->isLegacy()) {
			$image = EB::string()->getImage($item->body);
		}

		// If we still can't locate any images, use the placeholder image
		if (!$image) {
			$image = EB::getPlaceholderImage();
		}

		$registry = new JRegistry();
		$registry->set('image', $image);

		$item->params = $registry;

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'EasyBlog');

		// Add the author taxonomy data.
		if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
		}

		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		if (empty($item->language))
			$item->language = '*';

		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		EBFinderHelper::getContentExtras($item);

		return $this->indexer->index($item);
	}

	/**
	 * Remove any unwanted part of the url in the item link
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	private function removeAdminSegment($url = '')
	{
		if ($url) {
			$url = ltrim($url, '/');
			$url = str_replace('administrator/index.php', 'index.php', $url);
		}

		return $url;
	}

	/**
	 * This method would be invoked by Joomla's indexer
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	protected function setup()
	{
		if (!$this->exists()) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the sql query used to retrieve blog posts on the site
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();

		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select( 'a.*, b.title AS category, u.name AS author, eu.nickname AS created_by_alias');

		$sql->select('a.published AS state,a.id AS ordering');
		$sql->select('b.published AS cat_state, 1 AS cat_access');
		$sql->select('m.keywords AS metakay, m.description AS metadesc');
		$sql->from('#__easyblog_post AS a');

		// we only fetch the primary category.
		$sql->join('LEFT', '#__easyblog_post_category AS pc ON pc.post_id = a.id and pc.primary = 1');
		$sql->join('INNER', '#__easyblog_category AS b ON b.id = pc.category_id');
		$sql->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$sql->join('LEFT', '#__easyblog_users AS eu ON eu.id = a.created_by');
		$sql->join('LEFT', '#__easyblog_meta AS m ON m.content_id = a.id and m.type = ' . $db->Quote('post'));

		return $sql;
	}
}
