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

class modEasyBlogBloggerCloudHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	public function getBloggerCloud()
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

		$view = $this->input->get('view', '', 'var');
		$layout = $this->input->get('layout', 'default', 'var');

		$model = EB::model('Bloggers');
		$bloggerCloud = $model->getBloggerCloud($count, $order, $sort, false);
		$extraInfo = array();

		$moduleLayout = $this->params->get('layout', 'default');

		// Remove the _: from the value
		$moduleLayout = str_replace('_:', '', $moduleLayout);

		if ($moduleLayout == 'default' && $shuffeTags) {
			shuffle($bloggerCloud);
		}

		$bloggers = [];

		// Get the count for every tag
		foreach ($bloggerCloud as $item) {
			$blogger = EB::table('Profile');
			$blogger->load($item->id);

			$blogger->post_count = $item->post_count;
			$bloggers[] = $blogger;
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
		for ($i = 0; $i < count($bloggers); $i++) {
			$row =& $bloggers[$i];

			$size = $min_size + ($row->post_count - $minimum_count) * ($max_size - $min_size) / $spread;
			$row->fontsize = $size;
		}

		return $bloggers;
	}
}