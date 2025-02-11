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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelMetas extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;
	public $data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.meta.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getItemsTotal($type = '')
	{
		// Lets load the content if it doesn't already exist
		if (!$this->total) {
			$query = $this->buildItemsQuery($type);
			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getItemsPagination($type = '')
	{
		// Lets load the content if it doesn't already exist
		if (!$this->pagination) {
			$total = $this->getItemsTotal($type);
			$this->pagination = EB::Pagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function buildItemsQuery($type = '')
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->buildItemsQueryWhere($type);
		$db = EB::db();

		$query	= 'SELECT * FROM (';

		if ($type == 'post' || $type == '') {
			$query .= ' (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, if(m.title != \'\', ifnull(m.title, p.title), p.title) AS title';
			$query .= '  FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query .= '  INNER JOIN ' . $db->nameQuote('#__easyblog_post') . ' AS p';
			$query .= '  ON m.content_id = p.id';
			$query .= ' AND p.' . $db->quoteName('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);
			$query .= '  WHERE m.`type` = ' . $db->Quote('post') . ')';

		}

		if (!empty($query) && $type == '') {
			$query	.= ' UNION ';
		}

		if ($type == 'team' || $type == '') {
			$query	.= '  (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, if(m.title != \'\', ifnull(m.title, p.title), p.title) AS title';
			$query	.= '   FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query	.= '   LEFT JOIN ' . $db->nameQuote('#__easyblog_team') . ' AS p';
			$query	.= '   ON m.content_id = p.id';
			$query	.= '   WHERE m.`type` = ' . $db->Quote('team') . ')';
		}

		if (!empty($query) && $type == '') {
			$query	.= ' UNION ';
		}

		if ($type == 'blogger' || $type == '') {
			$query	.= '  (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, if(m.title != \'\', m.title, p.name) AS title';
			$query	.= '   FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query	.= '   LEFT JOIN ' . $db->nameQuote('#__users') . ' AS p';
			$query	.= '   ON m.content_id = p.id';
			$query	.= '   WHERE m.`type` = ' . $db->Quote('blogger') . ')';
		}

		if (!empty($query) && $type == '') {
			$query	.= ' UNION ';
		}

		if ($type == 'view' || $type == '') {
			$query	.= '  (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, m.title';
			$query	.= '   FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query	.= '   WHERE m.`type` = ' . $db->Quote('view') . ')';
		}

		if (!empty($query) && $type == '') {
			$query	.= ' UNION ';
		}

		if ($type == 'category' || $type == '') {
			$query	.= '  (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, if(m.title != \'\', ifnull(m.title, p.title), p.title) AS title';
			$query	.= '   FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query	.= '   LEFT JOIN ' . $db->nameQuote('#__easyblog_category') . ' AS p';
			$query	.= '   ON m.content_id = p.id';
			$query	.= '   WHERE m.`type` = ' . $db->Quote('category') . ')';
		}

		if (!empty($query) && $type == '') {
			$query	.= ' UNION ';
		}

		if ($type == 'tag' || $type == '') {
			$query	.= '  (SELECT m.id, m.type, m.content_id, m.keywords, m.description, m.indexing, if(m.title != \'\', ifnull(m.title, p.title), p.title) AS title';
			$query	.= '   FROM ' . $db->nameQuote('#__easyblog_meta') . ' AS m';
			$query	.= '   LEFT JOIN ' . $db->nameQuote('#__easyblog_tag') . ' AS p';
			$query	.= '   ON m.content_id = p.id';
			$query	.= '   WHERE m.`type` = ' . $db->Quote('tag') . ')';
		}

		$query	.= ') AS x ';

		$query	.= $where;

		// echo $query;exit;

		return $query;
	}

	/**
	 * Builds the WHERE clause
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function buildItemsQueryWhere( $type = '' )
	{
		$db = EB::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.meta.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest( 'com_easyblog.meta.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($search) {
			$where[] = ' LOWER(x.' . $db->quoteName('title') . ') LIKE ' . $db->Quote('%' . $search . '%');
		}

		$where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getItems($type = '', $usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (!$this->data) {

			$query = $this->buildItemsQuery($type);

			if ($usePagination) {
				$this->data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->data = $this->_getList($query);
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal( $type = '' )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($type);
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination( $type = '' )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = EB::Pagination( $this->getTotal( $type ), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery( $type = '' )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $type );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EB::db();

		$query	= 'SELECT m.*, p.title AS title FROM ' . $db->nameQuote( '#__easyblog_meta' ) . ' AS m ' .
				  'LEFT JOIN ' . $db->nameQuote( '#__easyblog_post' ) . ' AS p ' .
				  'ON m.content_id = p.id ' .
				  $where . ' ' .
				  $orderby;

		return $query;
	}

	public function _buildQueryWhere( $type = '' )
	{
		$mainframe			= JFactory::getApplication();
		$db					= EB::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.meta.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.meta.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(EBString::strtolower( $search ) ) );

		$where = array();

		if ($search)
		{
			$where[] = ' LOWER( p.title ) LIKE \'%' . $search . '%\' ';
		}

		if ( !empty( $type ) )
		{
			$where[] = 'm.`type` = '.$db->quote($type);
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.meta.filter_order', 		'filter_order', 	'm.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.meta.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.'';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $type = '', $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( $type );
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
			    $this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	function getPostMeta( $id )
	{
		return $this->getMetaInfo(META_TYPE_POST, $id);
	}

	function getMetaInfo( $type, $id )
	{
		$db	= EB::db();
		$query 	= 'SELECT id, keywords, description FROM #__easyblog_meta';
		$query	.= ' WHERE `content_id` = ' . $db->Quote($id);
		$query	.= ' AND `type` = ' . $db->Quote($type);
		$query	.= ' ORDER BY `id` DESC';
		$query	.= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadObject();

		if ( ! isset($result->id) )
		{
			$obj	= new stdClass();
			$obj->id			= '';
			$obj->keywords		= '';
			$obj->description 	= '';

			return $obj;
		}
		return $result;
	}

	/**
	 * Delete metas for particular post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteMetas($id, $type, $exclude = array())
	{
		$db = EB::db();

		if (!is_array($exclude)) {
			$exclude = EB::makeArray($exclude);
		}

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__easyblog_meta');
		$query[] = 'WHERE ' . $db->quoteName('content_id') . '=' . $db->Quote($id);
		$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote($type);

		if (!empty($exclude)) {
			$query[] = 'AND ' . $db->quoteName('id') . ' NOT IN (' . implode(',', $exclude) . ')';
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		return $db->Query();
	}


	/**
	 * Preload metas for blog posts.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preloadPostMetas($postIds)
	{
		$db = EB::db();

		$query = 'select * from ' . $db->qn('#__easyblog_meta');
		$query .= ' where `content_id` in (' . implode(',', $postIds) . ')';
		$query .= ' and `type` = ' . $db->Quote(META_TYPE_POST);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$metas = array();

		if ($results) {
			foreach($results as $item) {
				$metas[$item->content_id] = $item;
			}
		}

		return $metas;
	}

	/**
	 * Create meta record
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public function createMeta($uid, $type, $title = '', $keywords = '', $description = '')
	{
		// Check if the meta already exists
		$options = array('content_id' => $uid, 'type' => $type);

		// Store the meta data for this post now
		$meta = EB::table('Meta');
		$exists = $meta->load($options);

		if ($exists) {
			return false;
		}

		$meta->content_id = $uid;
		$meta->type = $type;
		$meta->title = $title;
		$meta->keywords = $keywords;
		$meta->description = $description;

		return $meta->store();
	}
}
