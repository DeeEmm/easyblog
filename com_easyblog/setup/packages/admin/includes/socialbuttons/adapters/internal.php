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

class EasyBlogSocialButtonInternal
{
	public $buttons = [
		'facebook',
		'twitter',
		'linkedin',
		'xing',
		'vk',
		'pinterest',
		'reddit',
		'pocket'
	];

	public $post = '';

	public function __construct(EasyBlogPost $post, $options = [])
	{
		$this->post = $post;
	}

	/**
	 * Creates a new button instance
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getButton($button)
	{
		// Include dependencies
		require_once(__DIR__ . '/internal/abstract.php');

		$file = __DIR__ . '/internal/' . $button . '.php';

		require_once($file);

		$className = 'EasyBlogInternalButton' . ucfirst($button);
		$obj = new $className($button, $this->post);

		return $obj;
	}

	/**
	 * Retrieves a list of buttons enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getButtons()
	{
		$buttons = [];
		$config = EB::config();

		foreach ($this->buttons as $name) {
			if ($config->get('social_button_' . $name)) {

				$button = $this->getButton($name);
				$buttons[] = $button;
			}
		}

		return $buttons;
	}

	/**
	 * Renders the DOM for the internal buttons
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html()
	{
		$buttons = $this->getButtons();

		// If there are no buttons enabled, just skip this
		if (!$buttons) {
			return;
		}

		$namespace = 'site/socialbuttons/internal';

		$theme = EB::themes();
		$theme->set('buttons', $buttons);
		$theme->set('post', $this->post);
		$output = $theme->output($namespace);

		return $output;
	}
}
