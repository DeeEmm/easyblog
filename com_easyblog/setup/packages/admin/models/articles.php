<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelArticles extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.articles.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->app->getUserStateFromRequest('com_easyblog.articles.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the list of articles for the back end
	 *
	 * @since   5.3.0
	 * @access  public
	 */
	public function getItems($options = array())
	{
		$db = EB::db();
		$filter = $this->getState('filter');

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM `#__content`';
		$query[] = 'WHERE 1';


		if (isset($options['search']) && $options['search']) {
			$search = $options['search'];

			$query[] = 'AND `title` LIKE ' . $db->Quote('%' . $search . '%');
		}

		$db->setQuery($query);

		$this->total = $db->loadResult();

		$query[0] = str_ireplace('COUNT(1)', '*', $query[0]);

		$db->setQuery($query);

		$result = $db->loadObjectList();

				
		return $result;
	}

	/**
	 * Retrieves the pagination
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function getPagination()
	{
		$pagination = EB::pagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Populate current stats
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	protected function populateState()
	{
		// Publishing state
		$state = $this->app->getUserStateFromRequest('com_easyblog.articles.filter_state', 'filter_state');
		$this->setState('filter_state', $state);

		// List state information.
		parent::populateState();
	}
}