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

class EasyBlogThemesHelperHeaders
{
	/**
	 * Renders the author header
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function author(EasyBlogTableProfile $author, $options = [])
	{
		static $jsLoaded = null;

		$showHeader = false;
		$viewOptions = [
			'name' => true,
			'avatar' => true,
			'rss' => true,
			'subscription' => true,
			'twitter' => true,
			'website' => true,
			'biography' => true,
			'isActivateBioTruncation' => true,
			'featureAction' => true
		];

		foreach ($viewOptions as $key => $value) {
			if (isset($options[$key])) {
				$viewOptions[$key] = $options[$key];
			}

			if ($viewOptions[$key]) {
				$showHeader = true;
			}
		}

		if (!$showHeader) {
			return;
		}

		// Convert it into an object
		$viewOptions = (object) $viewOptions;

		$isFeatured = (isset($author->featured)) ? $author->featured : $author->isFeatured();

		$theme = EB::themes();
		$theme->set('viewOptions', $viewOptions);
		$theme->set('author', $author);
		$theme->set('isFeatured', $isFeatured);
		$theme->set('jsLoaded', $jsLoaded);
		$output = $theme->output('site/helpers/headers/author');

		// So that subsequent calls would not load the js
		if (is_null($jsLoaded)) {
			$jsLoaded = true;
		}

		return $output;
	}

	/**
	 * Renders a category header
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function category(EasyBlogTableCategory $category, $options = array())
	{
		$showHeader = false;
		$viewOptions = array(
							'title' => true,
							'description' => true,
							'avatar' => true,
							'rss' => true,
							'subscription' => true,
							'subcategories' => true
						);

		// Always set description to false if there is no description
		if (!$category->description) {
			$options['description'] = false;
		}

		foreach ($viewOptions as $key => $value) {
			if (isset($options[$key])) {
				$viewOptions[$key] = $options[$key];
			}

			if ($viewOptions[$key]) {
				$showHeader = true;
			}
		}

		if (!$showHeader) {
			return;
		}


		// Convert it into an object
		$viewOptions = (object) $viewOptions;

		$theme = EB::themes();
		$theme->set('viewOptions', $viewOptions);
		$theme->set('category', $category);

		$output = $theme->output('site/helpers/headers/category');

		return $output;
	}

	/**
	 * Renders the header for a tag
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function tag($tag, $options = [])
	{
		$config = EB::config();
		$showTitle = EB::normalize($options, 'title', true);
		$showRss = EB::normalize($options, 'rss', true) && $config->get('main_rss');

		$theme = EB::themes();
		$theme->set('showTitle', $showTitle);
		$theme->set('showRss', $showRss);
		$theme->set('tag', $tag);

		$output = $theme->output('site/helpers/headers/tag');

		return $output;
	}

	/**
	 * Renders the team header
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function team(EasyBlogTableTeamBlog $team, $options = array())
	{
		static $jsLoaded = null;

		$returnUrl = base64_encode(EBFactory::getURI(true));

		$theme = EB::themes();
		$theme->set('team', $team);
		$theme->set('returnUrl', $returnUrl);
		$theme->set('jsLoaded', $jsLoaded);

		$output = $theme->output('site/helpers/headers/team');

		// So that subsequent calls would not load the js
		if (is_null($jsLoaded)) {
			$jsLoaded = true;
		}

		return $output;
	}
}
