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

class modEasyBlogShowcaseHelper
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieves a list of items for the module
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$model = EB::model('Blog');

		// Determines if we should display featured or latest entries
		$type = $this->params->get('showposttype', 'featured');
		$layoutType = $this->params->get('layout', 'default');

		// Determines if we should filter by category
		$categoryId = $this->params->get('catid');

		$result = [];

		if ($categoryId && !is_array($categoryId)) {
			$categoryId = (int) $categoryId;
		}

		$excludeIds = [];

		// If type equal to latest only, we need to exclude featured post as well
		if ($type == 'latestOnly') {
			// Retrieve a list of featured blog posts on the site.
			$featured = $model->getFeaturedBlog();

			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		$inclusion = '';

		// Get a list of category inclusions
		$inclusion = EB::getCategoryInclusion($categoryId);

		$subCat = $this->params->get('subcat', 1);

		// Include child category in the inclusions
		if ($subCat && !empty($inclusion)) {

			$tmpInclusion = [];

			foreach ($inclusion as $includeCatId) {

				// Retrieve nested categories
				$category = new stdClass();
				$category->id = $includeCatId;
				$category->childs = null;

				EB::buildNestedCategories($category->id, $category);

				$linkage = '';
				EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

				$catIds = [];
				$catIds[] = $category->id;
				EB::accessNestedCategoriesId($category, $catIds);

				$tmpInclusion = array_merge($tmpInclusion, $catIds);
			}

			$inclusion = $tmpInclusion;
		}

		$count = (int) trim($this->params->get('count', 5));
		if ($count < 1) {
			$count = 5;
		}

		// Let's get the post now
		if (($type == 'all' || $type == 'latestOnly')) {
			$result = $model->getBlogsBy('', '', 'latest', $count, EBLOG_FILTER_PUBLISHED, null, null, $excludeIds, false, false, false, [], $inclusion, '', '', false, [], [], false, [], array('paginationType' => 'none'));
		}

		// If not latest posttype, show featured post.
		if ($type == 'featured') {
			$result = $model->getFeaturedBlog($inclusion, $count);
		}

		// If there's nothing to show at all, don't display anything
		if (!$result) {
			return $result;
		}

		$posts = EB::formatter('list', $result);

		// Randomize items
		if ($this->params->get('autoshuffle')) {
			shuffle($posts);
		}

		return $posts;
	}

	/**
	 * Retrieves the photo layout settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPhotoLayout()
	{
		$layout = $this->params->get('photo_layout');

		if (!$layout) {
			$layout = (object) [
				'width' => 300,
				'height' => 200,
				'crop' => false,
				'alignment' => 'left'
			];
		}

		$layout->variation = $this->params->get('photo_size', 'medium');

		if ($layout->alignment === 'default') {
			$layout->alignment = 'left';
		}

		if (!isset($layout->crop)) {
			$layout->crop = false;
		}

		return $layout;
	}
}
