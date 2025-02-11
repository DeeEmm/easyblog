<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptFixedsystemBlockState extends EasyBlogMaintenanceScript
{
	public static $title = "Fixed the incorrect state of system block";
	public static $description = "Fixed the incorrect state of system block";

	public function main()
	{
		// Construct to the place where we store all the blocks
		$path = EBLOG_ADMIN_ROOT . '/defaults/blocks';

		// Retrieve the list of files of each blocks
		$files = JFolder::files($path, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

		foreach ($files as $file) {
			$block = json_decode(file_get_contents($file));

			// If for whatever reason the contents cannot be decoded, we should not allow it to continue.
			if (!$block) {
				continue;
			}

			// Only check for system block
			if ($block->published != 2) {
				continue;
			}

			$table = EB::table('Block');
			$table->load(array('element' => $block->element));

			// Check for previous publishing state for existing block.
			if ($table->id) {
				$table->published = $block->published;

				// Save the block
				$table->store();
			}
		}

		return true;
	}
}
