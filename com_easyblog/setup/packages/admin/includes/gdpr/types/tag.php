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

class EasyBlogGdprTag extends EasyBlog
{
	public $userId = null;
	public $type = null;
	public $params = null;

	public function __construct($id, $params)
	{
		$this->userId = $id;
		$this->type = 'tag';
		$this->params = $params;
	}

	/**
	 * Event trigger to process user's categories for GDPR download on EasySocial
	 *
	 * @since 5.2.5
	 * @access public
	 */
	public function onEasySocialGdprExport(SocialGdprSection &$section, SocialGdprItem $adapter)
	{
		// manually set type here.
		$adapter->type = $section->key . '_' . $this->type;

		// create tab in section
		$adapter->tab = $section->createTab($adapter);

		// get data.
		$model = EB::model('tags');
		$items = $model->getBloggerTags($this->userId);

		if ($items) {
			foreach ($items as $row) {

				$item = $adapter->getTemplate($row->id, $adapter->type);

				$item->created = $row->created;
				$item->title =  $row->title;
				$created = EB::date($row->created)->format($adapter->getDateFormat());
				$item->intro = $created;
				$item->view = false;

				$adapter->tab->addItem($item);
			}
		}

		// for cateogories, we always finalize.
		$adapter->tab->finalize();

		return true;
	}

	/**
	 * Main function to process user tag data for GDPR download.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function execute()
	{
		$model = EB::model('tags');
		$items = $model->getBloggerTags($this->userId);

		$data = array();

		if ($items) {
			foreach ($items as $item) {

				$obj = new EasyBlogGdprTemplate();
				$obj->id = $item->id;
				$obj->preview = $item->title;
				$obj->created = EB::date($item->created)->format('Y-m-d');

				$data[] = $obj;
			}
		}

		return $data;
	}


}
