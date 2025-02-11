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

class EasyBlogMaintenanceScriptMakePrimaryCategory extends EasyBlogMaintenanceScript
{
    public static $title = "Assigning default category";
    public static $description = "Assigning default category is there is no one.";

    public function main()
    {

        $state = true;
        $db = EB::db();

        // lets check if there is any default category assigned or not.
        $query = "select a.`id` from `#__easyblog_category` as a where a.`published` = 1 and a.`default` = 1";
        $db->setQuery($query);

        $result = $db->loadResult();

        if (! $result) {
            $query = "select a.`id`, count(b.`id`) as `cnt` from `#__easyblog_category` as a";
            $query .= " left join `#__easyblog_post_category` as b on a.`id` = b.`category_id`";
            $query .= " where a.`published` = 1";
            $query .= " group by a.`id`";
            $query .= " order by cnt desc";
            $query .= " limit 1";

            $db->setQuery($query);
            $id = $db->loadResult();

            // now we make sure no other categories which previously marked as default but its unpublished.
            $update = "update `#__easyblog_category` set `default` = 0";
            $db->setQuery($update);

            // now let update this category as default category
            $update = "update `#__easyblog_category` set `default` = 1 where `id` = " . $db->Quote($id);
            $db->setQuery($update);
            $state = $db->query();
        }

        return $state;
    }

}
