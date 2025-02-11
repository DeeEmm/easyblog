<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerSearch extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Search within EasyBlog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the query
		$query = $this->input->get('query', '', 'string');

		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');
		$query = trim(str_replace($badchars, '', $query));

		// Get category if exists
		$catId = $this->input->get('category_id', 0, 'int');
		$tagIds = $this->input->get('tag_ids', '', 'string');

		$sort = $this->input->get('sort', '', 'string');
		$ordering = $this->input->get('ordering', '', 'string');

		if ($sort == 'hits') {
			$ordering = 'desc';
		}

		$queryUrl = 'index.php?option=com_easyblog&view=search&query=' . $query;

		if ($catId) {
			$queryUrl .= '&category_id=' . $catId;
		}

		if ($tagIds) {
			$tags = explode(',', $tagIds);

			foreach($tags as $tag) {
				$tagId = (int) $tag;
				if ($tagId) {
					$queryUrl .= '&tags[]=' . $tagId;
				}
			}
		}

		if ($sort) {
			$queryUrl .= '&sort=' . $sort . '&ordering=' . $ordering;
		}

		$dispatcher = EB::dispatcher();
		$dispatcher->trigger('onEasyBlogPrepareSearchUrl', [&$queryUrl], 'easyblog');

		$url = EB::_($queryUrl, false);

		$dispatcher->trigger('onEasyBlogAfterRouteSearchUrl', [&$url], 'easyblog');

		$this->app->redirect($url);
	}

	/**
	 * Search blogger
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function blogger()
	{
		// Check for request forgeries
		FH::checkToken();

		// Get the query
		$search = $this->input->get('search', '', 'string');

		$badchars = array('#', '>', '<', '\\', '=', '(', ')', '*', ',', '.', '%', '\'');
		$search = trim(str_replace($badchars, '', $search));

		$url = EB::_('index.php?option=com_easyblog&view=blogger&search=' . $search, false);
		
		return $this->app->redirect($url);
	}
}
