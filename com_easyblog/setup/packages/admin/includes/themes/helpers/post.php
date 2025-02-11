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

class EasyBlogThemesHelperPost
{
	/**
	 * Renders the admin tools for posts
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function admin(EasyBlogPost $post, $returnUrl = null)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('return', $returnUrl);

		$output= $theme->output('site/helpers/post/admin');

		return $output;
	}

	/**
	 * Renders the author meta of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function author($authorName, $authorPermalink, $icon = false)
	{
		static $cache = [];

		$index = md5($authorName . $authorPermalink);

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('authorName', $authorName);
			$theme->set('authorPermalink', $authorPermalink);
			$theme->set('icon', $icon);

			$cache[$index] = $theme->output('site/helpers/post/author');
		}

		return $cache[$index];
	}

	/**
	 * Renders the categories meta of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function category($categories, $icon = false)
	{
		$theme = EB::themes();
		$theme->set('categories', $categories);
		$theme->set('icon', $icon);

		$output = $theme->output('site/helpers/post/category');

		return $output;
	}

	/**
	 * Renders the contriburo metadata of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function contributor($contributor, $avatar = false)
	{
		static $cache = [];

		$index = $contributor->id;

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('contributor', $contributor);
			$theme->set('avatar', $avatar);

			$cache[$index] = $theme->output('site/helpers/post/contributor');
		}

		return $cache[$index];
	}

	/**
	 * Renders the copyrights of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function copyrights($copyrights)
	{
		static $cache;

		if (is_null($cache)) {
			$theme = EB::themes();
			$theme->set('copyrights', $copyrights);

			$cache = $theme->output('site/helpers/post/copyrights');
		}

		return $cache;
	}

	/**
	 * Renders the contriburo metadata of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function comments($total, $permalink, $icon = false)
	{
		static $cache = [];

		$index = $total;

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('total', $total);
			$theme->set('permalink', $permalink);
			$theme->set('icon', $icon);

			$cache[$index] = $theme->output('site/helpers/post/comments');
		}

		return $cache[$index];
	}

	/**
	 * Renders the date area of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function date(EasyBlogPost $post, $dateSource, $format = 'DATE_FORMAT_LC1', $icon = false)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('dateSource', $dateSource);
		$theme->set('format', $format);
		$theme->set('icon', $icon);

		$output = $theme->output('site/helpers/post/date');

		return $output;
	}

	/**
	 * Renders the featured metadata of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function featured($icon = false, $text = true)
	{
		static $cache = [];

		$index = $icon . $text;

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('icon', $icon);
			$theme->set('text', $text);

			$cache[$index] = $theme->output('site/helpers/post/featured');
		}

		return $cache[$index];
	}

	/**
	 * Renders custom fields for ap ost
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function fields(EasyBlogPost $post, $fields)
	{
		if (!$fields) {
			return;
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('fields', $fields);

		$output = $theme->output('site/helpers/post/fields');

		return $output;
	}

	/**
	 * Renders the hits meta of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function hits(EasyBlogPost $post, $icon = false)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('icon', $icon);

		$output = $theme->output('site/helpers/post/hits');

		return $output;
	}

	/**
	 * Renders the title area of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function icon($icon, $class = '')
	{
		static $cache = [];

		$icon = $icon ? $icon : 'standard';
		$index = $icon . $class;

		if (!isset($cache[$index])) {
			$theme = EB::themes();
			$theme->set('icon', $icon);
			$theme->set('class', $class);

			$cache[$index] = $theme->output('site/helpers/post/icon/default');
		}

		return $cache[$index];
	}

	/**
	 * Renders the location area of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function location(EasyBlogPost $post)
	{
		$theme = EB::themes();
		$theme->set('post', $post);

		$output = $theme->output('site/helpers/post/location');
		return $output;
	}

	/**
	 * Renders the protected form
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function protectedPost(EasyBlogPost $post, $options = [])
	{
		// This is needed by the 3rd party extension when it wants to render this
		$idRequired = EB::normalize($options, 'idRequired', false);

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('idRequired', $idRequired);

		$output = $theme->output('site/helpers/post/protected');
		return $output;
	}

	/**
	 * Renders the ratings part of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function ratings($post, $locked = false)
	{
		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('locked', $locked);

		$output = $theme->output('site/helpers/post/ratings');
		return $output;
	}

	/**
	 * Renders the reactions part of a post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function reactions($post)
	{
		$output = EB::reactions($post)->html();

		return $output;
	}

	/**
	 * Renders the social buttons for a post. It can be used anywhere, either from the listing or the entry since they share the same DOM
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function socialShare(EasyBlogPost $post, $type = 'listings')
	{
		$output = EB::socialbuttons()->html($post, $type);

		return $output;
	}

	/**
	 * Renders the tags for a post. It can be used anywhere, either from the listing or the entry since they share the same DOM
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function tags($tags)
	{
		if (!$tags) {
			return;
		}

		$theme = EB::themes();
		$theme->set('tags', $tags);
		$output = $theme->output('site/helpers/post/tags');

		return $output;
	}
}
