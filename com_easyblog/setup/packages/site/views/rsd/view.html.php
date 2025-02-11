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

class EasyBlogViewRSD extends EasyBlogView
{
	/**
	 * This view is to let the world know this site supports xmlrpc publishing
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		if (!$this->config->get('main_remotepublishing_xmlrpc')) {
			return;
		}

		$title = $this->jconfig->get('sitename');

		$theme = EB::themes();
		$link = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&amp;tmpl=component';
		$xmlrpc = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&amp;view=xmlrpc&amp;tmpl=component';

		$theme->set('title', $title);
		$theme->set('link', $link);
		$theme->set('xmlrpc', $xmlrpc);

		$output = $theme->output('site/rsd/template');
		
		header('Content-Type: application/xml; charset=utf-8', true);
		echo $output;
		exit;
	}
}
