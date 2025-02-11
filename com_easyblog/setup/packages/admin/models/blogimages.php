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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelBlogImages extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function exists($title, $theme)
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_config_images');
		$query .= 'WHERE ' . $db->quoteName('title') . '=' . $db->Quote($title);
		$query .= 'AND ' . $db->quoteName('theme') . '=' . $db->Quote($theme);

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Purges the blog image cache
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function purge()
	{
		$folders = array();
		$folders[] = JPATH_ROOT . '/' . trim($this->config->get('main_image_path'));
		$folders[] = JPATH_ROOT . '/' . trim($this->config->get('main_shared_path'));

		$total = 0;

		foreach ($folders as $folder) {

			$pattern = EBLOG_BLOG_IMAGE_PREFIX . '*';

			// Find a list of images within the folder
			$images = JFolder::files($folder, $pattern, true, true);

			foreach ($images as $image) {
				JFile::delete($image);

				$total++;
			}
		}

		return $total;
	}
}
