<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class modImageWallHelper extends EasyBlog
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
		$count = (int) $this->params->get('count', 0);
		if ($count < 0) {
			$count = 5;
		}

		// Retrieve the model from the component.
		$model = EB::model('Blog');

		$categories	= $this->params->get('catid');
		$type = !empty($categories) ? 'categoryimage' : 'image';

		if ($categories && !is_array($categories)) {
			$categories = explode(',', $categories);
			$categories = array_map('trim', $categories);
		}

		$sorting = array();

		$sorting[] = $this->params->get('sorting', 'latest');
		$sorting[] = $this->params->get('ordering', 'desc');

		$rows = $model->getBlogsBy($type, $categories, $sorting, $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, true, array(), array(), null, 'listlength', true, array(), array(), false, array(), array('paginationType' => 'none'));

		return $rows;
	}

	/**
	 * get post image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getImage($content)
	{
		$pattern = '#<img[^>]*>#i';

		preg_match($pattern, $content, $matches);

		if (isset($matches[0])) {
			return $matches[0];
		}

		return false;
	}
}
