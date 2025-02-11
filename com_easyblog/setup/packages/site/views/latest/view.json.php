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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * Default method to render listings in json format.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tmpl = null)
	{
		// Get the sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'));

		$model = EB::model('Blog');
		$posts = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, true);

		// Format the posts
		$posts = EB::formatter('list', $posts);
		
		$this->set('posts', $posts);

		return parent::display();
	}

}
