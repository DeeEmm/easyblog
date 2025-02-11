<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptMigratePostCategory extends EasyBlogMaintenanceScript
{
    public static $title = "Migrating post category";
    public static $description = "Migrating post category to support multi categories.";

    public function main()
    {

        $db = EB::db();

        $query = 'insert into ' . $db->qn('#__easyblog_post_category') . '(';
        $query .= $db->qn('post_id') . ', ' . $db->qn('category_id') . ', ' . $db->qn('primary') . ')';
        $query .= ' select ' . $db->qn('a.id') . ', ' . $db->qn('a.category_id') . ', ' . $db->Quote('1') . ' from ' . $db->qn('#__easyblog_post') . ' as a';
        $query .= ' where ' . $db->qn('a.category_id') . ' > ' . $db->Quote('0');
        $query .= ' and not exists (select ' . $db->qn('pc.post_id') . ' from ' . $db->qn('#__easyblog_post_category') . ' as pc';
        $query .= '                     where ' . $db->qn('pc.post_id') . ' = ' . $db->qn('a.id') . ' and ' . $db->qn('pc.category_id') . ' = ' . $db->qn('a.category_id') . ')';

        $db->setQuery($query);
        $state = $db->query();

        return $state;
    }

}
