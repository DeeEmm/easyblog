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

require_once(__DIR__ . '/model.php');

class EasyBlogModelOptimizer extends EasyBlogAdminModel
{
	public $_data = null;
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves a list of image attachments that are not processed by the image optimizer yet
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getPhotosToOptimize($limit = 5)
	{
		$db = EB::db();
		$limit = (int) $limit;

		// for the photo limits, for now we set to 5.
		// 1 photo will have 7 variation, thats mean for 5 photos, system need to process 35 images.

		$q = [];
		$q[] = "SELECT a.* FROM `#__easyblog_media` AS a";
		$q[] = "WHERE a.`type` = " . $db->Quote('image');
		$q[] = "AND (";
		$q[] = "  a.`place` = " . $db->Quote('shared') . " OR a.`place` LIKE " . $db->Quote('post:%') . " OR a.`place` LIKE " . $db->Quote('user:%');
		$q[] = ")";
		$q[] = "AND NOT EXISTS(";
		$q[] = "select b.`id` from `#__easyblog_optimizer` as b where b.`url` = a.`url`";
		$q[] = ")";
		$q[] = "LIMIT $limit";

		$query = implode(' ', $q);
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (!$items) {
			return $items;
		}

		return $items;
	}
}
