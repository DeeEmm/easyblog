<?php
/**
* EasyBlog component extension for SEF Advance
*
* This extension will give the SEF Advance style URLs to the EasyBlog component
* Place this file (sef_ext.php) in the main component directory
*
* Copyright (C) 2010 StackIdeas, http://www.stackideas.com, All rights reserved.
**/

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class sef_easyblog
{
	/**
	 * Determines if EasyBlog exists on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_easyblog/router.php';

		if (!JFile::exists($file)) {
			return false;
		}

		include_once($file);

		return true;
	}

	/**
	 * Creates the SEF Advanced URL
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($string)
	{
		if (!$this->exists()) {
			return false;
		}

		$config = EB::getConfig();

		$db = EB::db();

		// SEF string
		$segments = array();
		$query = $this->getVars($string);

		// Pass this to EasyBlog's router to build it
		$segments = EasyBlogBuildRoute($query);

        // Convert the segments into a sef url
        $sefUrl = implode('/', $segments);
        $sefUrl = rtrim($sefUrl, '/');

        return $sefUrl;
    }

	/**
	 * Reverts the url and returns the correct query string
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revert($items, $pos)
	{
		if (!$this->exists()) {
			return false;
		}

		$segments = array();

		// First segment is always the menu alias.
		unset($items[0]);

		// Cleanup the segments
		if ($items) {
			foreach ($items as $value) {

				if (!$value) {
					continue;
				}

				$segments[] = $value;
			}
		}

		if (!$segments) {
			return '&option=com_easyblog&Itemid=' . EBR::getItemId();
		}

		$query = EasyBlogParseRoute($segments);
		$items = array();

		// Get the correct item id based on the view.
		if (isset($query['view'])) {
			$query['Itemid'] = EBR::getItemId($query['view']);
		}

		if ($query) {
			foreach ($query as $key => $value) {
				$items[] = $key . '=' . $value;

				$_GET[$key] = $value;
			}
		}

		$items = array_reverse($items);
		$query = '&' . implode('&', $items) . '&Itemid=' . EBR::getItemId();


		return $query;
    }

	/**
	 * Given a string, explode the parts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVars($string)
	{
		$string = str_ireplace('&amp;', '&', $string);
		$string = str_ireplace('option=com_easyblog', '', $string);
		$string = str_ireplace('index.php?', '', $string);

		$parts = explode('&', $string);
		$vars = array();

		foreach ($parts as $part) {
			if (!$part) {
				continue;
			}

			// Split the key=value
			list($key, $value) = explode('=', $part);

			if ($key == 'Itemid' || $key == 'lang') {
				continue;
			}

			$vars[$key] = $value;
		}

		return $vars;
	}
}
