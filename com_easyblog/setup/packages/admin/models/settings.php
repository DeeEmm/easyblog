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

require_once(__DIR__ . '/model.php');

class EasyBlogModelSettings extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getThemes( $type = 'client' )
	{
		static $themes	= array();

		if( !isset( $themes[ $type ] ) )
		{
			if( $type == 'dashboard' )
			{
				$themes[ $type ]	= JFolder::folders( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' );
			}
			else
			{
				$themes[ $type ]	= JFolder::folders( EBLOG_THEMES , '.' , false , false , array( 'dashboard' ) );
			}
		}

		return $themes[ $type ];
	}

	public function updateBlogPrivacy($value)
	{
		$db 	= EB::db();

		$query	= 'UPDATE ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'SET ' . $db->nameQuote( 'access' ) . ' = ' . $db->Quote($value) . ' '
				. 'WHERE ' . $db->nameQuote( 'access' ) . ' = ' . $db->Quote( 1 );
		$db->setQuery( $query );
		return $db->query();
	}

	public function save($data)
	{
		$config	= EB::table('Configs');
		$config->load('config');

		$registry = EB::registry($this->_getParams());

		foreach ($data as $index => $value) {

			// If the value is an array, we would assume that it should be comma separated
			if (is_array($value)) {
				$value = implode(',', $value);
			}

			$registry->set($index, $value);
		}

		// Get the complete INI string
		$config->params	= $registry->toString('INI');

		// Save it
		if (!$config->store()) {
			return false;
		}

		return true;
	}

	/**
	 * Get any available editor
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvailableEditor()
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->quoteName('element') . ' FROM ' . $db->quoteName('#__extensions');
		$query .= ' WHERE ' . $db->quoteName('type') . '=' . $db->Quote('plugin');
		$query .= ' AND ' . $db->quoteName('folder') . '=' . $db->Quote('editors');
		$query .= ' AND ' . $db->quoteName('enabled') . '=' . $db->Quote(true);
		$query .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Get fieldsets from a particular view
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getViewFieldsets($view, $layout = 'default')
	{
		jimport('joomla.filesystem.file');

		$file = EBLOG_ROOT . '/views/' . $view . '/tmpl/' . $layout . '.xml';

		$manifest = EB::form()->getManifest($file);

		return $manifest;
	}

	/**
	 * Retrieves the raw data from the database for the config
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRawData()
	{
		$db = EB::db();

		$query	= 'SELECT ' . $db->quoteName('params') . ' '
				. 'FROM ' . $db->quoteName('#__easyblog_configs') . ' '
				. 'WHERE ' . $db->nameQuote('name') . '=' . $db->Quote('config');

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public function &_getParams( $key = 'config' )
	{
		static $params	= null;

		if( is_null( $params ) )
		{
			$db		= EB::db();

			$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__easyblog_configs' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( $key );

			$db->setQuery( $query );

			$params	= $db->loadResult();
		}

		return $params;
	}

	public function getConfig()
	{
		static $config	= null;

		if (is_null($config)) {
			$params	= $this->_getParams( 'config' );
			$config	= EB::registry($params);
		}

		return $config;
	}

	/**
	 * Update Email Logo
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateLogo($file, $type = 'email')
	{
		// Do not proceed if image doesn't exist.
		if (empty($file) || !isset($file['tmp_name'])) {
			return false;
		}

		$source = $file['tmp_name'];

		$path = JPATH_ROOT . EB::getLogoOverridePath($type);

		// Try to upload the image
		$state = JFile::upload($source, $path);

		if (!$state) {
			$this->setError(JText::_('COM_EASYBLOG_EMAIL_LOGO_UPLOAD_ERROR'));
			return false;
		}

		return true;
	}

	 /**
	  * Restore Email Logo
	  *
	  * @since	5.1
	  * @access	public
	  */
	 public function restoreLogo($type = 'email')
	 {
		if (!EB::hasOverrideLogo($type)) {
			return false;
		}

		// Get override path
		$path = JPATH_ROOT . EB::getLogoOverridePath($type);

		// Let's delete it
		JFile::delete($path);

		return true;
	 }
}
