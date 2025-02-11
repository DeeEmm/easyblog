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

require_once(JPATH_COMPONENT . '/views/views.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/facebook/instantArticles.php');

class EasyBlogViewLatest extends EasyBlogView
{
	public function display($tpl = null)
	{
		$this->doc->link = EB::_('index.php?option=com_easyblog&view=latest');
		$this->doc->setTitle(JText::_('COM_EASYBLOG_FEEDS_LATEST_TITLE'));
		$this->doc->setDescription(JText::sprintf('COM_EASYBLOG_FEEDS_LATEST_DESC', JURI::root()));

		$posts = $this->getPosts();

		if (!$posts) {
			return;
		}

		$site = $this->jconfig->get('sitename');

		$posts = EB::formatter('list', $posts);

		// Format posts
		foreach ($posts as $post) {

			if (!class_exists('JFeedItem')) {
				require_once(JPATH_ROOT . '/libraries/joomla/document/feed.php');
			}
			$item = new JFeedItem();
			$item->title = $post->title;
			$item->link = $post->getPermalink();

			$author = $post->getAuthor();
			$category = $post->getPrimaryCategory();

			// If the post has a post cover, use it
			$cover = $post->getImage('original', false, true, false);
			$firstImage = false;

			// Check for image in post content
			if (!$cover) {
				$cover = $post->getImage('large', false, true, true);
				if ($cover) {
					$firstImage = true;
				}
			}

			$instantContent = $post->getInstantContent();

			$theme = EB::themes();
			$theme->set('post', $post);
			$theme->set('author', $author);
			$theme->set('content', $instantContent);
			$theme->set('category', $category);
			$theme->set('site', $site);
			$theme->set('config', $this->config);

			$theme->set('cover', $cover);
			$theme->set('firstImage', $firstImage);

			$contentEncoded = $theme->output('site/feeds/default/default');

			EBIA::clean($contentEncoded);

			$item->contentEncoded = $contentEncoded;
			$item->description = $instantContent;
			$item->date = $post->getCreationDate()->toSql();
			$item->category = $post->getPrimaryCategory()->getTitle();
			$item->author = $post->creator->getName();
			$item->authorEmail = $this->getRssEmail($post->creator);

			$feedItems[] = $item;
		}
		

		$this->doc->items = $feedItems;

		$instantRss = $this->render($posts);

		header('Content-Type: application/rss+xml; charset=utf-8');
		$theme = EB::themes();
		$theme->set('instantRss', $instantRss);

		echo $theme->output('site/feeds/instantarticle/default');
		exit;
	}

