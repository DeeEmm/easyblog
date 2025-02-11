<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class JFormFieldThemeFiles extends EasyBlogFormField
{
	protected $type = 'ThemeFiles';

	/**
	 * Cleanup file names
	 *
	 * @since	5.3.0
	 * @access	private
	 */
	private function cleanupFileNames($files)
	{
		$items = array();
		
		foreach ($files as $file) {

			// Remove the .php from the extension
			$file = str_ireplace('.php', '', $file);


			$obj = new stdClass();
			$obj->file = $file;
			
			$file = str_ireplace(array('_', '-'), ' ', $file);
			$file = ucfirst($file);

			$obj->title = $file;

			$items[] = $obj;
		}

		return $items;
	}

	/**
	 * Renders a dropdown to list files from a specific folder from a theme
	 *
	 * @since	5.3.0
	 * @access	public
	 */	
	protected function getInput()
	{
		if (!EB::isFoundryEnabled()) {
			return;
		}
		
		$folder = (string) $this->element['folder'];

		// We know for sure that wireframe must have these files as wireframe is the base theme
		$files = $this->getWireframeFiles($folder);
		$files = array_merge($files, $this->getThemeFiles($folder));
		$files = array_merge($files, $this->getOverrideFiles($folder));

		$files = array_unique($files);

		// Cleanup the names
		$files = $this->cleanupFileNames($files);

		if (!$this->value) {
			$this->value = $this->default;
		}

		$this->set('files', $files);
		$this->set('id', $this->id);
		$this->set('name', $this->name);
		$this->set('value', $this->value);

		return $this->output('admin/elements/themefiles');
	}

	/**
	 * Retrieve a list of files in the wireframe theme
	 *
	 * @since	5.3.0
	 * @access	private
	 */
	private function getWireframeFiles($folder)
	{
		$parent = JPATH_ROOT . '/components/com_easyblog/themes/wireframe/' . $folder;
		$files = JFolder::files($parent);

		return $files;
	}

	/**
	 * Retrieve a list of files in the currently selected theme
	 *
	 * @since	5.3.0
	 * @access	private
	 */
	private function getThemeFiles($folder)
	{
		$theme =  $this->config->get('layout_theme');

		$parent = JPATH_ROOT . '/components/com_easyblog/themes/' . $theme . '/' . $folder;
		$exists = JFolder::exists($parent);

		if (!$exists) {
			return array();
		}

		$files = JFolder::files($parent);

		return $files;
	}

	/**
	 * Retrieve a list of files in the Joomla template override section
	 *
	 * @since	5.3.0
	 * @access	private
	 */
	private function getOverrideFiles($folder)
	{
		$template = EB::getCurrentTemplate();

		$parent = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/' . $folder;
		$exists = JFolder::exists($parent);

		if (!$exists) {
			return array();
		}

		$files = JFolder::files($parent);

		return $files;
	}
}
