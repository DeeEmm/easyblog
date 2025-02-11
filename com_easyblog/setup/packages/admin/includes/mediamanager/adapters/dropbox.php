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

class EasyBlogMediaManagerDropboxSource
{
	private $token = null;
	private $access = null;

	/**
	 * Sets the oauth access
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setAccess(EasyBlogTableOAuth $access)
	{
		$this->client = EB::oauth()->getClient('dropbox');
		$this->client->setAccess($access->access_token);
	}

	/**
	 * Returns an array of folders / albums in a given folder since jomsocial only stores user images here.
	 *
	 * @since 	4.0
	 * @access	public
	 */
	public function getItems($path)
	{
		// If path is empty, we will presume that the user is trying to list the root contents
		$path = !$path ? '/' : '';

		// Get all files and fodlers from the initial path
		$result = $this->client->getFiles();

		// Build the current folder
		$currentFolder = $this->buildFolderItem($path, JText::_('Dropbox'));

		// Get the folders
		$folders = $this->folders($result->folders);

		// Get the files
		$files = $this->files($result->files);

		$currentFolder->contents = array_merge($folders, $files);
		
		return $currentFolder;
	}

	/**
	 * 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function buildFolderItem($path, $title)
	{
		$item = new stdClass();

		$item->title = $title;
		$item->type = 'folder';
		$item->path = $path;
		$item->place = 'dropbox';
		$item->contents	= array();
		$item->width = '';
		$item->height = '';

		return $item;
	}

	/**
	 * Format files
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function files($files)
	{
		$result = array();

		foreach ($files as $file) {

			$item = new stdClass();

			$item->title 	= $file->title;
			$item->url 		= rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=dropbox&layout=preview&path=' . urlencode($file->path) . '&tmpl=component&no_html=1';
			$item->width 	= 200;
			$item->height 	= 200;
			$item->place 	= 'dropbox';

			// Thumbnail
			$thumbnail 		= new stdClass();
			$thumbnail->url = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=dropbox&layout=preview&path=' . urlencode($file->path) . '&tmpl=component&no_html=1&size=m';
			$item->thumbnail = $thumbnail;


			// Icon
			$icon = new stdClass();
			$icon->url = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=dropbox&layout=preview&path=' . urlencode($file->path) . '&tmpl=component&no_html=1&size=s';
			$item->icon = $icon;

			$item->relativePath = '/';
			$item->dateModified	= $file->modified;
			$item->creationDate	= $file->modified;
			$item->mime = $file->mime_type;
			$item->path = '/' . $file->title;

			$result[] = $item;
		}

		return $result;
	}

	/**
	 * Format folders
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function folders($folders)
	{
		$result = array();

		foreach($folders as &$folder) {

			$item	= $this->buildFolderItem($folder->path, $folder->title);


			$result[]	= $item;
		}

		return $result;
	}
}