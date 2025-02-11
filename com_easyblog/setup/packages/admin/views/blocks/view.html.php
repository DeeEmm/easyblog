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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewBlocks extends EasyBlogAdminView
{
	/**
	 * Displays the blocks listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.blocks');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		JToolbarHelper::publishList('blocks.publish');
		JToolbarHelper::unpublishList('blocks.unpublish');

		$model = EB::model('Blocks');

		$search = $model->getState('search', '');
		$filterGroup = $model->getState('filter_group', '');
		$filterState = $model->getState('filter_state', 'all');

		$options = [
			'filter_state' => $filterState,
			'search' => $search,
			'filter_group' => $filterGroup
		];

		$this->setHeading('COM_EASYBLOG_TITLE_BLOCKS', '', 'fa-cubes');

		$blocks = $model->getBlocks($options);
		$groups = $model->getGroups();
		$pagination = $model->getPagination($options);
		$groups = array_combine($groups, array_values($groups));

		$limit = $model->getState('limit');

		$this->set('limit', $limit);
		$this->set('filterState', $filterState);
		$this->set('filterGroup', $filterGroup);
		$this->set('groups', $groups);
		$this->set('pagination', $pagination);
		$this->set('blocks', $blocks);
		$this->set('search', $search);

		parent::display('blocks/default');
	}

	/**
	 * Display block installation layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function install($tpl = null)
	{
		$this->checkAccess('easyblog.manage.blocks');

		JToolBarHelper::custom('blocks.upload', 'upload', '', JText::_('COM_EASYBLOG_UPLOAD_AND_INSTALL_BUTTON'), false);

		$this->setHeading('COM_EASYBLOG_TITLE_BLOCKS_INSTALL');

		parent::display('blocks/install');
	}

	/**
	 * Displays the block configurations
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function form()
	{
		JToolBarHelper::apply('blocks.apply');
		JToolbarHelper::save('blocks.save');
		JToolBarHelper::cancel('blocks.cancel');

		$this->checkAccess('easyblog.manage.blocks');

		$this->setHeading('COM_EB_BLOCKS_EDIT_BLOCK');

		$id = $this->input->get('id', 0, 'int');

		$block = EB::table('Block');
		$block->load($id);

		// Render the form for the block
		$forms = $block->getForms();
		$params = $block->getParams();

		$this->set('params', $params);
		$this->set('block', $block);
		$this->set('forms', $forms);

		parent::display('blocks/form/default');
	}

	/**
	 * Displays a list of block templates
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function templates()
	{
		// Ensure the user has access to manage templates
		$this->checkAccess('easyblog.manage.blocks');

		$this->setHeading('COM_EB_BLOCKS_TEMPLATES_TITLE', '', 'fa-clipboard');

		// JToolBarHelper::addNew('blocks.createTemplate');
		JToolbarHelper::publishList('blocks.publishTemplate');
		JToolbarHelper::unpublishList('blocks.unpublishTemplate');
		JToolbarHelper::custom('blocks.copyTemplate', 'copy', '', JText::_('COM_EASYBLOG_DUPLICATE'));
		JToolbarHelper::deleteList(JText::_('COM_EB_CONFIRM_DELETE_BLOCK_TEMPLATES'), 'blocks.deleteBlockTemplates');

		EB::loadLanguages();

		$search = $this->app->getUserStateFromRequest('com_easyblog.blocktemplates.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));
		$order = $this->app->getUserStateFromRequest('com_easyblog.blocktemplates.filter_order', 'filter_order', 'id', 'cmd');

		$model = EB::model('BlockTemplates');
		$rows = $model->getItems();

		$pagination = $model->getPagination();
		$limit = $model->getState('limit');
		$templates = array();
		$ordering = array();

		foreach ($rows as $row) {
			$template = EB::table('BlockTemplates');
			$template->bind($row);

			$ordering[] = $template->id;

			$templates[] = $template;
		}

		$this->set('limit', $limit);
		$this->set('search', $search);
		$this->set('templates', $templates);
		$this->set('pagination', $pagination);
		$this->set('ordering', $ordering);
		$this->set('order', $order);

		parent::display('blocks/templates/default/default');
	}

	/**
	 * Allows admin to edit the template
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function editTemplate()
	{
		// Ensure the user has access to manage templates
		$this->checkAccess('easyblog.manage.blocks');

		$this->setHeading('COM_EB_BLOCKS_TEMPLATES_TITLE', '', 'fa-clipboard');

		JToolBarHelper::apply('blocks.applyFormTemplate');
		JToolbarHelper::save('blocks.saveFormTemplate');
		JToolBarHelper::cancel();

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('BlockTemplates');
		$template->load($id);

		$this->set('template', $template);

		parent::display('blocks/templates/edit/default');
	}
}
