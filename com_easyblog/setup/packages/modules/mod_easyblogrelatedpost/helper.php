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

class modRelatedPostHelper extends EasyBlog
{
	public $lib = null;
	public $params = [];

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieves a list of related posts against the current post being viewed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($id, $count = 0)
	{
		$post = EB::post($id);
		$model = EB::model('Blog');

		$behavior = $this->params->get('behavior', 'tags');

		$options = [];
		$options['ordering'] = $this->params->get('ordering', 'created');
		$options['sort'] = $this->params->get('sort', 'desc');

		$posts = $model->getRelatedPosts($post->id, $count, $behavior, $post->category_id, $post->getTitle(), $options);
		$posts = $this->lib->processItems($posts);

		return $posts;
	}
}
