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

class EasyBlogViewMetas extends EasyBlogAdminView
{
	/**
	 * Default method to display meta
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.meta');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout($tpl);
		}

		// Set the heading
		$this->setHeading('COM_EASYBLOG_TITLE_METAS', '', 'fa-unlink');

		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_METAS_DELETE_META_CONFIRMATION'), 'meta.delete');
		JToolBarHelper::custom('meta.restore', '' , '' , JText::_('COM_EB_UPDATE_MISSING_META'), false);

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.metas.filter_state', 'filter_state', '*', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.metas.search', 'search', '', 'string');

		$type = $this->app->getUserStateFromRequest('com_easyblog.metas.filter_type', 'filter_type', 'view', 'word');

		$search = trim(EBString::strtolower($search));
		$order = $this->app->getUserStateFromRequest('com_easyblog.metas.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.metas.filter_order_Dir', 'filter_order_Dir', '', 'word');

		//Get data from the model
		$model = EB::model('Metas');
		$metas = $model->getItems($type);
		$limit = $model->getState('limit');

		//filtering
		$filter = new stdClass();
		$filter->type = $type;
		$filter->search = $this->app->getUserStateFromRequest('com_easyblog.meta.search', 'search', '', 'string');

		for ($i = 0; $i < count($metas); $i++) {
			$meta =& $metas[$i];

			switch ($meta->id) {
				case 1:
					$meta->title = JText::_('COM_EASYBLOG_LATEST_POSTS_PAGE');
					break;

				case 2:
					$meta->title = JText::_('COM_EASYBLOG_CATEGORIES_PAGE');
					break;

				case 3:
					$meta->title = JText::_('COM_EASYBLOG_TAGS_PAGE');
					break;

				case 4:
					$meta->title = JText::_('COM_EASYBLOG_BLOGGERS_PAGE');
					break;

				case 5:
					$meta->title = JText::_('COM_EASYBLOG_TEAM_BLOGS_PAGE');
					break;

				case 6:
					$meta->title = JText::_('COM_EASYBLOG_FEATURED_POSTS_PAGE');
					break;

				case 7:
					$meta->title = JText::_('COM_EASYBLOG_ARCHIVE_PAGE');
					break;

				case 8:
				case 30:
					$meta->title = JText::_( 'COM_EASYBLOG_SEARCH_PAGE' );
					break;
			}
		}

		// Get the pagination
		$pagination = $model->getPagination($type);

		// Get the filter states
		// $filterState = JHTML::_('grid.state', $filter_state);
		$filterState = '';

		//get the filter type
		$filterTypes = $this->getFilterTypes();
		$filterTypes = array_combine($filterTypes, array_values($filterTypes));

		$this->set('limit', $limit);
		$this->set('metas', $metas);
		$this->set('pagination', $pagination);
		$this->set('type', $type);
		$this->set('filter', $filter);
		$this->set('state', $filterState);
		$this->set('filterTypes', $filterTypes);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('metas/default');
	}

	/**
	 * Displays the meta form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->setHeading('COM_EASYBLOG_META_TAG_EDIT', '', 'fa-unlink');

		JToolBarHelper::apply('meta.apply');
		JToolBarHelper::save('meta.save');
		JToolBarHelper::divider();

		JToolBarHelper::cancel('meta.cancel');

		// Get the meta id
		$id = $this->input->get('id', '', 'int');

		// Load the meta data
		$meta = EB::table('Meta');
		$meta->load($id);

		// we need to add remove button if this meta is a post type.
		if ($id && $meta->type == 'post') {
			JToolBarHelper::trash('meta.remove');
		}

		$this->set('meta', $meta);

		parent::display('metas/form/default');
	}

	public static function getIndexing($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix = '')
	{
		if (is_object($value))
		{
			$value = $value->indexing;
		}

		$img = $value ? $img1 : $img0;
		$task = $value ? 'removeIndexing' : 'addIndexing';
		$alt = $value ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');
		$action = $value ? JText::_('JLIB_HTML_UNPUBLISH_ITEM') : JText::_('JLIB_HTML_PUBLISH_ITEM');

		$href = '';

		$href = JHTML::_('grid.boolean', $i, $value, $prefix . $task, $prefix . $task);

		return $href;
	}

	public function getFilterTypes()
	{
		$filter = [
			'view',
			'blogger',
			'post',
			'team',
			'category',
			'tag'
		];

		return $filter;
	}
}
