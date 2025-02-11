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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

// since backend will also call this rounter file and
// we cannot just throw the error or else user will
// not be able to accesss backend at all.
if (!EB::isFoundryEnabled()) {
	return false;
}

EB::initFoundry();

/**
 * Proxy layer to support Joomla 4.0 and Joomla 3.0
 *
 * @since  5.2
 */
class EasyBlogRouterBase
{
	public static function buildRoute(&$query)
	{

		$segments = array();
		$config = EB::config();

		// index.php?option=com_easyblog&view=latest
		if (isset($query['view']) && $query['view'] == 'latest') {
			unset($query['view']);
		}

		// index.php?option=com_easyblog&view=entry
		if (isset($query['view']) && $query['view'] == 'entry' && isset($query['id'])) {

			// Do something if the query string contain language
			if (isset($query['lang']) && $query['lang']) {

				// Retrieve the current active menu item
				$menu = JFactory::getApplication()->getMenu('site');
				$activeMenu = $menu->getActive();

				// Retrieve the current permalink post data
				$post = EB::post($query['id']);

				// Ensure that is valid post and posted under on different language
				// Ensure that current active menu language is not match with the post language
				// So that we can do something manually build the blog permalink base on the post language
				// If not it will show 404 error on the frontend dashboard post or favourite view.
				if ($activeMenu && $post->id && ($post->language != '*' && $post->language != '') && ($activeMenu->language != $post->language)) {

					// Respect which settings the user configured
					$respectView = $config->get('main_routing_entry');

					// Entry view has higher precedence over all
					$menu = EBR::getMenus('entry', 'entry', $post->id, $post->language);

					// Only check for this if there do not have any entry view menu item
					if ($menu === false) {

						if ($respectView == 'categories') {
							$menu = EBR::getMenus('categories', 'listings', $post->category_id, $post->language);
						}

						if ($respectView == 'blogger') {
							$menu = EBR::getMenus('blogger', 'listings', $post->created_by, $post->language);
						}

						if ($respectView == 'teamblog' && $post->source_type == EASYBLOG_POST_SOURCE_TEAM) {
							$menu = EBR::getMenus('teamblog', 'listings', $post->source_id, $post->language);
						}

						if ($respectView == 'grid') {
							$menu = EBR::getMenus('grid', '', $post->category_id, $post->language);
						}
					}

					// If the system do not find any menu type at above from the current menu structure
					// Then find the main menu like frontpage layout and etc...
					if ($menu === false) {

						$menu = EBR::getMenus('latest', null, null, $post->language);

						if ($menu === false) {
							$menu = EBR::getMenus('grid', null, null, $post->language);
						}

						if ($menu === false) {
							$menu = EBR::getMenus('magazine', null, null, $post->language);
						}

						if ($menu === false) {
							$menu = EBR::getMenus('categories', null, null, $post->language);
						}

						if ($menu === false) {
							$menu = EBR::getMenus('categories', 'listings', null, $post->language);
						}
					}

					// Ensure that found out any menu for this then only override the original query menu item id
					// If not then ignore it because something is wrong on the site without create any menu for these.
					if ($menu) {
						$query['Itemid'] = $menu->id;
					}
				}
			}

			$includeAlias = true;
			$ebRouting = $config->get('main_routing');

			// we need to check if we should add the post's alias into the sef link or not.
			// things to consider if when admin added a menu item to a blog post.
			// if found, we should not include any other segments for this particular post since
			// it should use only the menu item's alias.

			if ($ebRouting == 'default') {

				$menu = JFactory::getApplication()->getMenu('site');
				$activeMenu = $menu->getActive();

				// We need to know if the id should be appended to the menu
				if ($activeMenu && isset($activeMenu->query['id']) && isset($query['id'])
						&& isset($activeMenu->query['view']) && $activeMenu->query['view'] == 'entry'
						&& $activeMenu->query['id'] == $query['id']) {
					$includeAlias = false;
				}

				// the active menu might be diferent from the current catgory we looking. let check from menu item instead
				if (isset($query['id']) && $includeAlias) {
					$itemMenu = EBR::getMenus('entry', 'entry', $query['id']);

					if ($itemMenu && $itemMenu->segments->view == 'entry' && $itemMenu->segments->id == $query['id']) {
						$includeAlias = false;
					}
				}
			}

			if ($includeAlias && $config->get('main_sef') != 'simple' && $config->get('main_sef') != 'simplecategory') {
				$segments[] = EBR::translate($query['view']);
			}

			// Get the post from the cache
			$postId = $query['id'];

			$post = EB::post($postId);

			if (!$post->isPending()) {

				if ($includeAlias && $config->get('main_sef') == 'simplecategory') {
					$segments[]= $post->getPrimaryCategory()->getAlias();
				}

				// Since the cache library is already using the post library to re-render the post table data, just use the permalink.
				if ($includeAlias) {
					$segments[] = $post->getAlias();
				}

				if (isset($query['format']) == 'amp') {
					$segments[] = 'amp';

					unset($query['format']);
				}

				unset($query['id']);
				unset($query['view']);
			}
		}

		// Single category view
		// index.php?option=com_easyblog&view=categories&layout=listings&id=xxxx
		if (isset($query['view']) && $query['view'] == 'categories') {

			// Try to get rid of duplicated view vs menu alias
			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			$menu = JFactory::getApplication()->getMenu('site');
			$activeMenu = $menu->getActive();
			$includeAlias = true;

			// If there is an item id in the url, we should get the menu
			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate($query['view']);
				}
			} else {
				$segments[] = EBR::translate($query['view']);
			}


