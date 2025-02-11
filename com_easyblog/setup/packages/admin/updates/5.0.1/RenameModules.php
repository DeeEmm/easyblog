<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptRenameModules extends EasyBlogMaintenanceScript
{
    public static $title = "Renaming Modules name";
    public static $description = "Renaming modules name to follow the new name standard.";

    public function main()
    {
        $db = EB::db();

        // mod_searchblogs -> mod_easyblogsearch
        // mod_latestblogs -> mod_easybloglatestblogs
        // mod_topblogs -> mod_easyblogtopblogs
        // mod_imagewall -> mod_easyblogimagewall
        // mod_showcase -> mod_easyblogshowcase
        // mod_teamblogs -> mod_easyblogteamblogs
        // mod_subscribers -> mod_easyblogsubscribers

        $oldNames = array('mod_latestblogs', 'mod_topblogs', 'mod_searchblogs', 'mod_imagewall', 'mod_showcase', 'mod_teamblogs', 'mod_subscribers');
        $newNames = array('mod_easybloglatestblogs', 'mod_easyblogtopblogs', 'mod_easyblogsearch', 'mod_easyblogimagewall', 'mod_easyblogshowcase', 'mod_easyblogteamblogs', 'mod_easyblogsubscribers');

        for ($i=0; $i<count($oldNames); $i++) {

            // jos_modules
            $query = array();
            $query[] = 'UPDATE ' . $db->qn('#__modules') . ' SET ' . $db->qn('module') . '=' . $db->Quote($newNames[$i]);
            $query[] = 'WHERE ' . $db->qn('module') . '=' . $db->Quote($oldNames[$i]);

            $query = implode(' ', $query);

            $db->setQuery($query);
            $state = $db->Query();
        }
       
        return $state;
    }

}
