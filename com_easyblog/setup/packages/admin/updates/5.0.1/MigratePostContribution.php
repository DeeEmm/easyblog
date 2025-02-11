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

class EasyBlogMaintenanceScriptMigratePostContribution extends EasyBlogMaintenanceScript
{
    public static $title = "Migrating post's contribution";
    public static $description = "Migrating post's contribution between teamblog, JomSocial groups and JomSocial events.";

    public function main()
    {

        $db = EB::db();

        // teamblog
        $query = "update " . $db->qn('#__easyblog_post') . " as a";
        $query .= "    inner join " . $db->qn("#__easyblog_team_post") . " as b on a." . $db->qn('id') . " = b." . $db->qn('post_id');
        $query .= " set a." . $db->qn("source_id") . " = b." . $db->qn('team_id') . ",";
        $query .= "     a." . $db->qn('source_type') . " = " . $db->Quote('easyblog.team');
        $query .= " where a." . $db->qn('source_type') . " = ''";

        $db->setQuery($query);
        $state = $db->query();

        // jomsocial group
        $query = "update " . $db->qn('#__easyblog_post') . " as a";
        $query .= "    inner join " . $db->qn("#__easyblog_external_groups") . " as b on a." . $db->qn('id') . " = b." . $db->qn('post_id');
        $query .= " set a." . $db->qn("source_id") . " = b." . $db->qn('group_id') . ",";
        $query .= "     a." . $db->qn('source_type') . " = " . $db->Quote('jomsocial.group');
        $query .= " where a." . $db->qn('source_type') . " = ''";

        $db->setQuery($query);
        $state = $db->query();

        // jomsocial event
        $query = "update " . $db->qn('#__easyblog_post') . " as a";
        $query .= "    inner join " . $db->qn("#__easyblog_external") . " as b on a." . $db->qn('id') . " = b." . $db->qn('post_id');
        $query .= " set a." . $db->qn("source_id") . " = b." . $db->qn('uid') . ",";
        $query .= "     a." . $db->qn('source_type') . " = " . $db->Quote('jomsocial.event');
        $query .= " where a." . $db->qn('source_type') . " = ''";

        $db->setQuery($query);
        $state = $db->query();

        // okay, now let update the others to sitewide post.
        $query = "update " . $db->qn('#__easyblog_post') . " as a";
        $query .= " SET a." . $db->qn('source_type') . " = " . $db->Quote('easyblog.sitewide') . ",";
        $query .= "     a." . $db->qn('source_id') . " = " . $db->Quote('0');
        $query .= " where a." . $db->qn('source_type') . " = ''";

        $db->setQuery($query);
        $state = $db->query();

        return $state;
    }

}
