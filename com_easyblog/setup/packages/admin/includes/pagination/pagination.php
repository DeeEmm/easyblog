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

use Foundry\Libraries\Pagination;

class EasyBlogPagination extends Pagination
{

	public function __construct($total, $limitstart, $limit, $prefix = '')
	{
		parent::__construct(EB::fd(), $total, $limitstart, $limit, $prefix);

		// Flag indicates to not add limitstart=0 to URL
		$this->hideEmptyLimitstart = true;
	}

	/**
	 * Get current page url with pagination query
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getCurrentPageLink($view = 'latest', $external = false)
	{
		$data = $this->getData();

		$currentPageLink = 'index.php?option=com_easyblog&view=' . $view;

		// Need to inject the current easyblog item id. #3003
		$itemId = EB::request()->get('Itemid');
		$currentPageLink .='&Itemid=' . $itemId;

		$limitstart = $this->limitstart;

		if ($limitstart) {
			$limitstart = '&limitstart=' . $limitstart;
			$currentPageLink = $currentPageLink . $limitstart;
		}

		$url = JRoute::_($currentPageLink);

		// Ensure the url is internal
		if ($external && (stristr('http://', $url) !== false || stristr('https://', $url) !== false)) {
			$uri = JURI::getInstance();

			$url = ltrim($url, '/');
			$url = $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
		}

		return $url;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function get($property, $default = null)
	{
		if (method_exists(get_parent_class($this), 'get')) {
			return parent::get($property, $default);
		}

		if (strpos($property, '.')) {
			$prop = explode('.', $property);
			$prop[1] = ucfirst($prop[1]);
			$property = implode($prop);
		}

		if (isset($this->$property)) {
			return $this->$property;
		}

		return $default;
	}

}
