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

class EasyBlogMaintenanceScriptDropEasyBlogStream extends EasyBlogMaintenanceScript
{
    public static $title = "Remove EasyBlog stream";
    public static $description = "Remove EasyBlog stream table from the system.";

    public function main()
    {
        $db = EB::db();

        $query = "DROP TABLE IF EXISTS `#__easyblog_stream`";

        $db->setQuery($query);
        $state = $db->query();

        return $state;
    }

}
