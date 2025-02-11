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

require_once(__DIR__ . '/types/standard.php');

class EasyBlogFormatter extends EasyBlog
{
	private $type = null;
	private $items = null;
	private $cache = null;
	private $options = null;

	public function __construct($type, $items, $cache = true, $options = [])
	{
		parent::__construct();

		$this->type = $type;
		$this->items = $items;
		$this->cache = $cache;
		$this->options = $options;
	}

	public function execute()
	{
		// If there's no items, skip this altogether
		if (empty($this->items)) {
			return $this->items;
		}

		$fileName = $this->type;

		// Fixing 3rd party calling this formatter. #2449
		$option = $this->input->get('option', '', 'string');

		if ($this->doc->getType() == 'json' && $option == 'com_easyblog') {
			$fileName = $fileName . '.json';
		}

		require_once(__DIR__ . '/types/' . $fileName . '.php');

		$class = 'EasyBlogFormatter' . ucfirst($this->type);

		$obj = new $class($this->items, $this->cache, $this->options);

		return $obj->execute();
	}
}
