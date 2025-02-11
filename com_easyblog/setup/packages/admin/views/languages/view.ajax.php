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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewLanguages extends EasyBlogAdminView
{
	/**
	 * Retrieves a list of known languages
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLanguages()
	{
		$model = EB::model('Languages');
		$result = $model->discover();

		if ($result !== true) {

			$msg = isset($result->message) ? $result->message : $result;

			$return = base64_encode('index.php?option=com_easyblog&view=languages');

			$template = EB::themes();
			$template->set('return', $return);
			$output = $template->output('admin/languages/invalid');

			return $this->ajax->reject($output, $msg);
		}

		return $this->ajax->resolve();
	}
}