	/**
	 * Retrieves frontpage posts
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getPosts()
	{
		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');

		// Get the current active menu's properties.
		$params = $this->theme->params;
		$inclusion  = '';

		if ($params) {

			// Get a list of category inclusions
			$inclusion = EB::getCategoryInclusion($params->get('inclusion'));

			if ($params->get('includesubcategories', 0) && !empty($inclusion)) {

				$tmpInclusion = array();

				foreach ($inclusion as $includeCatId) {

					// Retrieve nested categories
					$category = new stdClass();
					$category->id = $includeCatId;
					$category->childs = null;

					EB::buildNestedCategories($category->id, $category);

					$linkage = '';
					EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

					$catIds = array();
					$catIds[] = $category->id;
					EB::accessNestedCategoriesId($category, $catIds);

					$tmpInclusion = array_merge($tmpInclusion, $catIds);
				}

				$inclusion = $tmpInclusion;
			}
		}

		// Get the blogs model
		$model = EB::model('Blog');

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog();
		$excludeIds = array();

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		if (!$this->theme->params->get('post_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		// Backward compatibility for EB5
		$excludedCategories = EB::getExcludedLegacyCategories();

		if ($params->get('exclusion_categories', false)) {
			$excludedCategories = $params->get('exclusion_categories');
		}

		$posts = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, $excludedCategories, $inclusion, null, 'listlength', $this->theme->params->get('post_pin_featured', false));

		return $posts;
	}

	/**
	 * Render the feed.
	 *
	 * @since   3.5
	 */
	public function render($posts)
	{
		$app = JFactory::getApplication();

		// Gets and sets timezone offset from site configuration
		$tz  = new DateTimeZone($app->get('offset'));
		$now = JFactory::getDate();
		$now->setTimeZone($tz);

		$data = $this->doc;

		$url = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		$title = $data->getTitle();

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $data->getTitle());
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $data->getTitle(), $app->get('sitename'));
		}

		$feed_title = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		$datalink = $data->getLink();

		if (preg_match('/[\x80-\xFF]/', $datalink))
		{
			$datalink = implode('/', array_map('rawurlencode', explode('/', $datalink)));
		}

		$feed = "\n<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\">\n";
		$feed .= "  <channel>\n";
		$feed .= "      <title>" . $feed_title . "</title>\n";
		$feed .= "      <description><![CDATA[" . $data->getDescription() . "]]></description>\n";
		$feed .= "      <link>" . str_replace(' ', '%20', $url . $datalink) . "</link>\n";
		$feed .= "      <lastBuildDate>" . htmlspecialchars($now->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</lastBuildDate>\n";

		if (isset($data->image) && $data->image != null)
		{
			$feed .= "      <image>\n";
			$feed .= "          <url>" . $data->image->url . "</url>\n";
			$feed .= "          <title>" . htmlspecialchars($data->image->title, ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "          <link>" . str_replace(' ', '%20', $data->image->link) . "</link>\n";

			if ($data->image->width != '')
			{
				$feed .= "          <width>" . $data->image->width . "</width>\n";
			}

			if ($data->image->height != '')
			{
				$feed .= "          <height>" . $data->image->height . "</height>\n";
			}

			if ($data->image->description != '')
			{
				$feed .= "          <description><![CDATA[" . $data->image->description . "]]></description>\n";
			}

			$feed .= "      </image>\n";
		}

		if ($data->getLanguage() !== '')
		{
			$feed .= "      <language>" . $data->getLanguage() . "</language>\n";
		}

		if (isset($data->copyright) && $data->copyright != '')
		{
			$feed .= "      <copyright>" . htmlspecialchars($data->copyright, ENT_COMPAT, 'UTF-8') . "</copyright>\n";
		}

		if (isset($data->editorEmail) && $data->editorEmail != '')
		{
			$feed .= "      <managingEditor>" . htmlspecialchars($data->editorEmail, ENT_COMPAT, 'UTF-8') . ' ('
				. htmlspecialchars($data->editor, ENT_COMPAT, 'UTF-8') . ")</managingEditor>\n";
		}

		if (isset($data->webmaster) && $data->webmaster != '')
		{
			$feed .= "      <webMaster>" . htmlspecialchars($data->webmaster, ENT_COMPAT, 'UTF-8') . "</webMaster>\n";
		}

		if (isset($data->pubDate) && $data->pubDate != '')
		{
			$pubDate = JFactory::getDate($data->pubDate);
			$pubDate->setTimeZone($tz);
			$feed .= "      <pubDate>" . htmlspecialchars($pubDate->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</pubDate>\n";
		}

		if (!empty($data->category))
		{
			if (is_array($data->category))
			{
				foreach ($data->category as $cat)
				{
					$feed .= "      <category>" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "</category>\n";
				}
			}
			else
			{
				$feed .= "      <category>" . htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8') . "</category>\n";
			}
		}

		for ($i = 0, $count = count($data->items); $i < $count; $i++)
		{
			$itemlink = $data->items[$i]->link;

			if (preg_match('/[\x80-\xFF]/', $itemlink))
			{
				$itemlink = implode('/', array_map('rawurlencode', explode('/', $itemlink)));
			}

			if ((strpos($itemlink, 'http://') === false) && (strpos($itemlink, 'https://') === false))
			{
				$itemlink = str_replace(' ', '%20', $url . $itemlink);
			}

			$feed .= "      <item>\n";
			$feed .= "          <title>" . htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "          <link>" . str_replace(' ', '%20', $itemlink) . "</link>\n";

			if (empty($data->items[$i]->guid))
			{
				$feed .= "          <guid isPermaLink=\"true\">" . str_replace(' ', '%20', $itemlink) . "</guid>\n";
			}
			else
			{
				$feed .= "          <guid isPermaLink=\"false\">" . htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8') . "</guid>\n";
			}

			// $feed .= "          <description><![CDATA[" . $this->relToAbs($data->items[$i]->description) . "]]></description>\n";

			// Instant articles uses <content:encoded> tags.
			if ($data->items[$i]->contentEncoded) {
				$feed .= "          <content:encoded><![CDATA[" . $this->relToAbs($data->items[$i]->contentEncoded) . "]]></content:encoded>\n";
			}

			if (empty($data->items[$i]->category) === false)
			{
				if (is_array($data->items[$i]->category))
				{
					foreach ($data->items[$i]->category as $cat)
					{
						$feed .= "          <category>" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "</category>\n";
					}
				}
				else
				{
					$feed .= "          <category>" . htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8') . "</category>\n";
				}
			}

			if ($data->items[$i]->date != '')
			{
				$itemDate = JFactory::getDate($data->items[$i]->date);
				$itemDate->setTimeZone($tz);
				$feed .= "          <pubDate>" . htmlspecialchars($itemDate->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</pubDate>\n";
			}

			$feed .= "      </item>\n";
		}

		$feed .= "  </channel>\n";
		$feed .= "</rss>\n";

		return $feed;
	}

	protected function relToAbs($text)
	{
		$base = JUri::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data|\/\/)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
