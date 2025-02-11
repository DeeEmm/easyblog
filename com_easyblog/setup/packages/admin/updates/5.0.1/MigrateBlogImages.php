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

class EasyBlogMaintenanceScriptMigrateBlogImages extends EasyBlogMaintenanceScript
{
    public static $title = "Migrating blog images.";
    public static $description = "Migrating post's the blog image path property.";

    public function main()
    {

        $db = EB::db();

        // get posts that has blog image added.
        $query = "select id, image from `#__easyblog_post` where `image` != ''";
        $db->setQuery($query);

        $results = $db->loadObjectList();

        if ($results) {

            foreach($results as $item) {

                $imageObj = json_decode($item->image);

                // sometime it will return null is because that JSON string contain linefeed caused invalid JSON string
                if (is_null($imageObj)) {
                    $item->image = preg_replace( "/[\n\r]/", "", $item->image);
                    $imageObj = json_decode($item->image);
                }

                // if this is an object, this mean its from legacy post. lets update the path.
                if (is_object($imageObj)) {
                    $newpath = $imageObj->place . $imageObj->path;

                    $update = "update `#__easyblog_post` set `image` = " . $db->Quote($newpath);
                    $update .= " where `id` = " . $db->Quote($item->id);

                    $db->setQuery($update);
                    $db->query();

                }

            } //end foreach

        } //end if

        return true;
    }

}