			// we only need to check if we should include alias only if SEO default routing set to use 'default'.
			// else, we alwyas include.
			$ebRouting = $config->get('main_routing');

			if ($ebRouting == 'default') {

				// We need to know if the id should be appended to the menu
				if ($activeMenu && isset($activeMenu->query['id']) && isset($query['id'])
						&& isset($activeMenu->query['view']) && $activeMenu->query['view'] == 'categories'
						&& isset($activeMenu->query['layout']) && $activeMenu->query['layout'] == 'listings'
						&& $activeMenu->query['id'] == $query['id']) {
					$includeAlias = false;
				}

				// the active menu might be diferent from the current catgory we looking. let check from menu item instead
				if (isset($query['id']) && $includeAlias) {
					$itemMenu = EBR::getMenus('categories', 'listings', $query['id']);

					if ($itemMenu && $itemMenu->segments->view == 'categories' && $itemMenu->segments->layout == 'listings' && $itemMenu->segments->id == $query['id']) {
						$includeAlias = false;
					}

				}
			}

			// Translate the category permalink now
			if (isset($query['id']) && $includeAlias) {
				$category = EB::cache()->get((int) $query['id'], 'category');

				if ($category) {
					$segments[] = $category->getAlias();
				}
			}

			unset($query['id']);
			unset($query['view']);
			unset($query['layout']);
		}

		// Single tag view
		// index.php?option=com_easyblog&view=tags&layout=listings&id=xxxx
		if (isset($query['view']) && $query['view'] == 'tags') {

			// Try to get rid of duplicated view vs menu alias
			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			$menu = JFactory::getApplication()->getMenu('site');
			$activeMenu = $menu->getActive();
			$includeAlias = true;

			// If there is an item id in the url, we should get the menu
			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate($query['view']);
				}
			} else {
				$segments[] = EBR::translate($query['view']);
			}

			// $segments[] = EBR::translate($query['view']);
			if (isset($query['id']) && isset($query['layout'])) {

				$tagId = (int) $query['id'];
				if (EB::cache()->exists($tagId, 'tag')) {
					$tag = EB::cache()->get($tagId, 'tag');
				} else {
					$tag = EB::table('Tag');
					$tag->load((int) $query['id']);

					if (! $tag->id) {
						$tag->load($query['id'], true);
					}
				}

				if ($itemId) {
					$activeMenu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

					// We need to know if the id alias should be appended to the permalink
					if ($activeMenu && isset($activeMenu->query['id']) && isset($query['id'])
							&& isset($activeMenu->query['view']) && $activeMenu->query['view'] == 'tags'
							&& isset($activeMenu->query['layout']) && $activeMenu->query['layout'] == 'tag'
							&& $activeMenu->query['id'] == $query['id']) {
						$includeAlias = false;
					}
				}

				// Translate the tag alias now
				if (isset($query['id']) && $includeAlias) {
					$segments[] = $tag->getAlias();
				}
			}

			unset($query['id']);
			unset($query['view']);
			unset($query['layout']);
		}

		if (isset($query['view']) && $query['view'] == 'grid') {
			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			$menu = JFactory::getApplication()->getMenu('site');
			$activeMenu = $menu->getActive();
			$includeAlias = true;

			// If there is an item id in the url, we should get the menu
			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate('grid');
				}
			} else {
				$segments[] = EBR::translate('grid');
			}
			unset($query['view']);
		}

		if (isset($query['view']) && $query['view'] == 'magazine') {

			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate('magazine');
				}
			} else {
				$segments[] = EBR::translate('magazine');
			}

			unset($query['view']);
		}

		// index.php?option=com_easyblog&view=teamblog&layout=listings&id=xxx
		if(isset($query['view']) && $query['view'] == 'teamblog') {
			// Try to get rid of duplicated view vs menu alias
			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			// If there is an item id in the url, we should get the menu
			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate($query['view']);
				}
			} else {
				$segments[] = EBR::translate($query['view']);
			}


			// $segments[] = EBR::translate($query['view']);

			if (isset($query['layout'])) {
				// $segments[] = EBR::translate($query['layout']);
			}

			if (isset($query['id'])) {
				$team = EB::cache()->get((int) $query['id'], 'team');
				$segments[] = $team->getAlias();
			}

			unset($query['id']);
			unset($query['stat']);
			unset($query['layout']);
			unset($query['view']);
		}

		// view=blogger&layout=listings&id=xxx
		if (isset($query['view']) && $query['view'] == 'blogger') {

			$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

			$menu = JFactory::getApplication()->getMenu('site');
			$activeMenu = $menu->getActive();
			$includeAlias = true;

			// If there is an item id in the url, we should get the menu
			if ($itemId) {
				$menu = JFactory::getApplication()->getMenu('site')->getItem($itemId);

				// Translate the view first
				if ($menu && $menu->query['view'] != $query['view']) {
					$segments[] = EBR::translate($query['view']);
				}
			} else {
				$segments[] = EBR::translate($query['view']);
			}

			// Add bloggers permalink
			if (isset($query['id'])) {
				$author = EB::cache()->get((int) $query['id'], 'author');
				$segments[] = $author->getAlias();
			}

			if (isset($query['sort'])) {
				$segments[]	= EBR::translate('sort');
				$segments[]	= EBR::translate($query['sort']);

				unset($query['sort']);
			}

			unset($query['view']);
			unset($query['id']);
			unset($query['layout']);
		}

		// index.php?option=com_easyblog&view=dashboard&layout=xxx
		if (isset($query['view']) && $query['view'] == 'dashboard') {

			$segments[] = EBR::translate($query['view']);

			if (isset($query['layout'])) {
				$segments[] = EBR::translate($query['layout']);
			}

			if (isset($query['filter'])) {
				$segments[] = $query['filter'];

				unset($query['filter']);
			}

			if (isset($query['blogid'])) {
				$segments[] = $query['blogid'];
				unset($query['blogid']);
			}

			// if (isset($query['postType'])) {
			// 	$segments[] = $query['postType'];
			// 	unset($query['postType']);
			// }

			unset($query['view']);
			unset($query['layout']);
		}

		// index.php?option=com_easyblog&view=archive
		if (isset($query['view']) && $query['view'] == 'archive') {

			$menu = EBR::getMenus($query['view']);

			if (!$menu) {
				$segments[] = EBR::translate($query['view']);
			}

			unset($query['view']);

			if (isset($query['layout'])) {
				$segments[] = $query['layout'];
				unset($query['layout']);
			}

			if (isset($query['archiveyear'])) {
				$segments[] = $query['archiveyear'];
				unset($query['archiveyear']);
			}

			if (isset($query['archivemonth'])) {
				$segments[] = $query['archivemonth'];
				unset($query['archivemonth']);
			}

			if (isset($query['archiveday'])) {
				$segments[] = $query['archiveday'];
				unset($query['archiveday']);
			}
		}

		// index.php?option=com_easyblog&view=calendar
		if (isset($query['view']) && $query['view'] == 'calendar') {

			$menu = EBR::getMenus($query['view']);

			if (!$menu) {
				$segments[] = EBR::translate($query['view']);
			}

			unset($query['view']);

			if (isset($query['year'])) {
				$segments[] = $query['year'];
				unset($query['year']);
			}

			if (isset($query['month'])) {
				$segments[] = $query['month'];
				unset($query['month']);
			}

			if (isset($query['day'])) {
				$segments[] = $query['day'];
				unset($query['day']);
			}

			if (isset($query['layout'])) {
				$segments[] = EBR::translate($query['layout']);
				unset($query['layout']);
			}
		}

		// index.php?option=com_easyblog&view=search
		if (isset($query['view']) && $query['view'] == 'search') {
			$segments[] = EBR::translate($query['view']);
			unset($query['view']);

			if (isset($query['layout'])) {
				$segments[] = $query['layout'];
				unset($query['layout']);
			}

			// we shouldn't sef the query as it become tricky
			// if user are searching with - or : characters.
			// # 2184

			// if (isset($query['query'])) {

			// 	// we need to rawurlencode on query string to be used as url segments
			// 	// to avoid invalid characters like percentage (%)
			// 	// # 1919

			// 	$segments[] = rawurlencode($query['query']);
			// 	unset($query['query']);
			// }
		}

		// Social network authentication
		if (isset($query['view']) && $query['view'] == 'auth') {
			$segments[] = $query['view'];
			unset($query['view']);

			if (isset($query['type'])) {
				$segments[] = $query['type'];
				unset($query['type']);
			}

			if (isset($query['system'])) {
				$segments[] = 'system';
				unset($query['system']);
			}
		}

		// index.php?option=com_easyblog&view=composer
		if (isset($query['view']) && $query['view'] == 'composer') {
			$segments[] = $query['view'];

			unset($query['view']);
		}

		// index.php?option=com_easyblog&view=login
		if (isset($query['view']) && $query['view'] == 'login') {
			$segments[] = EBR::translate($query['view']);
			unset($query['view']);
		}


		if (isset($query['type'])) {
			if (!isset($query['format']) && !isset($query['controller'])) {
				$segments[] = $query['type'];
				unset($query['type']);
			}
		}

		if (isset($query['view']) && $query['view'] == 'subscription') {
			$segments[] = $query['view'];

			unset($query['view']);
		}

		// if the limistart is 0, we dont include limitstart segment.
		if (isset($query['limitstart']) && !$query['limitstart']) {
			unset($query['limitstart']);
		}

		if (!isset($query['Itemid'])) {
			$query['Itemid'] = EBR::getItemId();
		}

		if (isset($query['lang'])) {
			unset($query['lang']);
		}

		return $segments;
	}

	public static function parseRoute(&$segments)
	{
		// Load site's language file
		EB::loadLanguages();

		$vars = array();
		$active = JFactory::getApplication()->getMenu('site')->getActive();
		$config = EB::config();

		$count = count($segments);
		$isAmp = false;

		// If the last segment is amp, means this is amp format
		if ($segments[($count - 1)] == 'amp') {
			$isAmp = true;

			array_pop($segments);
		}

		// RSD View
		if (isset($segments[0]) && $segments[0] == 'rsd') {
			$vars['view'] = 'rsd';

			return $vars;
		}

		// Feed view
		if (isset($segments[1])) {
			if ($segments[1] == 'rss' || $segments[1] == 'atom') {
				$vars['view']	= $segments[0];
				unset($segments);
				return $vars;
			}
		}

		// for now we dont translate limistart
		// check for the pagination
		// $count = count($segments);
		// $lastSegment = $segments[$count - 1];

		// if (EBString::strpos($lastSegment, EBR::translate('page') . ':') !== false) {
		// 	// The last segment is the pagination variable.
		// 	// Let process it and remove the last segment for further processing.
		// 	$tmp = explode(':', $lastSegment);
		// 	$vars['limitstart'] = $tmp[1];

		// 	// now remove the last segment.
		// 	array_pop($segments);
		// }

		// We know that the view=categories&layout=listings&id=xxx because there's only 1 segment
		// and the active menu is view=categories
		if (isset($active) && $active->query['view'] == 'categories' && count($segments) == 1) {

			$category = EB::table('Category');
			$category->load(array('alias' => $segments[0]));

			// if still can't get the correct category id try this
			if (!$category->id) {
				$categoryAlias = $segments[0];
				$categoryAlias = str_replace(':', '-', $categoryAlias);

				// Check if Unicode alias is enabled or not
				if (EBR::isIDRequired()) {

					// If enabled, we need to get the id from the alias
					$categoryParts = explode('-', $categoryAlias);

					$xId = array_shift($categoryParts);

					// now glue the alias string.
					$xAlias = implode('-', $categoryParts);

					// we need to make sure this is a valid category by loading the alias as id might has the change to conflict with post id.
					$category->load(array('alias' => $xAlias));

				} else {
					$category->load(array('alias' => $categoryAlias));
				}
			}

			// Only force this when we can find a category id.
			if ($category->id) {
				$vars['view'] = 'categories';
				$vars['layout'] = 'listings';
				$vars['id'] = $category->id;

				return $vars;
			}
		}

		// Active menu item with view=tags
		if (isset($active) && $active->query['view'] == 'tags' && count($segments) == 1) {

			$tag = EB::table('Tag');
			$tag->load($segments[0]);

			if (!$tag->id || true) {
				$tagAlias = $segments[0];
				$tagAlias = str_replace(':', '-', $tagAlias);

				// Check if Unicode alias is enabled or not
				if (EBR::isIDRequired()) {

					// If enabled, we need to get the id from the alias
					$tagParts = explode('-', $tagAlias);

					// Get the id
					$xId = array_shift($tagParts);

					// Let's try to load the tag again
					$tag->load($xId);
				} else {
					// Directly load from alias
					$tag->load($tagAlias, true);
				}
			}

			// Only force this when we can find a tag id.
			if ($tag->id) {
				$vars['view'] = 'tags';
				$vars['layout'] = 'tag';
				$vars['id'] = $tag->id;

				return $vars;
			}
		}

		// Active menu item with view=blogger
		if (isset($active) && $active->query['view'] == 'blogger') {

			$views = EBR::getSystemViews();

			if (!in_array($segments[0], $views)) {

				// this segment might be the valid user permalink. lets do a test here.
				if (count($segments) == 1) {
					$permalink = $segments[0];

					// for permalink that has a dash, joomla will convert it into colon character.
					// here we need to convert back the colon into dash.
					$permalink = str_replace(':', '-', $permalink);

					// For unicode urls we definitely know that the author's id would be in the form of ID-title
					if (EBR::isIDRequired()) {
						$permalink = explode('-', $permalink);
						$permalink = isset($permalink[1]) ? $permalink[1] : '';
					}

					$userid = '';
					if ($permalink) {
						$userid = EB::getUserId($permalink);

						if (!$userid) {
							$userid = EB::getUserId(EBString::str_ireplace('-', ' ', $permalink));
						}

						if (!$userid) {
							$userid = EB::getUserId(EBString::str_ireplace('-', '_', $permalink));
						}
					}

					if ($userid) {
						// Splice blogger view into the segment's first key
						array_splice($segments, 0, 0, EBR::translate('blogger'));
					}
				}

				// check if this is a all bloger sorting url.
				if (count($segments) > 1) {
					// check if this is all bloger sorting or not.
					if ($segments[0] == EBR::translate('sort') || $segments[0] == 'sort') {
						array_unshift($segments, EBR::translate('blogger'));
					}
				}
			}
		}


		// Active menu item with view=tags
		if (isset($active) && $active->query['view'] == 'teamblog' && count($segments) == 1) {

			$team = EB::table('TeamBlog');
			$team->load($segments[0]);

			if (!$team->id || true) {
				$teamAlias = $segments[0];
				$teamAlias = str_replace(':', '-', $teamAlias);

				// Check if Unicode alias is enabled or not
				if (EBR::isIDRequired()) {

					// If enabled, we need to get the id from the alias
					$teamParts = explode('-', $teamAlias);

					// Get the id
					$xId = array_shift($teamParts);

					// Let's try to load the tag again
					$team->load($xId);
				} else {
					// Directly load from alias
					$team->load($teamAlias, true);
				}
			}

			// Only force this when we can find a tag id.
			if ($team->id) {
				$vars['view'] = 'teamblog';
				$vars['layout'] = 'listings';
				$vars['id'] = $team->id;

				return $vars;
			}
		}


		// If user chooses to use the simple sef setup, we need to add the proper view
		if (($config->get('main_sef') == 'simple' && count($segments) == 1) ||
			($config->get('main_sef') == 'simplecategory' && count($segments) == 1) ||
			($config->get('main_sef') == 'simplecategory' && count($segments) == 2)) {

			// for $config->get('main_sef') == 'simplecategory' && count($segments) == 1,
			// this happened when site admin setup the EB menu item as home menu and the alias of this menu item
			// is the same as the category's alias.
			// #2272

			$views = EBR::getSystemViews();

			if (!in_array($segments[0], $views)) {
				if (count($segments) == 2) {
					// if the 1st element is not a view, most likely this is simplecategory type. Lets replace the 1st element with entry view.
					$segments[0] = EBR::translate('entry');
				} else {
					array_unshift($segments, EBR::translate('entry'));
				}
			}

			if (in_array($segments[0], $views) && count($segments) == 1) {
				$tmpPost = null;

				// lets chec if this segments belong to post or not.
				if (EBR::isIDRequired()) {
					// If unicode alias is enabled, just explode the data
					$tmp = explode(':', $segments[0]);
					$tmpPost = EB::post($tmp[0]);
				} else {
					$tmpPost = EB::post();
					$tmpPost->loadByPermalink($segments[0]);
				}

				if ($tmpPost && $tmpPost->id) {
					array_unshift($segments, EBR::translate('entry'));
				}
			}
		}

		// Composer view
		if (isset($segments[0]) && $segments[0] == EBR::translate('composer')) {
			$vars['view'] = 'composer';
		}

		// Entry view
		if (isset($segments[0]) && $segments[0] == EBR::translate('entry')) {
			$count	= count($segments);
			$entryId    = '';

			// perform manual split on the string.
			if (EBR::isIDRequired() && $count > 1) {
				$permalinkSegment = $segments[($count - 1)];
				$permalinkArr = explode(':', $permalinkSegment);
				$entryId = $permalinkArr[0];
			} else {
				$index = ($count - 1);
				$alias = $segments[$index];

				// There could be instances where it has &highlight=xxx in the query string
				$tmp = explode('&highlight', $alias);
				$alias = $tmp[0];

				$post = EB::post();
				$post->loadByPermalink($alias);

				if ($post) {
					$entryId = $post->id;
				}
			}

			if ($entryId) {
				$vars['id'] = $entryId;
			}

			// AMP view
			if ($isAmp) {
				$vars['format'] = 'amp';

				// reset the jdocuement to raw and set the type to 'amp'
				JFactory::$document = new JDocumentRaw();
				$doc = JFactory::getDocument();
				$doc->setType('amp');
			}

			$vars['view'] = 'entry';
		}

		// Calendar view
		if ((isset($segments[0]) && $segments[0] == EBR::translate('calendar')) || (isset($active) && $active->query['view'] == 'calendar')) {

			$vars['view'] = 'calendar';

			$xActive = $active->query['view'] == 'calendar' ? true : null;

			$totalSegments = count($segments);
			$counter = 0;

			if (!$xActive) {
				$totalSegments	= $totalSegments - 1;
				$counter++;
			} else if ($segments[0] == EBR::translate('entry')) {

				// There are instances where the first segments will become entry
				// if sef is set to use 'simple' or 'simplecategory'. #1432
				unset($segments[0]);
				$segments = array_values($segments);

				$totalSegments = $totalSegments - 1;
			}

			if ($totalSegments >= 1) {

				// First segment is always the year
				if (isset($segments[$counter])) {

					if ($segments[$counter] == EBR::translate('calendarView') || $segments[$counter] == EBR::translate('listView')) {
						$vars['layout'] = $segments[$counter];
					} else {
						$vars['year'] = $segments[$counter];
					}
				}

				$counter++;

				// Second segment is always the month
				if (isset($segments[$counter])) {

					if ($segments[$counter] == EBR::translate('calendarView')  || $segments[$counter] == EBR::translate('listView')) {
						$vars['layout'] = $segments[$counter];
					} else {
						$vars['month'] = $segments[$counter];
					}
				}

				$counter++;

				// Third segment is always the day
				if (isset($segments[$counter])) {

					if ($segments[$counter] == EBR::translate('calendarView')  || $segments[$counter] == EBR::translate('listView')) {
						$vars['layout'] = $segments[$counter];
					} else {

						// prevent this possibilities issue if the day returning string e.g calendarview
						if (is_numeric($segments[$counter])) {
							$vars['day'] = $segments[$counter];
						}
					}
				}

				$counter++;

				// Fourth segments will always be calendarview or listview
				if (isset($segments[$counter])) {
					$vars['layout'] = $segments[$counter];
				}
			}
		}

		if (isset($segments[ 0 ]) && $segments[ 0 ] == EBR::translate('archive')) {
			$vars[ 'view' ]	= 'archive';

			$count = count($segments);
			$totalSegments = $count - 1;

			if ($totalSegments >= 1) {
				$indexSegment = 1;

				if ($segments[ 1 ] == 'calendar') {
					$vars[ 'layout' ] = 'calendar';
					$indexSegment = 2;
				}

				// First segment is always the year
				if (isset($segments[ $indexSegment ])) {
					$vars[ 'archiveyear' ] = $segments[ $indexSegment ];
				}

				// Second segment is always the month
				if (isset($segments[ $indexSegment + 1 ])) {
					$vars[ 'archivemonth' ] = $segments[ $indexSegment + 1 ];
				}

				// Third segment is always the day
				if (isset($segments[ $indexSegment + 2 ])) {
					$vars[ 'archiveday' ] = $segments[ $indexSegment + 2 ];
				}
			}

		}

		// Process categories sef links
		// index.php?option=com_easyblog&view=categories
		if (isset($segments[0]) && $segments[0] == EBR::translate('categories')) {

			// Set the view
			$vars['view'] = 'categories';

			// Get the total number of segments
			$count = count($segments);

			// Ensure that the first index is not a system layout
			$layouts = array('listings');

			if ($count == 2 && !in_array($segments[1], $layouts)) {

				$id = null;

				// If unicode alias is enabled, just explode the data
				if (EBR::isIDRequired()) {
					$tmp = explode(':', $segments[1]);
					$id = $tmp[0];
				}

				// Encode segments
				$segments = EBR::encodeSegments($segments);

				if (!$id) {
					$category = EB::table('Category');
					$category->load(array('alias' => $segments[1]));

					$id = $category->id;
				}

				$vars['id'] = $id;
				$vars['layout']	= 'listings';
			}

			// index.php?option=com_easyblog&view=categories&layout=simple
			if ($count == 2 && in_array($segments[1], $layouts)) {
				$vars['layout']	= $segments[1];
			}
		}

		if (isset($segments[0]) && $segments[0] == EBR::translate('tags')) {
			$count	= count($segments);

			if ($count > 1) {
				$tagId = '';
				if (EBR::isIDRequired()) {
					// perform manual split on the string.
					$permalinkSegment = $segments[ ($count - 1) ];
					$permalinkArr = explode(':', $permalinkSegment);
					$tagId = $permalinkArr[0];
				}

				$segments = EBR::encodeSegments($segments);
				if (empty($tagId)) {
					$table	= EB::table('Tag');
					$table->load($segments[ ($count - 1) ] , true);
					$tagId  = $table->id;
				}

				$vars[ 'id' ] = $tagId;
				$vars['layout']	= 'tag';
			}
			$vars[ 'view' ]	= 'tags';
		}

		// view=blogger&layout=listings&id=xxx
		if (isset($segments[0]) && $segments[0] == EBR::translate('blogger')) {

			$vars[ 'view' ]	= 'blogger';

			$count	= count($segments);

			if ($count > 1) {

				if ($count == 3) {
					// this is bloggers sorting page
					$vars['sort'] = $segments[2];

				} else {

					// Default user id
					$id = 0;

					// Parse the segments
					$segments = EBR::encodeSegments($segments);

					// For unicode urls we definitely know that the author's id would be in the form of ID-title
					if (EBR::isIDRequired()) {
						$permalink = explode(':', $segments[1]);
						$id = $permalink[0];
					}

					if (!$id) {

						// Try to get the user id
						$permalink = $segments[1];

						$id = EB::getUserId($permalink);

						if (!$id) {
							$id = EB::getUserId(EBString::str_ireplace('-', ' ', $permalink));
						}

						if (!$id) {
							$id = EB::getUserId(EBString::str_ireplace('-', '_', $permalink));
						}
					}

					if ($id) {
						$vars['layout'] = 'listings';
						$vars['id']	= $id;
					}

				}// if count > 3
			}
		}

		if (isset($segments[0]) && $segments[0] == EBR::translate('dashboard')) {
			// Encode it for prevent those segments contains e.g. dash.
			$segments = EBR::encodeSegments($segments);

			$count = count($segments);

			if ($count > 1) {

				switch (EBR::translate($segments[1])) {
					case EBR::translate('write'):
						$vars['layout']	= 'write';
					break;
					case EBR::translate('profile'):
						$vars['layout']	= 'profile';
					break;
					case EBR::translate('drafts'):
						$vars['layout']	= 'drafts';
					break;
					case EBR::translate('entries'):
						$vars['layout']	= 'entries';
					break;
					case EBR::translate('favourites'):
						$vars['layout']	= 'favourites';
					break;
					case EBR::translate('reports'):
						$vars['layout']	= 'reports';
					break;
					case EBR::translate('comments'):
						$vars['layout']	= 'comments';
					break;
					case EBR::translate('categories'):
						$vars['layout']	= 'categories';
					break;
					case EBR::translate('requests');
						$vars['layout'] = 'requests';
					break;
					case EBR::translate('listCategories'):
						$vars['layout']	= 'listCategories';
					break;
					case EBR::translate('category'):
						$vars['layout']	= 'category';
					break;
					case EBR::translate('tags'):
						$vars['layout']	= 'tags';
					break;
					case EBR::translate('review'):
						$vars['layout']	= 'review';
					break;
					case EBR::translate('pending'):
						$vars['layout']	= 'pending';
					break;
					case EBR::translate('revisions'):
						$vars['layout'] = 'revisions';
					break;
					case EBR::translate('teamblogs'):
						$vars['layout']	= 'teamblogs';
					break;
					case EBR::translate('teamblogForm'):
						$vars['layout']	= 'teamblogForm';
					break;
					case EBR::translate('quickpost'):
						$vars['layout']	= 'quickpost';
					break;
					case EBR::translate('moderate'):
						$vars['layout']	= 'moderate';
					break;
					case EBR::translate('templates'):
						$vars['layout'] = 'templates';
					break;
					case EBR::translate('templateform'):
						$vars['layout'] = 'templateform';
					break;
					case EBR::translate('compare'):
						$vars['layout'] = 'compare';
					break;
					case EBR::translate('tagForm'):
						$vars['layout'] = 'tagForm';
					break;
					case EBR::translate('autoposting'):
						$vars['layout'] = 'autoposting';
					break;
					case EBR::translate('categoryForm'):
						$vars['layout'] = 'categoryForm';
					break;
					case EBR::translate('polls'):
						$vars['layout'] = 'polls';
					break;
					case EBR::translate('blockTemplates'):
						$vars['layout'] = 'blockTemplates';
					break;
					case EBR::translate('blockTemplatesForm'):
						$vars['layout'] = 'blockTemplatesForm';
					break;
				}

				// Check if there's any default type
				if (isset($vars['layout']) && $vars['layout'] == 'quickpost' && isset($segments[2])) {
					$vars['type'] = $segments[2];
				}

				if (isset($vars['layout']) && $vars['layout'] == 'compare' && isset($segments[2])) {
					$vars['blogid'] = $segments[2];
				}

				if (isset($vars['layout']) && $vars['layout'] == 'entries' && isset($segments[2])) {

					$vars['filter'] = $segments[2];

				} else {
					if (isset($segments[2])) {
						$vars['filter'] = $segments[2];
					}
				}
			}
			$vars['view'] = 'dashboard';
		}

		if (isset($segments[0]) && $segments[0] == EBR::translate('teamblog')) {
			$count = count($segments);

			if ($count > 1) {
				$rawSegments = $segments;
				$segments = EBR::encodeSegments($segments);

				if (EBR::isIDRequired()) {
					// perform manual split on the string.
					$permalinkSegment = $rawSegments[1];
					$permalinkArr = explode(':', $permalinkSegment);
					$teamId = $permalinkArr[0];
				} else {

					$permalink = $segments[1];

					$table = EB::table('TeamBlog');
					$loaded = $table->load($permalink , true);

					if (!$loaded) {
						$name = $segments[1];
						$name = EBString::str_ireplace(':' , ' ' , $name);
						$name = EBString::str_ireplace('-', ' ' , $name);
						$table->load($name , true);
					}

					$teamId = $table->id;
				}
				$vars['id'] = $teamId;
				$vars['layout']	= 'listings';
			}

			$vars[ 'view' ]	= 'teamblog';
		}

		if (isset($segments[0]) && $segments[0] == EBR::translate('search')) {
			$count	= count($segments);
			if ($count == 2) {
				$vars['layout'] = EBR::translate($segments[1]);
			}

			$vars['view'] = 'search';
		}

		// // http://site.com/auth/linkedin
		if (isset($segments[0]) && $segments[0] == 'auth') {
			$vars['view'] = $segments[0];
			$vars['type'] = $segments[1];

			// http://site.com/auth/linkedin/system
			if (isset($segments[2]) && $segments[2] == 'system') {
				$vars['system'] = 1;
			}
		}

		$count	= count($segments);
		if ($count == 1) {
			switch(EBR::translate($segments[0]))
			{
				case EBR::translate('latest'):
					$vars['view'] = 'latest';
					break;
				case EBR::translate('featured'):
					$vars['view'] = 'featured';
					break;
				case EBR::translate('images'):
					$vars['view'] = 'images';
					break;
				case EBR::translate('login'):
					$vars['view'] = 'login';
					break;
				case EBR::translate('myblog'):
					$vars['view'] = 'myblog';
					break;
				case EBR::translate('ratings'):
					$vars['view'] = 'ratings';
					break;
				case EBR::translate('subscription'):
					$vars['view'] = 'subscription';
					break;
			}
		}

		if (! $vars) {
			// someting is not right here.
			throw EB::exception(JText::_('COM_EASYBLOG_PAGE_IS_NOT_AVAILABLE'), 404);
		}

		return $vars;
	}

}

