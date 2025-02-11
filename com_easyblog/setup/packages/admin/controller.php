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

class EasyBlogController extends JControllerLegacy
{
	protected $default_view = 'easyblog';

	public $doc = null;
	public $my = null;
	public $config = null;
	public $info = null;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->doc = JFactory::getDocument();
		$this->config = EB::config();
		$this->my = JFactory::getUser();
		$this->info = EB::info();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = EB::ajax();
		}
	}
	/**
	 * ACL checks for admin access
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function checkAccess($acl)
	{
		if (!$this->my->authorise('easyblog.manage.' . $acl , 'com_easyblog')) {
			$this->info->set('JERROR_ALERTNOAUTHOR', 'error');
			return $this->app->redirect('index.php?option=com_easyblog');
		}
	}

	/**
	 * Default display method which is invoked by Joomla
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$type = $this->doc->getType();
		$name = $this->input->get('view', $this->getName(), 'cmd');
		$layout = $this->input->get('layout', 'default', 'cmd');

		// Get the current view object
		$view = $this->getView($name, $type, '');
		$view->setLayout($layout);

		// For templates view, we would treat it as a composer view.
		if ($name == 'templates') {
			$name = 'composer';
		}

		if ($name != 'composer') {
			$stylesheet = EB::stylesheet('admin', 'default');

			// Load frontend's language file as we might need it
			EB::loadLanguages();

			// Attach fontawesome stylesheet
			$stylesheet->attachFontawesome();

			// Allow caller to invoke recompiling of the entire css
			if ($this->input->get('compileCss') && FH::isSiteAdmin()) {
				$result = $stylesheet->build('full');

				header('Content-type: text/x-json; UTF-8');
				echo json_encode($result);
				exit;
			}

			$stylesheet->attach();
		}

		parent::display();

		return $this;
	}

	/**
	 * Redirects a request
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function redirectToView($view, $layout = '')
	{
		$link = 'index.php?option=com_easyblog&view=' . $view;

		if ($layout) {
			$link .= '&layout=' . $layout;
		}

		return $this->app->redirect($link);
	}

	/**
	 * Proxy to set a messsage
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function setMessage($message, $type = 'success')
	{
		$this->info->set($message, $type);
	}
}
