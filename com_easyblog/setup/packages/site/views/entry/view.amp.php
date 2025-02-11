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

class EasyBlogViewEntry extends EasyBlogView
{
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_amp')) {

			$id = $this->input->get('id', 0, 'int');

			return $this->app->redirect(EBR::_('index.php?option=com_easyblog&view=entry&id=' . $id, false));
		}

		//for trigger
		$params	= $this->app->getParams('com_easyblog');

		$id = $this->input->get('id', 0, 'int');

		if (empty($id)) {
			throw EB::exception(JText::_('COM_EASYBLOG_BLOG_NOT_FOUND'), 404);
		}

		$my = JFactory::getUser();

		$post = EB::post($id);

		$post = EB::formatter('entry', $post);

		// Check if blog is password protected.
		if ($post->isPasswordProtected()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($post->isTrashed()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Check if the blog post is trashed
		if (!$post->isPublished() && $this->my->id != $post->created_by && !FH::isSiteAdmin()) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		// Check for team's privacy
		$allowed = $this->checkTeamPrivacy($post);

		if ($allowed === false) {
			throw EB::exception(JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY'), 404);
		}

		// Check if the blog post is accessible.
		$accessible = $post->isAccessible();

		if (!$accessible->allowed) {
			echo $accessible->error;

			return;
		}

		// Prepare related post
		$relatedPosts = array();

		// Load up the blog model
		$model = EB::model('Blog');

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		if ($params->get('post_related', true)) {
			$limit = (int) $params->get('post_related_limit', 5);
			if ($limit < 0) {
				$limit = 5;
			}

			$behavior = $params->get('post_related_behavior', 'tags');
			$options = array('newness' => $params->get('post_related_interval', 180));

			$relatedPosts = $model->getRelatedPosts($post->id, $limit, $behavior, $post->category->id, $post->getTitle(), $options);
		}

		// Format the post
		$post = EB::formatter('entry', $post);

		// Process the post cover image
		$coverInfo = false;

		if ($post->image) {
			$media = EB::mediamanager();
			$imagePath = $media->getPath($post->image);

			$imageData = @getimagesize($imagePath);

			// If height is missing, we skip showing the image
			if (!empty($imageData[1])) {
				$coverInfo = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';
			}
		}

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id, false, null, false, true);

		// If there is a canonical link for the post, it should have the highest precedence
		if ($post->canonical) {
			$url = $post->canonical;
		}

		$join = EBR::isSefEnabled() ? '?' : '&';

		$relatedUrl = $post->getPermalink(true, true) . $join . 'layout=related&format=json';

		// For the related post url, it must be https:// or //
		$relatedUrl = str_replace("http://", "//", $relatedUrl);

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->ampHtml($post);

		// Get the menu items to render amp sidebar
		$menuItems = $this->getMenuItem();

		// Get Publisher data
		$jConfig = EB::jConfig();
		$siteName = $jConfig->get('sitename');

		// RTL compatibility
		$lang = JFactory::getLanguage();

		$theme 	= EB::themes();

		// If a custom theme is setup for entries in the category, set a different theme
		if (!empty($post->category->theme)) {
			$theme->setCategoryTheme($post->category->theme);
		}

		$blocks = $post->getBlocks();
		$availableBlocks = array();

		if (!empty($blocks)) {
			foreach ($blocks as $block) {
				$availableBlocks[] = $block->type;

				// Get blocks inside block
				$this->getAvailableBlocks($block, $availableBlocks);
			}
		}

		$ampContent = $post->getAMPContent();

		// Simulate onAfterRender since amp page does not trigger this
		// Replace index.php URI by SEF URI.
		if (strpos($ampContent, 'href="index.php?') !== false) {
			preg_match_all('#href="index.php\?([^"]+)"#m', $ampContent, $matches);

			foreach ($matches[1] as $urlQueryString) {
				$ampContent = str_replace(
					'href="index.php?' . $urlQueryString . '"',
					'href="' . trim('', '/') . JRoute::_('index.php?' . $urlQueryString) . '"',
					$ampContent
				);
			}
		}

		$logoObj = $this->getLogoObject();
		$ampImageUrl = $post->getImage('amp', false, true);

		if (empty($ampImageUrl)) {
			$ampImageUrl = EB::getAmpPlaceholderImage();
		}

		$pageTitle = $post->getPagePostTitle();

		$socialEnabled = $this->socialEnabled($params);

		$theme->set('pageTitle', $pageTitle);
		$theme->set('ampContent', $ampContent);
		$theme->set('post', $post);
		$theme->set('url', $url);
		$theme->set('relatedPosts', $relatedPosts);
		$theme->set('relatedUrl', $relatedUrl);
		$theme->set('entryParams', $params);
		$theme->set('adsense', $adsense);
		$theme->set('menuItems', $menuItems);
		$theme->set('coverInfo', $coverInfo);
		$theme->set('siteName', $siteName);
		$theme->set('logoObj', $logoObj);
		$theme->set('langTag', $lang->getTag());
		$theme->set('isRtl', $lang->isRTL());
		$theme->set('availableBlocks', $availableBlocks);
		$theme->set('ampImageUrl', $ampImageUrl);
		$theme->set('socialEnabled', $socialEnabled);

		$blogHtml = $theme->output('site/entry/amp/default');

		echo $blogHtml;
		exit;
	}

	/**
	 * Make social buttons are enabled
	 *
	 * @since   5.3
	 * @access  public
	 */
	public function socialEnabled($params)
	{
		if ($params->get('post_social_buttons', true) && $this->config->get('social_amp', true)) {
			return true;
		}

		return false;
	}

	/**
	 * Get available block in post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAvailableBlocks($blockObj, &$availableBlocks)
	{
		if (!empty($blockObj->blocks)) {
			foreach ($blockObj->blocks as $block) {

				if (in_array($block->type, $availableBlocks)) {
					continue;
				}

				$availableBlocks[] = $block->type;

				// Get block inside block
				$this->getAvailableBlocks($block, $availableBlocks);
			}
		}
	}

	/**
	 * Retrieve a logo object for structured data
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getLogoObject()
	{
		$logoUrl = EB::getLogo();

		$logoPath = EB::string()->abs2rel($logoUrl);

		$logoData = @getimagesize($logoPath);
		$logoObj = false;

		if ($logoData) {
			$logoObj = new stdClass;
			$logoObj->url = $logoUrl;
			$logoObj->width = $logoData[0];
			$logoObj->height = $logoData[1];
		}

		return $logoObj;
	}

	/**
	 * Get a menu item from the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getMenuItem()
	{
		// Get the menu items to render amp sidebar
		$menuType = $this->config->get('amp_sidebar_menu', 'mainmenu');

		if (empty($menuType)) {
			return false;
		}

		$menu = JFactory::getApplication()->getMenu();
		$items = $menu->getItems('menutype', $menuType);

		if (!$items) {
			return false;
		}

		foreach ($items as $i => &$item) {

			// We need to respect the menu item hidden setting
			if (($item->getParams()->get('menu_show', 1) == 0)) {
				unset($items[$i]);
				continue;
			}

			self::buildRoute($item);
		}

		return $items;
	}

	public static function buildRoute(&$item)
	{
		$item->flink  = $item->link;

		// Reverted back for CMS version 2.5.6
		switch ($item->type) {
			case 'separator':
			case 'heading':
				// No further action needed.
				break;

			case 'url':
				if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
					// If this is an internal Joomla link, ensure the Itemid is set.
					$item->flink = $item->link . '&Itemid=' . $item->id;
				}
				break;

			case 'alias':
				// If this is an alias use the item id stored in the parameters to make the link.
				$item->flink = 'index.php?Itemid=' . $item->getParams()->get('aliasoptions');
				break;

			default:
				$item->flink .= '&Itemid=' . $item->id;

				$mode = EBRouter::getMode();

				if ($mode == EASYBLOG_JROUTER_MODE_SEF) {
					$item->flink = 'index.php?Itemid=' . $item->id;
				}

				break;
		}

		if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
			$item->flink = JRoute::_($item->flink, true, $item->getParams()->get('secure'));
		} else {
			$item->flink = JRoute::_($item->flink);
		}
	}

	/**
	 * Determines if the user is allowed to view this post if this post is associated with a team.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function checkTeamPrivacy(EasyBlogPost &$blog)
	{
		$id = $blog->getTeamAssociation();

		// This post is not associated with any team, so we do not need to check anything on the privacy
		if (!$id) {
			return true;
		}

		$team = EB::table('TeamBlog');
		$team->load($id);

		// If the team access is restricted to members only
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember($this->my->id) && !FH::isSiteAdmin()) {
			return false;
		}

		// If the team access is restricted to registered users, ensure that they are logged in.
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED && $this->my->guest) {
			echo EB::showLogin();

			return false;
		}

		return true;
	}
}
