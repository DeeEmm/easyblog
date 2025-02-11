<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

class plgEasyBlogAutoArticle extends JPlugin
{
	// autoload language
	protected $autoloadLanguage = true;

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$exists = JFile::exists($file);

			if (!$exists) {
				return false;
			}

			require_once($file);

			if (!EB::isFoundryEnabled()) {
				$exists = false;
			}
		}

		return $exists;
	}

	/**
	 * Run some cleanup after a blog post is deleted
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function onAfterEasyBlogDelete($blog)
	{
		if (!$this->exists()) {
			return;
		}

		// Get plugin info
		$plugin	= JPluginHelper::getPlugin('easyblog', 'autoarticle');
		$pluginParams = EB::registry($plugin->params);

		if ($pluginParams->get('unpublish') == '1') {

			$db = EB::db();
			$query = 'SELECT * FROM `#__easyblog_autoarticle_map` WHERE `post_id`=' . $db->Quote( $blog->id );
			$db->setQuery($query);
			$map = $db->loadObject();

			if ($map) {
				$query = 'UPDATE `#__content` SET `state`=' . $db->Quote(0)
						. ' WHERE `id`=' . $db->Quote($map->content_id);
				$db->setQuery($query);
				$db->Query();

				$query = 'DELETE FROM `#__content_frontpage` '
						. 'WHERE `content_id`=' . $db->Quote($map->content_id);
				$db->setQuery($query);
				$db->Query();
			}
		}
	}

	public function onAfterEasyBlogSave($post, $isNew)
	{
		if (!$this->exists()) {
			return;
		}

		if (!$post->isPublished()) {
			return;
		}

		// Get plugin info
		$plugin = JPluginHelper::getPlugin('easyblog', 'autoarticle');
		$pluginParams = EB::registry($plugin->params);

		// Need to trigger the Joomla article workflow in Joomla 4
		$this->workflowProcess();

		// Normalize the post data
		$data = $this->normalizeData($post, $pluginParams);

		// try to get the existing content id via the mapping table
		$contentMap = EB::table('AutoArticleMap');
		$contentMap->load($post->id, true);

		$aid = '';
		$db = EB::db();

		if (!empty($contentMap->content_id)) {
			$aid = $contentMap->content_id;
		}

		if (empty($aid) && !empty($post->permalink)) {

			//try to get if the article already inserted before based on title alias.
			$query = 'SELECT `id` FROM `#__content` WHERE `alias` = ' . $db->Quote($post->permalink);
			$db->setQuery($query);
			$aid = $db->loadResult();
		}

		$joomlaContent = JTable::getInstance('content');

		if (!empty($aid)) {
			$joomlaContent->load($aid);
		}

		$joomlaContent->bind($data);
		$joomlaContent->store();

		$articleId = $joomlaContent->id;

		if (is_null($isNew)) {
			// something wrong here. test the aid to determine.
			if (empty($aid)) {
				$isNew = true;
			} else {
				$isNew = false;
			}
		}

		if ($isNew && !empty($articleId)) {
			// if saved ok, then insert the mapping into our map table.
			$jdate = EB::date();
			$map = [];
			$map['content_id'] = $articleId;
			$map['post_id'] = $post->id;
			$map['created'] = $jdate->toSql();

			$contentMap->bind($map);
			$contentMap->store();
		}

		// Need to trigger the Joomla article workflow in Joomla 4
		$this->workflowProcess('after', $data, $articleId, $isNew);

		$frontpage = ($pluginParams->get('frontpage', '-1') == '-1') ? $post->frontpage : $pluginParams->get('frontpage', '0');
		$this->setFeatured($articleId, $isNew, $frontpage);

		$cache = JFactory::getCache('com_content');
		$cache->clean();
	}

	public function mapCategory($eb_catid)
	{
		$db = EB::db();

		$data = new stdClass();
		$data->sid = '0';
		$data->cid = '0';

		$category = EB::table('Category');
		$category->load($eb_catid);

		//get joomla section
		$query = 'SELECT cc.id as `catid`';
		$query .= ' FROM #__categories AS cc';
		$query .= ' WHERE cc.`title` = ' . $db->Quote($category->title);
		$db->setQuery($query);

		$section = $db->loadObject();

		if ($section && count($section) > 0) {
			$data->cid = $section->catid;
		}

		return $data;
	}

	/**
	 * Process the workflow in Joomla 4
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function workflowProcess($preparation = 'before', $data = '', $postId = '', $isNew = false)
	{
		$isJoomla4 = EB::isJoomla4();
		$context = 'com_content.article';

		if (!$isJoomla4) {
			return;
		}

		$articleModel = new Joomla\Component\Content\Administrator\Model\ArticleModel();

		if ($preparation === 'before') {
			$articleModel->setUpWorkflow($context);
			$articleModel->workflowBeforeSave();
			return;
		}

		// Process workflow after save
		// Reset the original data for this property
		$articleModel->getState('article.id');

		// Manually set the joomla article id here
		$articleModel->setState('article.id', $postId);
		$articleModel->setState('article.new', $isNew);

		$articleModel->workflowAfterSave($data);
	}

	/**
	 * Set featured value
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function setFeatured($postId, $isNew = false, $frontpage = false)
	{
		if (!$isNew || !$frontpage) {
			return;
		}

		$db = EB::db();
		$isJoomla4 = EB::isJoomla4();

		$path = $isJoomla4 ? JPATH_ADMINISTRATOR . '/components/com_content/src/Table' : JPATH_ADMINISTRATOR . '/components/com_content/tables';
		$prefix = $isJoomla4 ? 'Joomla\\Component\\Content\\Administrator\\Table\\' : 'ContentTable';
		$name = $isJoomla4 ? 'FeaturedTable' : 'Featured';

		JTable::addIncludePath($path);

		$insertData = EB::isJoomla4() ? '('. (int) $postId .', 1, null, null)' : '('. (int) $postId .', 1)';

		// Insert the new entry
		$query = 'INSERT INTO `#__content_frontpage`' . ' VALUES ' . $insertData;
		$db->setQuery($query);
		$db->query();

		$featured = JTable::getInstance($name, $prefix);
		$featured->reorder();
	}

	/**
	 * Normalise post data before store in Joomla content
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function normalizeData($post, $pluginParams)
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// easyblog blog details
		$data = array();
		$data['title'] = $post->title;
		$data['alias'] = $post->permalink;

		$data['introtext'] = $post->getContent('entry');
		$data['fulltext'] = '';

		// Determine whether need to show readmore into article content
		$showReadmore = $pluginParams->get('readmore');

		if ($showReadmore) {
			$EasyBlogitemId = EBR::getItemId('latest');
			$readmoreURL = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id . '&Itemid=' . $EasyBlogitemId);
			$readmoreURL = str_replace('/administrator/', '/', $readmoreURL);

			$readmoreLink = '<a href="' . $readmoreURL . '" class="readon"><span>' . JText::_('PLG_EASYBLOG_AUTOARTICLE_ARTICLE_READMORE') . '</span></a>';
			$data['introtext'] = $data['introtext'] . '<br />' . $readmoreLink;
		}

		$data['created'] = $post->created;
		$data['created_by'] = $post->created_by;
		$data['modified'] = $post->modified;
		$data['modified_by'] = $user->id;
		$data['publish_up'] = $post->publish_up;
		$data['publish_down'] = $post->publish_down;
		$data['language'] = $post->language;

		$imageParams = [
			'image_intro' => '',
			'float_intro' => '',
			'image_intro_alt' => '',
			'image_intro_caption' => '',
			'image_fulltext' => '',
			'float_fulltext' => '',
			'image_fulltext_alt' => '',
			'image_fulltext_caption' => ''
		];

		// Try to get the cover image
		$imageUrl = $post->getImage('large', false, false, false);

		if (!empty($imageUrl)) {
			$imageUrl = EB::string()->abs2rel($imageUrl);
			$imageCaption = $post->getImageCaption();

			$imageParams = [
				'image_intro' => $imageUrl,
				'float_intro' => '',
				'image_intro_alt' => '',
				'image_intro_caption' => $imageCaption,
				'image_fulltext' => '',
				'float_fulltext' => '',
				'image_fulltext_alt' => '',
				'image_fulltext_caption' => ''
			];
		}

		$imageParams = json_encode($imageParams);
		$data['images'] = $imageParams;

		$urlParams = [
				'urla' => '',
				'urlatext' => '',
				'targeta' => '',
				'urlb' => '',
				'urlbtext' => '',
				'targetb' => '',
				'urlc' => '',
				'urlctext' => '',
				'targetc' => ''
			];

		$urlParams = json_encode($urlParams);
		$data["urls"] = $urlParams;

		$attribsParams = [
				'article_layout' => '',
				'show_title' => '',
				'link_titles' => '',
				'show_tags' => '',
				'show_intro' => '',
				'info_block_position' => '',
				'info_block_show_title' => '',
				'show_category' => '',
				'link_category' => '',
				'show_parent_category' => '',
				'link_parent_category' => '',
				'show_author' => '',
				'link_author' => '',
				'show_create_date' => '',
				'show_modify_date' => '',
				'show_publish_date' => '',
				'show_item_navigation' => '',
				'show_hits' => '',
				'show_noauth' => '',
				'urls_position' => '',
				'alternative_readmore' => '',
				'article_page_title' => '',
				'show_publishing_options' => '',
				'show_article_options' => '',
				'show_urls_images_backend' => '',
				'show_urls_images_frontend' => '',
			];

		$attribsParams = json_encode($attribsParams);
		$data["attribs"] = $attribsParams;

		//these four get from plugin params
		$state = $pluginParams->get('status');
		$access = 1;

		if ($pluginParams->get('access', '-1') == '-1') {
			$access = ($post->access) ? 2 : 1;
		} else {
			$tmpAccess = $pluginParams->get('access');

			switch ($tmpAccess) {
				case '1':
					$access = '2';
					break;
				case '2':
					$access = '3';
					break;
				case '0':
				default:
					$access = '1';
					break;
			}
		}

		$section = '0';
		$category = $pluginParams->get('sectionCategory', '0');
		$frontpage = ($pluginParams->get('frontpage', '-1') == '-1') ? $post->frontpage : $pluginParams->get('frontpage', '0');
		$autoMapCategory = $pluginParams->get('autocategory', '0');

		if ($autoMapCategory) {
			$autoMapped = self::mapCategory($post->category_id);

			if (!empty($autoMapped->cid)) {
				$category = $autoMapped->cid;
			}
		}

		$metaKey = $app->input->get('keywords', '', 'raw');;
		$metaDesc = $app->input->get('description', '', 'raw');

		$data['state'] = $state;
		$data['access'] = $access;
		$data['sectionid'] = $section;
		$data['catid'] = $category;
		$data['metakey'] = $metaKey;
		$data['metadesc'] = $metaDesc;

		$metadataParams = [
				'robots' => '',
				'author' => '',
				'rights' => ''
			];

		$metadataParams = json_encode($metadataParams);
		$data["metadata"] = $metadataParams;

		return $data;
	}
}
