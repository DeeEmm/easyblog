<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

JFormHelper::loadFieldClass('groupedlist');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldEasyBlogMenuItem extends JFormFieldGroupedList
{
	protected $type = 'EasyBlogMenuItem';

	protected function getGroups()
	{
		// Include engine
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

		if (!EB::isFoundryEnabled()) {
			return;
		}

		// Initialize variables.
		$groups = array();

		// Initialize some field attributes.
		$menuType = (string) $this->element['menu_type'];
		$published = $this->element['published'] ? explode(',', (string) $this->element['published']) : array();
		$disable = $this->element['disable'] ? explode(',', (string) $this->element['disable']) : array();

		// Get the menu items.
		$items = $this->getMenuLinks($menuType, 0, 0, $published);



		// Build group for a specific menu type.
		if ($menuType)
		{
			// Initialize the group.
			$groups[$menuType] = array();

			// Build the options array.
			foreach ($items as $link)
			{
				$groups[$menuType][] = JHtml::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
			}
		}
		// Build groups for all menu types.
		else
		{
			// Build the groups arrays.
			foreach ($items as $menu)
			{
				// Initialize the group.
				$groups[$menu->menutype] = array();

				// Build the options array.
				foreach ($menu->links as $link)
				{
					$groups[$menu->menutype][] = JHtml::_(
						'select.option', $link->value, $link->text, 'value', 'text',
						in_array($link->type, $disable)
					);
				}
			}
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;

	}

	protected function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published=array() )
	{
		$db = EB::db();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level, a.menutype, a.type, a.template_style_id, a.checked_out');
		$query->from('#__menu AS a');
		$query->join('LEFT', $db->nameQuote('#__menu').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Filter by the type
		if ($menuType) {
			$query->where('(a.menutype = '.$db->quote($menuType).' OR a.parent_id = 0)');
		}

		if ($parentId) {
			if ($mode == 2) {
				// Prevent the parent and children from showing.
				$query->join('LEFT', '#__menu AS p ON p.id = '.(int) $parentId);
				$query->where('(a.lft <= p.lft OR a.rgt >= p.rgt)');
			}
		}

		if (!empty($published)) {
			if (is_array($published)) $published = '(' . implode(',', $published) .')';
			$query->where('a.published IN ' . $published);
		}

		$query->where('a.published != -2');

		$query->where('a.link LIKE ' . $db->Quote('%com_easyblog%') );

		$query->group('a.id, a.title, a.level, a.menutype, a.type, a.template_style_id, a.checked_out, a.lft');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$links = $db->loadObjectList();

		// Check for a database error.
		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
			return false;
		}

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($links as &$link) {
			$link->text = str_repeat('- ', $link->level).$link->text;
		}

		if (empty($menuType)) {
			// If the menutype is empty, group the items by menutype.
			$query->clear();
			$query->select('*');
			$query->from('#__menu_types');
			$query->where('menutype <> '.$db->quote(''));
			$query->order('title, menutype');
			$db->setQuery($query);

			$menuTypes = $db->loadObjectList();

			// Check for a database error.
			if ($error = $db->getErrorMsg()) {
				JError::raiseWarning(500, $error);
				return false;
			}

			// Create a reverse lookup and aggregate the links.
			$rlu = array();
			foreach ($menuTypes as &$type) {
				$rlu[$type->menutype] = &$type;
				$type->links = array();
			}

			// Loop through the list of menu links.
			foreach ($links as &$link) {
				if (isset($rlu[$link->menutype])) {
					$rlu[$link->menutype]->links[] = &$link;

					// Cleanup garbage.
					unset($link->menutype);
				}
			}

			$easyBlogMenuTypes  = array();

			foreach ($menuTypes as $mtype) {
				if( count( $mtype->links ) > 0 )
				{
					$easyBlogMenuTypes[] = $mtype;
				}
			}

			return $easyBlogMenuTypes;
		} else {
			return $links;
		}
	}
}
