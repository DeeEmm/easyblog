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

class EasyBlogBlockHandlerModule extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-cube';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();
		$meta->preview = '';

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;
		$meta->properties['textpanel'] = false;

		$themes = EB::themes();
		$themes->set('installedModules', $this->getInstalledModules());
		$meta->html = $themes->output('site/composer/blocks/handlers/' . $this->type . '/html');

		return $meta;
	}

	public function data()
	{
		$data = (object) [];

		// For fieldset
		$data->installedModules = $this->getInstalledModules();

		return $data;
	}

	/**
	 * Retrieving available modules on the site.
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getInstalledModules()
	{
		static $_cache = null;

		if (is_null($_cache)) {
			$db = EB::db();

			$query = [];
			$query[] = 'SELECT `id`, `title`, `module`, `position` FROM ' . $db->quoteName('#__modules');
			$query[] = ' WHERE ' . $db->nameQuote('published') . '=' . $db->Quote(1);
			$query[] = ' AND ' . $db->nameQuote('client_id') . '=' . $db->Quote(0);
			$query[] = ' ORDER BY `title`';

			$db->setQuery($query);
			$_cache = $db->loadObjectList('id');
		}

		return $_cache;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		if (!isset($block->data->id) || !$block->data->id) {
			return false;
		}

		if (!isset($block->data->module) || !$block->data->module) {
			return false;
		}

		return true;
	}

	/**
	 * Normalize the data
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function normalizeData($data)
	{
		if (isset($data->id) && $data->id) {
			$installedModules = $this->getInstalledModules();

			$data->name = $installedModules[$data->id]->title;

			$themes = EB::themes();
			$themes->set('installedModules', $installedModules);
			$data->html = $themes->output('site/composer/blocks/handlers/' . $this->type . '/html');

			return $data;
		}
	}

	/**
	 * Displays the html output for a module preview block
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// For RSS documents, we just want to display nothing
		$doc = JFactory::getDocument();

		if ($doc->getType() === 'feed') {
			return;
		}

		// If configured to display text only, nothing should appear at all for this block.
		if ($textOnly) {
			return;
		}

		// Need to ensure that we have the "source"
		if (!$this->validate($block)) {
			return;
		}

		$themes = EB::themes();
		$themes->set('data', $block->data);
		$output = $themes->output('site/blocks/module');

		return $output;
	}
}