<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableAutoArticleMap extends EasyBlogTable
{
	/*
	 * The id of the map
	 * @var int
	 */
	var $id 						= null;

	/*
	 * EasyBlog post id
	 * @var int
	 */
	var $post_id					= null;

	/*
	 * Content ID
	 * @var string
	 */
	var $content_id					= null;

	/*
	 * Created datetime of the article
	 * @var datetime
	 */
	var $created					= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_autoarticle_map' , 'id' , $db );
	}

	function load( $id = null, $loadPostId	= false)
	{
	    if( !$loadPostId )
	        return parent::load($id);

		$db = EB::db();

		$query  = 'select `id` from `#__easyblog_autoarticle_map` where `post_id` = ' . $db->Quote($id);
		$db->setQuery( $query );
		$result = $db->loadResult();

		return parent::load( $result );
	}
}
