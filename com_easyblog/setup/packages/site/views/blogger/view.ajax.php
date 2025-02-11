<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogViewBlogger extends EasyBlogView
{
	/**
	 * Allows user tagging suggestion which is used by the helper.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function suggest()
	{
		// Only logged in users are allowed here
		EB::requireLogin();
		
		$keyword = $this->input->get('search', '', 'default');
		$limit = $this->config->get('composer_max_tag_suggest');

		$model = EB::model('Blogger');
		$suggestions = $model->suggest($keyword, $limit);

		return $this->ajax->resolve($suggestions);
	}

}
