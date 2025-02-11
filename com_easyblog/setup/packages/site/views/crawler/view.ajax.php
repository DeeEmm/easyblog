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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewCrawler extends EasyBlogView
{
	/**
	 * Given a specific URL, try to crawl the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function crawl()
	{
		// Only authors with write access should be allowed to use this since crawlers
		// are only used for blocks
		if (!$this->acl->get('add_entry')) {
			die();
		}

		// Get a list of urls to crawl
		$urls = $this->input->get('url', array(), 'array');

		if (!is_array($urls)) {
			$urls = array($urls);
		}

		// Result placeholder
		$result = array();
		$message = '';

		if (!$urls || empty($urls)) {
			return $this->ajax->reject();
		}

		// Get the crawler library
		$crawler = EB::crawler();

		foreach ($urls as $url) {

			// Ensures that the domain is valid
			if (!EB::string()->isValidDomain($url)) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_EMPTY'));
			}

			// Crawl the url
			$state = $crawler->crawl($url);

			// Get the data from the crawled site
			if ($state) {
				$data = $crawler->getData();

				if (!isset($data['title']) || !$data['title']) {
					$data['title'] = JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_DEFAULT_TITLE');
				}

				if (!isset($data['description']) || !$data['description']) {
					$data['description'] = JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_DEFAULT_DESCRIPTION');
				}

				// check for the oembed video, if there doesn't return any oembed data
				// we need to return a message here
				if (isset($data['oembed'])) {
					if (isset($data['oembed']->error) && $data['oembed']->error) {
						$message = $data['oembed']->errorMsg;
					}
				}

			} else {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_NOT_REACHABLE'));
			}

			$result[$url] = $data;
		}

		return $this->ajax->resolve($result, $message);
	}
}
