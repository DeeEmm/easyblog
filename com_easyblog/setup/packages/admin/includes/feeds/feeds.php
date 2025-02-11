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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogFeeds extends EasyBlog
{
	/**
	 * Adds the respective link tags on the head of the document
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function addHeaders($url)
	{
		// If rss is disabled or the current view type is not of html, do not add the headers
		if (!$this->config->get('main_rss') || $this->doc->getType() != 'html') {
			return false;
		}

		// Add rss link for feedburner
		if ($this->config->get('main_feedburner')) {
			$this->doc->addHeadLink($this->config->get('main_feedburner_url'), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));

			return;
		}

		// If feedburner is not enabled, we use the normal blogs rss links
		$sef = EBR::isSefEnabled();
		$join = $sef ? '?' : '&';

		// Add default rss feed link
		$this->doc->addHeadLink(EBR::_($url) . $join . 'format=feed&type=rss', 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
		$this->doc->addHeadLink(EBR::_($url) . $join . 'format=feed&type=atom', 'alternate', 'rel', array('type' => 'application/atom+xml', 'title' => 'Atom 1.0'));
	}

	/**
	 * Appends the necessary rss fragments on existing url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFeedURL($url, $atom = false, $type = 'site')
	{
		if ($this->config->get('main_feedburner') && $type == 'site' && $this->config->get('main_feedburner_url') != '') {
			return $this->config->get('main_feedburner_url');
		}

		$join = EBR::isSefEnabled() ? '?' : '&';

		// Append the necessary queries
		$url = EBR::_($url) . $join . 'format=feed';
		$url .= $atom ? '&type=atom' : '&type=rss';

		return $url;
	}

	/**
	 * Gets an adapter formatter for feed imports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAdapter($adapter)
	{
		$file = __DIR__ . '/adapters/' . strtolower($adapter) . '.php';

		require_once($file);

		$className = 'EasyBlogFeedAdapter' . ucfirst($adapter);
		$obj = new $className();

		return $obj;
	}

	/**
	 * Main method to import the feed items
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function import(EasyBlogTableFeed &$feed, $limit = 0)
	{
		// Load site language file
		EB::loadLanguages();

		// Import simplepie library
		jimport('simplepie.simplepie');

		// We need DomDocument to exist
		if (!class_exists('DomDocument')) {
			return false;
		}

		// Set the maximum execution time to a higher value
		@ini_set('max_execution_time', 720);

		// Get the feed params
		$params = EB::registry($feed->params);

		// Determines the limit of items to fetch
		$limit = $limit ? $limit : $params->get('feedamount', 0);

		// Trim extra spacing in url so that the connector can reach the target url correctly.
		$feedUrl = trim($feed->url);

		// Setup the outgoing connection to the feed source
		$connector = FH::connector($feedUrl);
		$connector->setUserAgent('Facebot');
		$contents = $connector->execute()->getResult();

		// If contents is empty, we know something failed
		if (!$contents) {
			return EB::exception(JText::sprintf('COM_EASYBLOG_FEEDS_UNABLE_TO_REACH_TARGET_URL', $feedUrl), EASYBLOG_MSG_ERROR);
		}

		// Get the cleaner to clean things up
		$cleaner = $this->getAdapter('Cleaner');
		$contents = $cleaner->cleanup($contents);

		// Load up the xml parser
		$parser = EB::simplepie();
		$parser->strip_htmltags(false);
		$parser->set_raw_data($contents);
		@$parser->init();

		// Get a list of items
		// We need to supress errors here because simplepie will throw errors on STRICT mode.
		$items = @$parser->get_items();

		if (!$items) {
			// TODO: Language string
			return EB::exception(JText::sprintf('COM_EASYBLOG_FEEDS_NOTHING_TO_BE_IMPORTED_CURRENTLY', $feedUrl), EASYBLOG_MSG_ERROR);
		}

		// Get the feeds model
		$model = EB::model('Feeds');

		// Determines the total number of items migrated
		$total = 0;

		foreach ($items as $item) {

			// If it reaches limit, skip processing
			if ($limit && $total == $limit) {
				break;
			}

			// Get the item's unique id
			$uid = @$item->get_id();

			// Remove http:// or https://
			$uid = str_ireplace(array('http://', 'https://'), '', $uid);

			// If item already exists, skip this
			if ($model->isFeedItemImported($feed->id, $uid)) {
				continue;
			}

			// Log down a new history record to avoid fetching of the same item again
			$history = EB::table('FeedHistory');
			$history->feed_id = $feed->id;
			$history->uid = $uid;
			$history->created = EB::date()->toSql();

			$feedParams = new JRegistry();
			$feedParams->set('permalink', $item->get_id());
			$history->params = $feedParams->toString();

			$history->store();

			// Load up the post library
			$post = EB::post();

			$createOption = array(
								'overrideDoctType' => 'legacy',
								'checkAcl' => false,
								'overrideAuthorId' => $feed->item_creator,
								'fromFeedImport' => true
							);

			$post->create($createOption);

			// Pass this to the adapter to map the items
			$mapper = $this->getAdapter('Mapper');
			$mapper->map($post, $item, $feed, $params);

			// Now we need to get the content of the blog post
			$mapper->mapContent($post, $item, $feed, $params);

			$saveOptions = array(
							'applyDateOffset' => false,
							'validateData' => false,
							'useAuthorAsRevisionOwner' => true,
							'checkAcl' => false,
							'overrideAuthorId' => $feed->item_creator,
							'processAutopost' => $params->get('autopost', 0),
							'skipNotifications' => !$params->get('notify', true),
							'fromFeedImport' => true
							);

			// Try to save the blog post now
			try {
				$post->save($saveOptions);

				// Update the history table
				$history->post_id = $post->id;
				$history->store();

				$total++;

			} catch(EasyBlogException $exception) {
				// do nothing.
			}
		}

		return EB::exception(JText::sprintf('COM_EASYBLOG_FEEDS_POSTS_MIGRATED_FROM_FEED', $total, $feedUrl), EASYBLOG_MSG_SUCCESS);
	}

	/**
	 * Executes during cron to import items from feeds
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function cron()
	{
		// Get default site language
		$langParams = JComponentHelper::getParams('com_languages');
		$defaultLang = $langParams->get('site');

		// Load the language
		$lang = JFactory::getLanguage();
		$lang->load('com_easyblog', JPATH_ROOT, $defaultLang);

		if (!class_exists('DomDocument')) {
			return EB::exception('COM_EASYBLOG_FEEDS_DOMDOCUMENT_MISSING', EASYBLOG_MSG_ERROR);
		}

		// Get the feeds model
		$model = EB::model('Feeds');

		$debug = $this->input->get('debug', false, 'bool');

		// fetch rss feed limit
		$limit = (int) $this->config->get('main_rss_cronlimit', 5);

		// Get a list of pending feeds
		$feeds = $model->getPendingFeeds($limit, $debug);

		if (!$feeds) {
			return EB::exception('COM_EASYBLOG_FEEDS_NO_FEEDS_TO_IMPORT', EASYBLOG_MSG_INFO);
		}

		// Determines the total number of feeds imported
		$total = 0;
		$results = array();

		// Import them now
		foreach ($feeds as $feed) {

			// Update the flag first so that if another cron service is executed, it will not overlap
			$feed->flag = 0;
			$feed->last_import = EB::date()->toSql();
			$feed->store();

			// Import them now
			$results[] = $this->import($feed);
		}

		return $results;
	}

}
