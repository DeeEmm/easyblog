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

class EasyBlogViewReactions extends EasyBlogAdminView
{
	/**
	 * Displays a list of comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.reactions');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}
		// Set the page heading
		$this->setHeading('COM_EASYBLOG_TITLE_REACTIONS');

		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_REACTIONS_CONFIRM_DELETE', true), 'reactions.delete');

		$model = EB::model('Reactions');
		$result = $model->getData();
		$pagination = $model->getPagination();
		$limit = $model->getState('limit');

		$reactions = array();

		if ($result) {
			foreach ($result as &$row) {
				$reaction = EB::table('ReactionHistory');
				$reaction->bind($row);

				$reaction->type = $row->type;
				$reaction->post = EB::post($row->post_id);
				$reaction->user = EB::user($row->user_id);

				$reactions[] = $reaction;
			}
		}

		$this->set('limit', $limit);
		$this->set('reactions', $reactions);
		$this->set('pagination', $pagination);

		parent::display('reactions/default');
	}
}
