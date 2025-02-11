<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogSocialButtons extends EasyBlog
{
	/**
	 * Generates the social buttons html codes
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html(EasyBlogPost &$post, $view)
	{
		// Get the button type
		$type = $this->config->get('social_button_type');

		// Ensure that social buttons has been enabled
		if ($type == 'disabled') {
			return;
		}

		// Generate internal buttons
		if ($type == 'internal') {
			return $this->getInternalButtons($post);
		}

		// Generate internal buttons
		if ($type == 'external') {
			return $this->getExternalButtons($post);
		}

		// Append the necessary scripts to the page
		if ($type == 'addthis') {
			EB::scripts()->addScript('//s7.addthis.com/js/300/addthis_widget.js#pubid=' . $this->config->get('social_addthis_customcode'));
		}

		$namespace = 'site/socialbuttons/' . $type;

		if ($type === 'sharethis') {

			$legacycode = $this->config->get('social_sharethis_publishers');
			$propertyid = $this->config->get('social_sharethis_property');

			if (!$propertyid) {
				$namespace .= '.legacy';
			} else {
				EB::scripts()->addScript('//platform-api.sharethis.com/js/sharethis.js#property=' . $propertyid . '&product=inline-share-buttons', false, true);
			}
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$output = $theme->output($namespace);

		return $output;
	}

	/**
	 * Generates the DOM for external social buttons
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getExternalButtons(EasyBlogPost &$post)
	{
		$external = $this->get('External', $post);

		return $external->html();
	}

	/**
	 * Generates the DOM for internal social buttons
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getInternalButtons(EasyBlogPost &$post)
	{
		$internal = $this->get('Internal', $post);

		return $internal->html();
	}

	/**
	 * Retrieve a specific button type
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function get($button, EasyBlogPost &$blog)
	{
		$adapter = $this->getAdapter($button);

		if (!$adapter) {
			return false;
		}

		$className = 'EasyBlogSocialButton' . ucfirst($button);


		$isFrontpage = true;

		$view = $this->input->get('view', '');

		if ($view == 'entry') {
			$isFrontpage = false;
		}

		$options = array();
		$options['frontpage'] = $isFrontpage;

		$obj = new $className($blog, $options);

		return $obj;
	}

	/**
	 * Retrieves an adapter for a social button
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function getAdapter($button)
	{
		$file = __DIR__ . '/adapters/' . strtolower($button) . '.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}


	/**
	 * Backward compatibility for 5.0 as some 5.1 site still has
	 * overrides which uses old social buttons
	 *
	 * @since	5.1.3
	 * @access	public
	 */
	public function enabled()
	{
		return true;
	}
}
