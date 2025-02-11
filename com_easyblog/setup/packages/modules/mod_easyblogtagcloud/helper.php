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

class modEasyBlogTagCloudHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getTagCloud()
	{
		$order = $this->params->get('order', 'postcount');
		$sort = $this->params->get('sort', 'desc');
		$count = (int) trim( $this->params->get('count', 0) );
		if ($count < 0) {
			$count = 0;
		}

		$shuffeTags	= $this->params->get('shuffleTags', true);
		$min_size = $this->params->get('minsize', '10');
		$max_size = $this->params->get('maxsize', '30');
		$categoryBased = $this->params->get('categoryBased', false);

		$view = $this->input->get('view', '', 'var');
		$layout = $this->input->get('layout', '', 'var');

		// If this is not categories view, categoryBased is not usable
		if ($view != 'categories' && $layout != 'listings') {
			$categoryBased = false;
		}

		// if category-Based tags is enabled, we should get the category ID
		if ($categoryBased) {
			$id = $this->input->get('id', '', 'var');
			$categoryBased = $id;
		}

		$model = EB::model('Tags');
		$tagCloud = $model->getTagCloud($count, $order, $sort, false, '', $categoryBased);
		$extraInfo = array();

		$moduleLayout = $this->params->get('layout', 'default');

		// Remove the _: from the value
		$moduleLayout = str_replace('_:', '', $moduleLayout);

		if ($moduleLayout == 'default' && $shuffeTags) {
			shuffle($tagCloud);
		}

		$tags = [];

		// get the count for every tag
		foreach ($tagCloud as $item) {
			$tag = EB::table('Tag');
			$tag->bind($item);

			$tag->post_count = $item->post_count;
			$tags[] = $tag;
			$extraInfo[] = $item->post_count;
		}

		$minimum_count = 0;
		$maximum_count = 0;

		// get the min and max 
		if (!empty($extraInfo)) {
			$minimum_count = min($extraInfo);
			$maximum_count = max($extraInfo);
		}

		$spread = $maximum_count - $minimum_count;

		if ($spread == 0) {
			$spread = 1;
		}

		$cloud_html = '';
		$cloud_tags = array();

		//foreach ($tags as $tag => $count)
		for ($i = 0; $i < count($tags); $i++) {
			$row =& $tags[$i];

			$size = $min_size + ($row->post_count - $minimum_count) * ($max_size - $min_size) / $spread;
			$row->fontsize = $size;
		}

		return $tags;
	}
}