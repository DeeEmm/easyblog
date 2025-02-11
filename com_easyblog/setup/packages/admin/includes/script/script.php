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

class EasyBlogScript extends EasyBlog
{
	public $extension = 'js';
	public $scriptTag = false;
	public $openingTag = '<script>';
	public $closingTag = '</script>';
	public $CDATA = false;
	public $safeExecution = false;

	public $header = '';
	public $footer = '';

	public $vars = [];

	/**
	 * Sets a variable
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Appends script files into the scripts section
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function attach($path = null)
	{
		// Import joomla's filesystem library.
		jimport('joomla.filesystem.file');

		$this->file = EB::themes()->resolve($path) . '.' . $this->extension;

		// Reset to false
		$this->scriptTag = false;
		$this->CDATA = false;

		$contents = $this->parse();

		ob_start();
		include(EBLOG_MEDIA . '/head.js');
		$scripts = ob_get_contents();
		ob_end_clean();

		// Inject the scripts on the page
		EB::scripts()->add($scripts);
	}

	/**
	 * Parses a script file.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function parse($vars = null)
	{
		ob_start();

		// If argument is passed in, we only want to load that into the scope.
		if (is_array($vars)) {
			extract($vars);
		} else {
			// Extract variables that are available in the namespace
			if(!empty($this->vars)) {
				extract($this->vars);
			}
		}

		// Magic happens here when we include the template file.
		include($this->file);

		$output = ob_get_contents();
		ob_end_clean();

		$script	= $this->header . $output . $this->footer;

		// Do not reveal root folder path.
		$file = str_ireplace(JPATH_ROOT, '', $this->file);

		// Replace \ with / to avoid javascript syntax errors.
		$file = str_ireplace( '\\' , '/' , $file );

		$cdata = $this->CDATA;
		$scriptTag = $this->scriptTag;
		$safeExecution = $this->safeExecution;

ob_start();
include(EBLOG_MEDIA . '/scripts/template.php');
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Allows inclusion of scripts within another script
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function output($file = null, $vars = null)
	{
		$template = $this->getTemplate($file);

		// Ensure that the script file exists
		if (!JFile::exists($template->script)) {
			return;
		}

		$this->file = $template->script;
		$output = $this->parse();

		return $output;
	}
}