if (EB::isFoundryEnabled() && FH::isJoomla4()) {
	/**
	 * Routing class to support Joomla 4.0
	 *
	 * @since  5.2
	 */
	class EasyblogRouter extends Joomla\CMS\Component\Router\RouterBase
	{
		public function build(&$query)
		{
			$segments = EasyBlogRouterBase::buildRoute($query);
			return $segments;
		}

		public function parse(&$segments)
		{
			$vars = EasyBlogRouterBase::parseRoute($segments);

			// look like we have to manually reset the segments so that we will not hit this error:
			// Uncaught Joomla\CMS\Router\Exception\RouteNotFoundException: URL invalid in /libraries/src/Router/Router.php on line 152
			$segments = array();

			return $vars;
		}
	}
}

if (EB::isFoundryEnabled()) {
	/**
	 * Routing class to support Joomla 3.0
	 *
	 * @since  5.0
	 */
	function EasyBlogBuildRoute(&$query)
	{
		$segments = EasyBlogRouterBase::buildRoute($query);
		return $segments;
	}

	/**
	 * Routing class to support Joomla 4.0
	 *
	 * @since  5.0
	 */
	function EasyBlogParseRoute(&$segments)
	{
		$vars = EasyBlogRouterBase::parseRoute($segments);
		return $vars;
	}
}

// foundry not found. let return custom router and parser that always
// go to eb latest page.
if (!EB::isFoundryEnabled()) {
	/**
	 * Routing class to support Joomla 3.0
	 *
	 * @since  5.0
	 */
	function EasyBlogBuildRoute(&$query)
	{
		if (isset($query['view'])) {
			unset($query['view']);
		}

		if (isset($query['type'])) {
			unset($query['type']);
		}

		// if the limistart is 0, we dont include limitstart segment.
		if (isset($query['limitstart']) && !$query['limitstart']) {
			unset($query['limitstart']);
		}

		if (!isset($query['Itemid'])) {
			$query['Itemid'] = EBR::getItemId();
		}

		if (isset($query['lang'])) {
			unset($query['lang']);
		}

		return $segments;
	}

	/**
	 * Routing class to support Joomla 4.0
	 *
	 * @since  5.0
	 */
	function EasyBlogParseRoute(&$segments)
	{
		$vars['view'] = 'latest';
		return $vars;
	}
}
