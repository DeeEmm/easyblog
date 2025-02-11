<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableMeta extends EasyBlogTable
{
	public $id = null;
	public $type = null;
	public $content_id = null;
	public $title = null;
	public $keywords = null;
	public $description	= null;
	public $canonical = null;
	public $indexing = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__easyblog_meta' , 'id' , $db );
	}

	/**
	 * Loads a meta data by a specific type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadByType($type, $id)
	{
		$db = EB::db();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->quoteName($this->_tbl);
		$query[] = 'WHERE ' . $db->quoteName('type') . '=' . $db->Quote($type);
		$query[] = 'AND ' . $db->quoteName('content_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$data = $db->loadObject();

		if (!$data) {
			return false;
		}

		return parent::bind($data);
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete($pk = null)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			return false;
		}

		return parent::delete($pk);
	}

	public function getTitle()
	{
		$title = '';

		switch ($this->id) {
			case 1:
				$title = JText::_('COM_EASYBLOG_LATEST_POSTS_PAGE');
				break;

			case 2:
				$title = JText::_('COM_EASYBLOG_CATEGORIES_PAGE');
				break;

			case 3:
				$title = JText::_('COM_EASYBLOG_TAGS_PAGE');
				break;

			case 4:
				$title = JText::_('COM_EASYBLOG_BLOGGERS_PAGE');
				break;

			case 5:
				$title = JText::_('COM_EASYBLOG_TEAM_BLOGS_PAGE');
				break;

			case 6:
				$title = JText::_('COM_EASYBLOG_FEATURED_POSTS_PAGE');
				break;

			case 7:
				$title = JText::_('COM_EASYBLOG_ARCHIVE_PAGE');
				break;

				case 8:
				case 30:
				$title = JText::_('COM_EASYBLOG_SEARCH_PAGE');
				break;

			default:
				$title = $this->_getTitle($this->id);

		}

		return $title;
	}

	public function _getTitle($id)
	{
		$db = EB::db();

		$query = 'SELECT `type`, `content_id` FROM ' . $db->quoteName('#__easyblog_meta') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result) {
			$result	= new stdClass;
			$result->type	= '';
		}

		$query = '';

		switch ($result->type) {
			case 'post':
				$query = 'SELECT `title` FROM ' . $db->quoteName('#__easyblog_post') . ' WHERE id = ' . $db->Quote($result->content_id);
				break;

			case 'blogger':
				$query = 'SELECT `name` AS title  FROM ' . $db->quoteName('#__users') . ' WHERE id = ' . $db->Quote($result->content_id);
				break;

			case 'team':
				$query = 'SELECT `title`  FROM ' . $db->quoteName('#__easyblog_team') . ' WHERE id = ' . $db->Quote($result->content_id);
				break;

			case 'category':
				$query = 'SELECT `title`  FROM ' . $db->quoteName('#__easyblog_category') . ' WHERE id = ' . $db->Quote($result->content_id);
				break;

			case 'tag':
				$query = 'SELECT `title`  FROM ' . $db->quoteName('#__easyblog_tag') . ' WHERE id = ' . $db->Quote($result->content_id);
				break;

			default:
				return 'unknown';
				break;
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
