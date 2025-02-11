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

class EasyBlogThemesHelperGrid extends EasyBlog
{
	/**
	 * Renders a pending moderation icon
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function moderation($controllerName)
	{
		$fd = EB::fd();

		// For moderated items, tasks should always be publish
		$task = $controllerName . '.publish';

		return $fd->html('table.moderation', $task);
	}

	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function core($obj, $key = '', $tooltip = [])
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'state';

		$fd = EB::fd();

		// We need to cast the object key to INT or otherwise, the checking is not working.
		$postStatus = (int) $obj->$key;

		$isCore = $postStatus === EASYBLOG_POST_TEMPLATE_CORE;

		if ($isCore) {
			$tooltip = isset($tooltip[1]) ? $tooltip[1] : 'COM_EASYBLOG_GRID_CORE';
		}

		if (!$isCore) {
			$tooltip = isset($tooltip[0]) ? $tooltip[0] : 'COM_EASYBLOG_GRID_NOT_CORE';
		}

		return $fd->html('table.core', $isCore, [
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders the specific icon for post template's global template column
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function globalTemplate($template, $disabled = false)
	{
		$fd = EB::fd();

		$publishTask = 'blogs.setGlobalTemplate';
		$unpublishTask = 'blogs.removeGlobalTemplate';

		$unsetGlobalTooltip = JText::_('COM_EASYBLOG_GRID_TOOLTIP_UNSET_AS_GLOBAL');
		$setGlobalTooltip = JText::_('COM_EASYBLOG_GRID_TOOLTIP_SET_AS_GLOBAL');

		$state = (int) $template->system;

		$tooltip = $state ? $setGlobalTooltip : $unsetGlobalTooltip;
		$task = $state ? $unpublishTask : $publishTask;

		$allowed = ($disabled) ? false : true;
		$class = 'publish';

		if ($state === EB_POST_TEMPLATE_GLOBAL) {
			$class = 'global';
		}

		if ($state === EB_POST_TEMPLATE_BLANK) {
			$class = 'unpublish';
			$tooltip = JText::_('COM_EASYBLOG_GRID_BLANK_TEMPLATE');
		}

		return $fd->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders the specific icon for post listing's status column
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function postStatus(EasyBlogPost $post, $key)
	{
		$fd = EB::fd();
		$publishTask = 'blogs.publish';
		$unpublishTask = 'blogs.unpublish';

		$allowed = true;
		$state = (int) $post->$key;

		// By default is published
		$class = 'publish';
		$tooltip = JText::_('COM_EASYBLOG_GRID_TOOLTIP_PUBLISH');

		if ($state === EASYBLOG_POST_SCHEDULED || $state === EASYBLOG_POST_ARCHIVED) {
			$class = $key === 'state' ? 'archived' : 'scheduled';
			$tooltip = $key === 'state' ? 'COM_EASYBLOG_GRID_TOOLTIP_ARCHIVED' : 'COM_EASYBLOG_SCHEDULED';
			$allowed = false;
		}

		if ($key === 'state' && $state === EASYBLOG_POST_TRASHED) {
			$class = 'trash';
			$tooltip = 'COM_EASYBLOG_TRASHED';
		}

		if ($state === EASYBLOG_POST_UNPUBLISHED) {
			$class = 'unpublish';
			$tooltip = 'COM_EASYBLOG_GRID_TOOLTIP_UNPUBLISH';
		}

		$task = $post->$key ? $unpublishTask : $publishTask;

		return $fd->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function published($obj, $controllerName = '', $key = '', $tasks = [], $tooltip = [], $disabled = false)
	{
		$fd = EB::fd();
		$input = EB::request();
		$view = $input->get('view', '', 'cmd');
		$layout = $input->get('layout', '', 'cmd');

		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'state';

		$publishTask = isset($tasks[0]) ? $tasks[0] : $controllerName . '.publish';
		$unpublishTask = isset($tasks[1]) ? $tasks[1] : $controllerName . '.unpublish';

		$allowed = ($disabled) ? false : true;

		// We need to cast the object key to INT or otherwise, the checking is not working.
		$state = (int) $obj->$key;

		if ($state) {
			$class = 'publish';
			$tooltip = isset($tooltip[1]) ? $tooltip[1] : 'COM_EASYBLOG_GRID_TOOLTIP_PUBLISH';
		}

		if (!$state) {
			$class = 'unpublish';
			$tooltip = isset($tooltip[0]) ? $tooltip[0] : 'COM_EASYBLOG_GRID_TOOLTIP_UNPUBLISH';
		}

		if (is_array($tooltip) && empty($tooltip)) {
			$tooltip = '';
		}

		$task = $obj->$key ? $unpublishTask : $publishTask;

		return $fd->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders the lock in a grid
	 *
	 * @since	5.4
	 * @access	public
	 */
	public static function locked($obj, $tasks = [], $tooltip = [], $disabled = false)
	{
		$fd = EB::fd();

		$isLocked = $obj->isLocked();
		$allowed = $disabled ? false : true;
		$lockTask = isset($tasks[0]) ? $tasks[0] : 'blogs.lockTemplate';
		$unlockTask = isset($tasks[1]) ? $tasks[1] : 'blogs.unlockTemplate';

		if ($isLocked) {
			$task = $unlockTask;
			$tooltip = isset($tooltip[0]) ? $tooltip[0] : 'COM_EB_GRID_TOOLTIP_TEMPLATE_IS_LOCKED';
		}

		if (!$isLocked) {
			$task = $lockTask;
			$tooltip = isset($tooltip[1]) ? $tooltip[1] : 'COM_EB_GRID_TOOLTIP_TEMPLATE_IS_UNLOCKED';
		}

		return $fd->html('table.locked', $isLocked, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders featured icon.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function featured($obj, $controllerName = '', $key = '', $task = '', $allowed = true, $tooltip = array())
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'default';
		$fd = EB::fd();

		$featureTask = '';
		$unfeatureTask = '';

		if (is_array($task)) {
			$featureTask = $task[0];
			$unfeatureTask = $task[1];
		}

		if (!is_array($task)) {
			$featureTask = !empty($task) ? $task : 'easyblog.toggleDefault';
			$unfeatureTask = $featureTask;
		}

		// We need to cast the object key to INT or otherwise, the checking is not working.
		$state = (int) $obj->$key;

		$class = 'default';
		$task = $featureTask;
		$tooltip = isset($tooltip[0]) ? $tooltip[0] : 'COM_EASYBLOG_GRID_TOOLTIP_FEATURE_ITEM';

		if ($state === EASYBLOG_POST_PUBLISHED) {
			$class = 'featured';
			$tooltip = '';

			if ($allowed) {
				$tooltip = isset($tooltip[1]) ? $tooltip[1] : 'COM_EASYBLOG_GRID_TOOLTIP_UNFEATURE_ITEM';
			}

			$task = $unfeatureTask;
		}

		return $fd->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	public function listbox($name, $items, $options = array())
	{
		$options = array_merge(array(
			'attributes' => '',
			'classes' => '',
			'id' => $name,
			'sortable' => false,
			'toggleDefault' => true,
			'allowAdd' => true,
			'allowRemove' => true,
			'customHTML' => '',
			'itemTitle' => JText::_('COM_EASYBLOG_GRID_LISTBOX_DEFAULT_ITEM_TITLE'),
			'addTitle' => JText::_('COM_EASYBLOG_GRID_LISTBOX_ADD_NEW_ITEM_TITLE'),
			'default' => 0,
			'max' => 0,
			'min' => 0,
			'allowInput' => false
		), $options);

		if (is_array($options['attributes'])) {
			$options['attributes'] = implode(' ', $options['attributes']);
		}

		if (is_array($options['classes'])) {
			$options['classes'] = implode(' ', $options['classes']);
		}

		if (!is_array($items)) {
			$items = array($items);
		}

		$theme = EB::themes();

		$theme->set('name', $name);
		$theme->set('items', $items);
		$theme->set('options', $options);

		return $theme->output('admin/html/grid.listbox');
	}
}
