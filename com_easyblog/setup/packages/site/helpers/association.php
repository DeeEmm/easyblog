<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// This file needs to be in the current location is this is where Joomla is searching for language association files

abstract class EasyBlogHelperAssociation
{
	/**
	 * Retrieves the association of a blog post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$view = is_null($view) ? $input->get('view') : $view;
		$id = empty($id) ? $input->getInt('id') : $id;

		if ($view == 'entry' && $id) {
			// We want to link it back to the correct page.
			$db = EB::db();

			$query = "";
			$query = "select p.`language`, a.`post_id` as `id`";
			$query .= " from `#__easyblog_associations` as a";
			$query .= " inner join `#__easyblog_associations` as b on a.`key` = b.`key`";
			$query .= " inner join `#__easyblog_post` as p on a.`post_id` = p.`id`";
			$query .= " where b.`post_id` = " . $db->Quote($id);

			$db->setQuery($query);
			$results = $db->loadObjectList();

			$assocs = array();
			if ($results) {
				foreach($results as $item) {
					$langs = explode('-', $item->language);

					// $url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $item->id . '&lang=' . $langs[0], false);
					// $url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $item->id, false, null, false, false, false);
					if (EBR::isSefEnabled()) {
						$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $item->id . '&lang=' . $langs[0], false, null, false, false, false);
					} else {
						$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $item->id, false, null, false, false, false);
					}
					$assocs[$item->language] = $url;
				}
			}
			return $assocs;
		}

		return array();
	}
}
