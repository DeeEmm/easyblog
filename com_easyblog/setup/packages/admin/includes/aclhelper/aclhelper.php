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

class EasyBlogAclHelper
{
	/**
	 * generate sql used for blogger retrieval
	 *
	 * @since	5.0
	 * @access	public
	 * @param
	 * @return string
	 */

	public static function genIsbloggerSQL($column = 'a.id')
	{
		$db = EB::db();

		$aclQuery = '1 <= (select count(1) from `#__easyblog_acl_group` as ag';
		$aclQuery .= '			inner join `#__easyblog_acl` as acl on ag.`acl_id` = acl.`id`';
		$aclQuery .= '			inner join `#__user_usergroup_map` as up on ag.`content_id` = up.`group_id`';
		$aclQuery .= '		 	where up.`user_id` = ' . $db->qn($column);
		$aclQuery .= '			and acl.`action` = ' . $db->Quote('add_entry');
		$aclQuery .= '			and ag.`type` = ' . $db->Quote('group');
		$aclQuery .= '			and ag.`status` = 1)';

		return $aclQuery;
	}
}
