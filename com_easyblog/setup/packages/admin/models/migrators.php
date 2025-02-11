<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelMigrators extends EasyBlogAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	function __construct()
	{
		parent::__construct();

		
		$mainframe	= JFactory::getApplication();

// 		$limit		= $mainframe->getUserStateFromRequest( 'com_easyblog.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
// 		$limitstart = $mainframe->getUserStateFromRequest( 'com_easyblog.limitstart', 'limitstart', 0, 'int' );
// 
// 		$this->setState('limit', $limit);
// 		$this->setState('limitstart', $limitstart);
	}
	
	
	/**
	 * function used to get article's categories drop down list.
	 */	 	
	function  getArticleCategories( $excludeSection = '')
	{
		$db		= EB::db();
		
		$excludeSQL = '';
		if($excludeSection != '')
		{
		    $excludeSection = ' WHERE s.id != ' . $db->Quote($excludeSection);
		}
		
		$query = 'SELECT cc.id AS value, cc.title AS text, section' .
				' FROM #__categories AS cc' .
				' INNER JOIN #__sections AS s ON s.id = cc.section ' .
				$excludeSection .
				' ORDER BY s.ordering, cc.ordering';
		
		$db->setQuery($query);		
		return $db->loadObjectList();
	}


	/**
	 * function used to get list of Authors for dropdown filter
	 */	 	
	function getArticleAuthors()
	{
		$db		= EB::db();		
		$query = 'SELECT c.created_by, u.name' .
				' FROM #__content AS c' .
				' INNER JOIN #__sections AS s ON s.id = c.sectionid' .
				' LEFT JOIN #__users AS u ON u.id = c.created_by' .
				' WHERE c.state <> -1' .
				' AND c.state <> -2' .
				' GROUP BY u.name' .
				' ORDER BY u.name';
						
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getArticleAuthors16()
	{
	    $db		= EB::db();
	    
	    $query  = 'SELECT u.id AS created_by, u.name';
	    $query  .= ' FROM `#__users` AS u';
	    $query  .= ' INNER JOIN `#__content` AS c ON c.created_by = u.id';
		$query  .= ' GROUP BY u.id, u.name';
		$query  .= ' ORDER BY u.name';
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	

}