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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

if (!EB::isFoundryEnabled()) {
	EB::raiseWarning(500, 'Please ensure that the plugin <b>Foundry by Stackideas</b> is enabled on the site. Go to <a href="index.php?option=com_plugins&view=plugins&filter[search]=Foundry">Joomla! plugin manager</a>');

	class EasyBlogFormField extends JFormField
	{
		public function __construct()
		{
			// use joomla way of loading component lang file
			JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT . '/administrator');
		}

		public function set($key, $value) {}
		public function output($namespace) {}
		protected function getInput() {}
	}

} else {

	class EasyBlogFormField extends JFormField
	{
		public function __construct()
		{
			EB::loadLanguages(JPATH_ADMINISTRATOR);

			// Load our own js library
			EB::init('admin');

			// Attach the admin's css
			$stylesheet = EB::stylesheet('admin', 'default');
			$stylesheet->attach();

			$stylesheet->attachFontawesome();

			// Render modal from Joomla
			FH::renderModalLibrary();

			// Ensure that jQuery is loaded
			FH::renderjQueryFramework();

			$this->app = JFactory::getApplication();
			$this->input = $this->app->input;
			$this->config = EB::config();
			$this->theme = EB::themes();
		}

		/**
		 * Proxy method to assist child elements to set variables to the theme
		 *
		 * @since	5.1
		 * @access	public
		 */
		public function set($key, $value)
		{
			$this->theme->set($key, $value);
		}

		/**
		 * Proxy method to assist child elements to retrieve contents of a theme file
		 *
		 * @since	5.1
		 * @access	public
		 */
		public function output($namespace)
		{
			$contents = $this->theme->output($namespace);

			// check if this field element is called from outside of EB or not.
			// if yes, we need to attach the script tag from the theme files.
			$option = $this->app->input->get('option', '', 'default');

			if ($option !== 'com_easyblog') {
				// Collect all javascripts attached so that we can output them at the bottom of the page
				$doc = JFactory::getDocument();
				$scripts = EB::scripts()->getScripts();

				if ($doc->getType() == 'html' && $scripts) {
					$doc->addCustomTag($scripts);
				}
			}

			return $contents;
		}

		/**
		 * Abstract method that should be implemented on child classes
		 *
		 * @since   5.1
		 * @access  public
		 */
		protected function getInput()
		{
		}
	}
}
