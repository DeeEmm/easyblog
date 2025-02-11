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

class EasyBlogGdprSubscription extends EasyBlog
{
	public $userId = null;
	public $type = null;
	public $params = null;

	public function __construct($id, $params)
	{
		$this->userId = $id;
		$this->type = 'subscription';
		$this->params = $params;
	}

	/**
	 * Event trigger to process user's subscriptions for GDPR download on EasySocial
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
		$defaultLimit = 15;

		$model = EB::model('subscriptions');
		$items = $model->getSubscriptionsByUser($this->userId);

		if ($items) {
			foreach ($items as $row) {

				$item = $adapter->getTemplate($row->id, $adapter->type);

				$item->created = $row->created;

				$title = '';

				switch ($row->utype) {
					case EBLOG_SUBSCRIPTION_ENTRY:
						$post = EB::post($row->uid);
						$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_POST_TITLE', $post->getTitle());
						break;

					case EBLOG_SUBSCRIPTION_CATEGORY:
						$category = EB::table('Category');
						$category->load($row->uid);
						$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_CATEGORY_TITLE', $category->title);
						break;

					case EBLOG_SUBSCRIPTION_BLOGGER:
						$author = EB::user($row->uid);
						$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_AUTHOR_TITLE', $author->getName());
						break;

					case EBLOG_SUBSCRIPTION_TEAMBLOG:
						$team = EB::table('TeamBlog');
						$team->load($row->uid);

						$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_TEAMBLOG_TITLE', $team->getTitle());
						break;

					default:
						$title = JText::_('COM_EB_GDPR_SUBSCRIPTION_SITE_TITLE');
						break;
				}
				$item->title =  $title;

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
	 * Main function to process user subscription data for GDPR download.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function execute()
	{
		$model = EB::model('subscriptions');
		$items = $model->getSubscriptionsByUser($this->userId);

		$data = array();

		if ($items) {
			foreach ($items as $item) {

				$obj = new EasyBlogGdprTemplate();

				$type = $item->utype;
				$obj->id = $item->id;
				$obj->type = $this->type;

				$title = JText::_('COM_EB_GDPR_SUBSCRIPTION_SITE_TITLE');

				if ($item->utype == EBLOG_SUBSCRIPTION_ENTRY) {
					$post = EB::post($item->uid);
					$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_POST_TITLE', $post->getTitle());
				}

				if ($item->utype == EBLOG_SUBSCRIPTION_CATEGORY) {
					$category = EB::table('Category');
					$category->load($item->uid);
					$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_CATEGORY_TITLE', $category->title);
				}

				if ($item->utype == EBLOG_SUBSCRIPTION_BLOGGER) {
					$author = EB::user($item->uid);
					$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_AUTHOR_TITLE', $author->getName());
				}

				if ($item->utype == EBLOG_SUBSCRIPTION_TEAMBLOG) {
					$team = EB::table('TeamBlog');
					$team->load($item->uid);

					$title = JText::sprintf('COM_EB_GDPR_SUBSCRIPTION_TEAMBLOG_TITLE', $team->getTitle());
				}

				$created = EB::date($item->created)->format('Y-m-d');

				$obj->preview = $title . ' (' . $created . ')';
				$obj->created = $created;

				$data[] = $obj;
			}
		}

		return $data;
	}


}
