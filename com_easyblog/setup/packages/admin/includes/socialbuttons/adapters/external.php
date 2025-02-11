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

class EasyBlogSocialButtonExternal extends EasyBlog
{
	public $buttons = array(
				'facebook',
				'linkedin',								
				'twitter',
				'xing',
				'vk',
				'pinterest',
				'reddit',
				'pocket'
			);

	// This is a mapping because anything prior to 5.1, the settings are stored as non standardized names
	public $buttonConfigs = array('facebook' => 'main_facebook_like',
								  'pinterest' => 'main_pinit_button',
								  'linkedin' => 'main_linkedin_button',
								  'pocket' => 'main_pocket_button',
								  'reddit' => 'main_reddit_button',
								  'twitter' => 'main_twitter_button',
								  'vk' => 'main_vk',
								  'xing' => 'main_xing_button'
							);

	public function __construct(EasyBlogPost $post, $options = array())
	{
		parent::__construct();

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
		require_once(__DIR__ . '/external/abstract.php');

		$file = __DIR__ . '/external/' . $button . '.php';

		require_once($file);

		$className = 'EasyBlogExternalButton' . ucfirst($button);
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
		$buttons = array();
		
		foreach ($this->buttons as $name) {
			$enabled = $this->config->get($this->buttonConfigs[$name]);
			if ($enabled) {
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

		// If there are no buttons enabled, do not display anything
		if (!$buttons) {
			return;
		}

		$namespace = 'site/socialbuttons/external';

		$theme = EB::themes();
		$theme->set('buttons', $buttons);
		$theme->set('post', $this->post);
		$output = $theme->output($namespace);

		return $output;
	}
}
