<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modEasyBlogListHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getPosts()
	{
		// Get the default sorting and ordering
		$sort = $this->normalizeSorting($this->params->get('sorting', 'desc'));
		$order = $this->normalizeOrdering($this->params->get('ordering', 'created'));

		// Get the total number of posts to display
		$limit = (int) trim($this->params->get('count', 5));
		if ($limit < 1) {
			$limit = 5;
		}

		// Determines if the user wants to filter items by specific ategories
		$categories = $this->params->get('catid', array(), 'array');

		$includeAuthors = $this->params->get('inclusion_authors', array());
		$excludeAuthors = $this->params->get('exclusion_authors', array());

		$options = array(
						'sort' => $sort,
						'ordering' => $order
					);
		
		$model = EB::model('Category');
		$result = $model->getPosts($categories, $limit, $includeAuthors, $excludeAuthors, $options);
		$posts = array();

		if (!$result) {
			return $posts;
		}

		$posts = EB::formatter('list', $result);

		return $posts;
	}

	/**
	 * Fix ordering value that was reversed on 5.0
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeOrdering($ordering)
	{
		if ($ordering == 'asc' || $ordering == 'desc') {
			return 'created';
		}

		return $ordering;
	}

	/**
	 * Fix sorting value that was reversed on 5.0
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeSorting($sorting)
	{
		if ($sorting == 'latest' || $sorting == 'alphabet' || $sorting == 'popular') {
			return 'desc';
		}

		return $sorting;
	}
}