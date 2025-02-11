<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogGdprComment extends EasyBlog
{
	public $userId = null;
	public $type = null;
	public $params = null;

	public function __construct($id, $params)
	{
		$this->userId = $id;
		$this->type = 'comment';
		$this->params = $params;
	}

	/**
	 * Event trigger to process user's comments for GDPR download on EasySocial
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

		$model = EB::model('comments');
		$items = $model->getGDPRComments($this->userId);

		if ($items) {
			foreach ($items as $row) {

				$item = $adapter->getTemplate($row->id, $adapter->type);

				$item->created = $row->created;

				$created = EB::date($row->created)->format($adapter->getDateFormat());

				$item->title = JText::sprintf('APP_USER_BLOG_GDPR_COMMENT_POSTED_ON', $created);
				$item->intro = $row->comment;
				$item->view = false;

				$adapter->tab->addItem($item);
			}
		}

		// for comments, we always finalize.
		$adapter->tab->finalize();

		return true;
	}

	/**
	 * Main function to process user subscription data for GDPR download.
	 * @since 5.2
	 * @access public
	 */
	public function execute()
	{
		$model = EB::model('comments');
		$items = $model->getGDPRComments($this->userId);

		$data = array();

		if ($items) {
			foreach ($items as $item) {

				$obj = new EasyBlogGdprTemplate();

				$obj->id = $item->id;
				$obj->type = $this->type;

				$created = EB::date($item->created)->format('Y-m-d');

				$obj->preview = JText::sprintf('COM_EB_GDPR_COMMENT_POSTED_ON', $created);
				$obj->preview .= '<br>' . $item->comment;
				$obj->created = $created;

				$data[] = $obj;
			}
		}

		return $data;
	}


}
