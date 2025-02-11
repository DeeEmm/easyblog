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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelLanguages extends EasyBlogAdminModel
{
	private $data = null;
	private $pagination = null;
	private $total = null;

	public function __construct()
	{
		parent::__construct();

		// Get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easyblog.languages.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Purges non installed languages
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		$db = EB::db();
		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_languages');
		$query .= ' WHERE ' . $db->quoteName('state') . ' = ' . EBLOG_LANGUAGES_NOT_INSTALLED;

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Retrieve languages
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getLanguages()
	{
		if (!$this->data) {
			$db 	= EB::db();

			$query	= $this->_buildQuery();

			$db->setQuery($query);
			$this->data = $db->loadObjectList();
		}

		return $this->data;
	}

	/**
	 * Discover new languages
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function discover()
	{
		$config = EB::config();
		$key = $config->get('main_apikey');

		$connector = FH::connector(EBLOG_LANGUAGES_SERVER);
		$connector->addQuery('key', $key);
		$connector->setMethod('POST');
		$contents = $connector->execute()->getResult();

		if (!$contents) {
			$result = new stdClass();
			$result->message = 'No language found';
			return $result;
		}

		// Decode the result
		$result	= json_decode($contents);

		if ($result->code != 200) {

			$return = base64_encode('index.php?option=com_easyblog&view=languages');
			$msg = isset($result->error) ? $result->error : $result->message;
			
			return $msg;
		}

		foreach ($result->languages as $language) {

			// If it does, load it instead of overwriting it.
			$table = EB::table('Language');
			$exists = $table->load(array('locale' => $language->locale));

			// We do not want to bind the id
			unset($language->id);

			// Since this is the retrieval, the state should always be disabled
			if (!$exists) {
				$table->state = EBLOG_LANGUAGES_NOT_INSTALLED;
			}

			// Then check if the language needs to be updated. If it does, update the ->state to EBLOG_LANGUAGES_NEEDS_UPDATING
			// We need to check if the language updated time is greater than the local updated time
			if ($exists && $table->state == EBLOG_LANGUAGES_INSTALLED) {
				$languageTime = strtotime($language->updated);
				$localLanguageTime = strtotime($table->updated);

				if ($languageTime > $localLanguageTime && $table->state == EBLOG_LANGUAGES_INSTALLED) {
					$table->state = EBLOG_LANGUAGES_NEEDS_UPDATING;
				}
			}

			// Set the title
			$table->title = $language->title;

			// Set the locale
			$table->locale = $language->locale;

			// Set the translator
			$table->translator = $language->translator;

			// Set the updated time
			$table->updated = $language->updated;

			// Update the progress
			$table->progress = $language->progress;

			// Update the table with the appropriate params
			$params = new JRegistry();

			$params->set('download' , $language->download);
			$params->set('md5', $language->md5);
			$table->params = $params->toString();

			$table->store();
		}

		return true;
	}

	public function _buildQuery()
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_languages');
		return $query;
	}


	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = EB::Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Determines if the language rows has been populated
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initialized()
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_languages');
		$db->setQuery($query);

		$initialized = $db->loadResult() > 0;

		return $initialized;
	}
}
