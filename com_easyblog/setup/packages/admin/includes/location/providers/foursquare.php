<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogLocationProvidersFoursquare extends EasyBlogLocationProviders
{
	protected $queries = array(
								'query' => '',
								'client_id' => '',
								'client_secret' => '',
								'm' => 'foursquare',
								'radius' => 800,
								'v' => '20140905',
								'intent' => 'browse'
						);

	protected $url = 'https://api.foursquare.com/v2/venues/search';

	public function __construct()
	{
		parent::__construct();
		
		// Initialise the client_id and client_secret
		$this->client_id = $this->config->get('foursquare_client_id');
		$this->client_secret = $this->config->get('foursquare_client_secret');

		$this->setQuery('client_id', $this->client_id);
		$this->setQuery('client_secret', $this->client_secret);
	}

	/**
	 * Determines if the settings is complete
	 *
	 * @since   5.0
	 * @access  public
	 * @param   string
	 * @return  
	 */
	public function isSettingsComplete()
	{
		if ($this->config->get('location_service_provider') != 'foursquare') {
			return false;
		}

		if ($this->client_id && $this->client_secret) {
			return true;
		}

		return false;
	}

	public function setCoordinates($lat, $lng)
	{
		return $this->setQuery('ll', $lat . ',' . $lng);
	}

	public function setSearch($search = '')
	{
		return $this->setQuery('near', $search);
	}

	public function getResult($queries = array())
	{
		$this->setQueries($queries);

		$result = [];

		$connector = FH::connector($this->buildUrl());
		$result[] = $connector->execute()->getResult();

		if (!empty($this->queries['query'])) {
			$this->setQuery('intent', 'global');
			$connector = FH::connector($this->buildUrl());
			$result[] = $connector->execute()->getResult();
		}

		$venues = array();

		foreach ($result as $row) {
			$object = json_decode($row->contents);

			if (!isset($object->meta) || !isset($object->meta->code)) {
				$this->setError(JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_FOURSQUARE_UNKNOWN_ERROR'));

				return array();
			}

			if ($object->meta->code != 200) {
				$this->setError($object->meta->errorDetail);

				return array();
			}

			if (empty($object->response->venues)) {
				continue;
			}

			// We want to merge in the browse results and global results
			foreach ($object->response->venues as $venue) {
				if (!isset($venues[$venue->id])) {
					$obj = new EasyBlogLocationData;
					$obj->latitude = $venue->location->lat;
					$obj->longitude = $venue->location->lng;
					$obj->address = isset($venue->location->address) ? $venue->location->address : '';
					$obj->name = $venue->name;

					$venues[$venue->id] = $obj;
				}
			}
		}

		$venues = array_values($venues);

		return $venues;
	}
}
