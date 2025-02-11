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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class EasyBlogModelThemes extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieve the current front end's default template
	 *
	 * @since 5.0.37
	 * @access public
	 */
	public function getDefaultJoomlaTemplate()
	{
		$db = EB::db();
		$app = JFactory::getApplication();

		if (!EB::isFromAdmin() && !EBFactory::isCli()) {

			// Try to load the template from joomla cache since some 3rd party plugins can change the templates on the fly. #907
			$template = $app->getTemplate();

			if ($template) {
				return $template;
			}
		}

		$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		$query .= ' WHERE ' . $db->nameQuote('home') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();


		return $template;
	}

	/**
	 * Generates the path to the theme
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getThemePath($element)
	{
		// to accomodate the directory separator of Windows platform
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

		$path = JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'themes' . DS . $element;

		return $path;
	}

	/**
	 * Retrieves a list of files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFiles($element)
	{
		$path = $this->getThemePath($element);

		// We should exclude emails since we already have a email template editor
		$exclude = array('.svn', 'CVS', 'styles', '.DS_Store', '__MACOSX', 'emails', 'images', 'styleguide', 'config', '.less');

		// Get a list of folers first
		$folders = JFolder::folders($path, '.', false, true, $exclude);
		$files = array();

		foreach ($folders as $folder) {

			$group = basename($folder);

			if (!isset($files[$group])) {
				$files[$group] = array();
			}

			$items = JFolder::files($folder, '.', true, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', '.less', '.json', '_cache', '_log', 'index.html'), array('^\..*', '.*~', '\.less', '\.json'));

			foreach ($items as $item) {


				$item = str_ireplace($path, '', $item);
				$item = base64_encode($item);

				$file = $this->getFile($item, $element);

				$files[$group][] = $file;
			}
		}

		return $files;
	}




	/**
	 * Allows caller to revert an overriden theme file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function revert($file)
	{
		$exists = JFile::exists($file->override);
		if (!$exists) {
			return false;
		}

		$state = JFile::delete($file->override);

		return $state;
	}


	/**
	 * Retrieves information about a single file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFile($filePath, $element, $contents = false)
	{
		$path = $this->getThemePath($element);

		$filePath = base64_decode($filePath);

		$filePath = $path . $filePath;

		$file = new stdClass();
		$file->element = $element;
		$file->title = str_ireplace($path, '', $filePath);
		$file->absolute = $filePath;
		$file->relative = str_ireplace($path, '', $filePath);
		$file->id = base64_encode($file->relative);

		$file->override = $this->getOverridePath($file->relative);
		$file->modified = JFile::exists($file->override);
		$file->contents = '';

		if ($contents) {
			$location = $file->modified ? $file->override : $file->absolute;
			$file->contents = file_get_contents($location);
		}

		return $file;
	}

	/**
	 * Generates the override path for a theme file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getOverridePath($relativePath)
	{
		$template = $this->getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/' . ltrim($relativePath, '/');

		return $path;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCurrentTemplate()
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->qn('template') . ' FROM ' . $db->qn('#__template_styles');
		$query .= ' WHERE ' . $db->qn('home') . '!=' . $db->Quote(0);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();


		return $template;
	}

	/**
	 * Retrieves the current site template's path
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCurrentTemplatePath()
	{
		// Get the custom.css override path for the current Joomla template
		$template = $this->getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template;

		return $path;
	}

	/**
	 * Retrieves the current site template's path
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCustomCssTemplatePath()
	{
		// Get the custom.css override path for the current Joomla template
		$template = $this->getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/styles/custom.css';

		return $path;
	}

	/**
	 * Allows caller to write contents
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function write($file, $contents)
	{
		$state = JFile::write($file->override, $contents);

		return $state;
	}

	/**
	 * Retrieves a list of installed themes on the site
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function getThemes()
	{
		$path = EBLOG_THEMES;

		$result	= JFolder::folders($path , '.', false, true, array('.svn', 'CVS', '.', '.DS_Store'));
		$themes	= array();

		// Cleanup output
		foreach ($result as $item) {
			$name = basename($item);

			if ($name != 'dashboard') {
				$obj = $this->getThemeObject($name);

				if ($obj) {
					$obj->default = false;

					if ($this->config->get('layout_theme') == $obj->element) {
						$obj->default = true;
					}

					$themes[] = $obj;
				}
			}
		}

		return $themes;
	}

	/**
	 * Retrieves the theme params
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getThemeParams($theme)
	{
		$table = EB::table('Configs');
		$table->load(array('name' => $theme));

		$registry = new JRegistry($table->params);

		return $registry;
	}

	/**
	 * Generates a standard theme object
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getThemeObject($name)
	{
		$file = EBLOG_THEMES . '/' . $name . '/config.xml';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		$parser = EB::getXml($file);

		$obj = new stdClass();
		$obj->element = $name;
		$obj->name = $name;
		$obj->path = $file;
		$obj->writable = is_writable($file);
		$obj->created = JText::_('Unknown');
		$obj->updated = JText::_('Unknown');
		$obj->author = JText::_('Unknown');
		$obj->version = JText::_('Unknown');
		$obj->desc = JText::_('Unknown');

		$childrens = $parser->children();

		foreach ($childrens as $key => $value) {
			if ($key == 'description') {
				$key = 'desc';
			}

			$obj->$key 	= (string) $value;
		}

		$obj->path = $file;

		// Since EasyBlog 5.2, we read from a config.json file for configuration
		$obj->config = null;

		$configFile = EBLOG_THEMES . '/' . $name . '/config.json';

		if (JFile::exists($configFile)) {
			$contents = file_get_contents($configFile);
			$obj->config = json_decode(file_get_contents($configFile));
		}

		return $obj;
	}

	/**
	 * Installs a new theme
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function install($file)
	{
		$source = $file['tmp_name'];
		$fileName = md5( $file[ 'name' ] . EB::date()->toSql());
		$fileExtension = '_themes_install.zip';
		$destination = JPATH_ROOT . '/tmp/' . $fileName . $fileExtension;

		// Upload the zip archive
		// argument in JFile::upload($src, $dest, $use_streams, $allow_unsafe, $safeFileOptions)
		// allow_unsafe = true. We need this to let files containing PHP code to upload. See JInputFiles::get.
		$state = JFile::upload($source, $destination, false, true);

		if (!$state) {
			$this->setError( JText::_( 'COM_EASYBLOG_THEMES_INSTALLER_ERROR_COPY_FROM_PHP' ) );

			return false;
		}

		// Extract the zip
		$extracted = dirname($destination) . '/' . $fileName . '_themes_install';
		$state = JArchive::extract($destination, $extracted);

		// Once it is extracted, delete the zip file
		JFile::delete($destination);

		// Get the configuration file.
		$manifest = $extracted . '/config/template.json';
		$manifest = file_get_contents($manifest);

		// Get the theme object
		$theme = json_decode($manifest);

		// Move it to the appropriate folder
		$themeDestination 	= EBLOG_THEMES . '/' . strtolower($theme->element);
		$exists	= JFolder::exists($themeDestination);

		// If folder exists, overwrite it. For now, just throw an error.
		if ($exists) {
			// Delete teh etracted folder
			JFolder::delete($extracted);

			$this->setError( JText::sprintf('COM_EASYBLOG_THEMES_INSTALLER_ERROR_SAME_THEME_FOLDER_EXISTS', $theme->element));
			return false;
		}

		// Move extracted folder
		$state	= JFolder::move($extracted, $themeDestination);

		if (!$state) {
			// Delete the etracted folder
			JFolder::delete($extracted);

			$this->setError(JText::_('COM_EASYBLOG_THEMES_INSTALLER_ERROR_MOVING_FOLDER_TO_THEMES_FOLDER'));
			return false;
		}

		return true;
	}
}
