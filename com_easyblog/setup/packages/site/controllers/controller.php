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
	public $doc = null;
	public $acl = null;
	public $info = null;
	public $my = null;
	public $config = null;

	public function __construct($config = array())
	{
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->acl = EB::acl();
		$this->my = JFactory::getUser();
		$this->info = EB::info();
		$this->config = EB::config();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = EB::ajax();
		}

		parent::__construct($config);

		$this->input = EB::request();
	}

	/**
	 * Determines if we should be retrieving the return url
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getReturnURL()
	{
		// Redirect
		$return = $this->input->get('return', '', 'default');

		if (!$return) {
			return false;
		}

		$return = base64_decode($return);

		return $return;
	}

	/**
	 * Override parent's display method
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$viewType = $this->doc->getType();
		$viewName = $this->input->get('view', 'latest');
		$viewLayout = $this->input->get('layout', 'default', 'string');

		// Get the view
		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

		// Set the layout
		$view->setLayout($viewLayout);

		// Get Joomla's configuration
		$jconfig    = EB::jconfig();

		// Display the view
		if ($cachable && $viewType != 'feed' && $jconfig->get('caching') >= 1) {

			$option = $this->input->get('option');
			$cache  = JFactory::getCache($option, 'view');

			if (is_array($urlparams)) {

				if (!empty($this->app->registeredurlparams)) {
					$registeredurlparams = $app->registeredurlparams;
				} else {
					$registeredurlparams = new stdClass;
				}

				foreach ($urlparams as $key => $value) {
					// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}

				$app->registeredurlparams = $registeredurlparams;
			}

			$cache->get($view, 'display');
		} else {

			if (method_exists($view, $viewLayout)) {
				$view->$viewLayout();
			} else {
				$view->display();
			}
		}

		return $this;
	}
}
