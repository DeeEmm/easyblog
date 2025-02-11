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

class EasyBlogMaintenanceScriptMigrateSubscriptions extends EasyBlogMaintenanceScript
{
    public static $title = "Migrating subscription items into one central place.";
    public static $description = "Migrating subcriptions items from various tables into one central place.";

    public function main()
    {

        $db = EB::db();

        // if old tables not exists, then we shouldn't continue the migration.
        if (! $this->isTableDraftsExists()) {
            return true;
        }

        // site subscriptions
        $query = 'insert into ' . $db->qn('#__easyblog_subscriptions') . ' (' . $db->qn('uid') . ', ' . $db->qn('utype') . ', ';
        $query .= $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created') . ')';
        $query .= ' select ' . $db->Quote('0') . ', ' . $db->Quote('site') . ', ' . $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created');
        $query .= ' from ' . $db->qn('#__easyblog_site_subscription');

        $db->setQuery($query);
        $state = $db->query();

        if (!$state) {
            return false;
        }

        // entry subscriptions
        $query = 'insert into ' . $db->qn('#__easyblog_subscriptions') . ' (' . $db->qn('uid') . ', ' . $db->qn('utype') . ', ';
        $query .= $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created') . ')';
        $query .= ' select ' . $db->qn('post_id') . ', ' . $db->Quote('entry') . ', ' . $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created');
        $query .= ' from ' . $db->qn('#__easyblog_post_subscription');

        $db->setQuery($query);
        $state = $db->query();

        if (!$state) {
            return false;
        }

        // blogger subscriptions
        $query = 'insert into ' . $db->qn('#__easyblog_subscriptions') . ' (' . $db->qn('uid') . ', ' . $db->qn('utype') . ', ';
        $query .= $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created') . ')';
        $query .= ' select ' . $db->qn('blogger_id') . ', ' . $db->Quote('blogger') . ', ' . $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created');
        $query .= ' from ' . $db->qn('#__easyblog_blogger_subscription');

        $db->setQuery($query);
        $state = $db->query();

        if (!$state) {
            return false;
        }


        // category subscriptions
        $query = 'insert into ' . $db->qn('#__easyblog_subscriptions') . ' (' . $db->qn('uid') . ', ' . $db->qn('utype') . ', ';
        $query .= $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created') . ')';
        $query .= ' select ' . $db->qn('category_id') . ', ' . $db->Quote('category') . ', ' . $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created');
        $query .= ' from ' . $db->qn('#__easyblog_category_subscription');

        $db->setQuery($query);
        $state = $db->query();

        if (!$state) {
            return false;
        }

        // team subscriptions
        $query = 'insert into ' . $db->qn('#__easyblog_subscriptions') . ' (' . $db->qn('uid') . ', ' . $db->qn('utype') . ', ';
        $query .= $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created') . ')';
        $query .= ' select ' . $db->qn('team_id') . ', ' . $db->Quote('team') . ', ' . $db->qn('user_id') . ', ' . $db->qn('fullname') . ', ' . $db->qn('email') . ', ' . $db->qn('created');
        $query .= ' from ' . $db->qn('#__easyblog_team_subscription');

        $db->setQuery($query);
        $state = $db->query();

        if (!$state) {
            return false;
        }

        return $state;
    }

    public function isTableDraftsExists() {
        $db = EB::db();

        $query = "SHOW TABLES LIKE '%_easyblog_site_subscription'";
        $db->setQuery($query);

        $result = $db->loadResult();

        return ($result) ? true : false;
    }

}
