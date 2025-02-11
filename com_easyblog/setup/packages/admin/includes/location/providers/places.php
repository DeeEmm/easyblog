<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogLocationProvidersPlaces extends EasyBlogLocationProviders
{
	protected $queries = array(
		'location' => '',
		'radius' => 800,
		'key' => '',
		'query' => '',
		'keyword' => ''
	);

	public function __construct()
	{
		parent::__construct();

		$this->key = $this->config->get('googleplaces_api_key');

		if (empty($this->key)) {
			return $this->setError(JText::_('COM_EASYBLOG_LOCATION_PROVIDERS_PLACES_MISSING_APIKEY'));
		}

		$this->setQuery('key', $this->key);
	}

	/**
	 * Determines if the settings is complete
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function isSettingsComplete()
	{
		if (!$this->config->get('location_service_provider') == 'maps') {
			return false;
		}

		if (!$this->key) {
			return false;
		}

		return true;
	}

	public function setCoordinates($lat, $lng)
	{
		return $this->setQuery('location', $lat . ',' . $lng);
	}

	public function setSearch($search = '')
	{
		$this->setQuery('keyword', $search);
		$this->setQuery('query', $search);

		return $this;
	}

	public function getResult($queries = array())
	{
		$this->setQueries($queries);

		// There is 2 parts to this
		// nearbysearch
		// textsearch

		$nearbysearchUrl = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
		$textsearchUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

		$nearbysearchOptions = array(
			'location' => $this->queries['location'],
			'radius' => $this->queries['radius'],
			'key' => $this->queries['key'],
			'keyword' => $this->queries['keyword']
		);

		$textsearchOptions = array(
			'query' => $this->queries['query'],
			'key' => $this->queries['key']
		);

		$results = [];

		$connector = FH::connector($nearbysearchUrl . '?' . http_build_query($nearbysearchOptions));
		$connector->setReferer(JURI::root());
		$results[] = $connector->execute()->getResult();

		if (!empty($this->queries['query'])) {
			$connector = FH::connector($textsearchUrl . '?' . http_build_query($textsearchOptions));
			$connector->setReferer(JURI::root());
			$results[] = $connector->execute()->getResult();
		}

		$venues = [];

		foreach ($results as $result) {

			$obj = json_decode($result);

			if (!$obj->results) {
				continue;
			}

			foreach ($obj->results as $row) {
				$obj = new EasyBlogLocationData;
				$obj->latitude = $row->geometry->location->lat;
				$obj->longitude = $row->geometry->location->lng;
				$obj->name = $row->name;
				$obj->address = isset($row->formatted_address) ? $row->formatted_address : $row->name;

				$venues[$row->place_id] = $obj;
			}
		}

		$venues = array_values($venues);

		return $venues;
	}
}
