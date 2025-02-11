<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogGdprPost extends EasyBlog
{
	public $userId = null;
	public $type = null;
	public $params = null;

	public function __construct($id, $params)
	{
		$this->userId = $id;
		$this->type = 'post';
		$this->params = $params;
	}

	/**
	 * Event trigger to process user post data for GDPR download on EasySocial
	 *
	 * @since 5.2.5
	 * @access public
	 */
	public function onEasySocialGdprExport(SocialGdprSection &$section, SocialGdprItem $adapter)
	{
		// manually set type here.
		$adapter->type = $section->key . '_' . $this->type;

		// create tab in section
		$adapter->tab = $section->createTab($adapter);

		// get data.
		$defaultLimit = 15;

		$ids = $adapter->tab->getProcessedIds();
		$limit = $this->getParams('limit', $defaultLimit);

		$model = EB::model('blog');
		$items = $model->getUserPosts($this->userId, array('type' => 'all', 'exclude' => $ids, 'limit' => $limit));

		if (!$items) {
			$adapter->tab->finalize();
			return true;
		}

		foreach ($items as $row) {

			$post = EB::post($row->id);

			$item = $adapter->getTemplate($post->id, $adapter->type);

			$item->created = $post->created;
			$item->title =  $post->getTitle();
			$item->intro = $post->getIntro();
			$item->view = true;

			$content = $post->getContent(EASYBLOG_VIEW_ENTRY);

			$media = array();
			$postMedia = $post->getMedia();

			if ($postMedia) {

				foreach ($postMedia as $type => $value) {

					if ($type == 'galleries') {
						continue;
					}

					if (!empty($type)) {
						foreach ($value as $mm) {

							if (!$mm->url) {
								continue;
							}

							// Ensure that the media is hosted directly from the site
							if (strpos($mm->url, '//') !== false || strpos($mm->url, 'http://') !== false || strpos($mm->url, 'https://') !== false) {

								$uri = JURI::getInstance();
								$domain	= $uri->toString(array('host'));

								// The media is belong to somewhere else
								if (strpos($mm->url, $domain) === false) {
									continue;
								}
							}

							// Retrieve the relative url
							$url = $this->getRelativeUrl($mm);
							$url = '/' . ltrim($url, '/');

							// Replace the url from the content with the new one
							$content = str_replace($mm->url, '{%MEDIA%}', $content);

							$media[] = 'joomla:'. $url;
						}
					}
				}
			}

			$item->content = $content;
			$item->source = $media;

			$adapter->tab->addItem($item);
		}

		return true;
	}


	/**
	 * Main function to process user post data for GDPR download.
	 *
	 * @since 5.2
	 * @access public
	 */
	public function execute()
	{
		// Process small number of posts and media at a time to avoid timeout
		$defaultLimit = 15;

		$limitstart = $this->getParams('limitstart', 0);
		$limit = $this->getParams('limit', $defaultLimit);

		$this->setParams('status', false);

		$model = EB::model('blog');
		$items = $model->getUserPosts($this->userId, array('type' => 'all', 'limitstart' => $limitstart, 'limit' => $limit));

		// Get existings data
		$data = $this->getParams('data', array());
		$media = array();

		if ($data) {
			$data = json_decode($data);
		}

		if ($items) {
			foreach ($items as $item) {

				$post = EB::post($item->id);

				$obj = new EasyBlogGdprTemplate();

				$obj->id = $post->id;
				$obj->type = $this->type;
				$obj->preview = $post->getTitle();
				$obj->link = 'posts/' . $post->id . '.html';

				$content = $post->getContent(EASYBLOG_VIEW_ENTRY);

				$obj->created = $post->getCreationDate()->format('Y-m-d');

				$postMedia = $post->getMedia();

				foreach ($postMedia as $type => $value) {

					if ($type == 'galleries') {
						continue;
					}

					if (!empty($type)) {
						foreach ($value as $item) {

							if (!$item->url) {
								continue;
							}

							// Ensure that the media is hosted directly from the site
							if (strpos($item->url, '//') !== false || strpos($item->url, 'http://') !== false || strpos($item->url, 'https://') !== false) {

								$uri = JURI::getInstance();
								$domain	= $uri->toString(array('host'));

								// The media is belong to somewhere else
								if (strpos($item->url, $domain) === false) {
									continue;
								}
							}

							// Retrieve the relative url
							$url = $this->getRelativeUrl($item);

							$newUrl = 'media/' . $url;

							// Replace the url from the content with the new one
							$content = str_replace($item->url, $newUrl, $content);

							$item->url = $url;
							$item->postId = $post->id;

							$media[$type][] = $item;
						}
					}
				}

				$obj->content = $content;

				$data[] = $obj;
			}

			$this->setParams('data', json_encode($data));

		} else {
			// Mark the process as complete
			$this->setParams('status', true);
		}

		// Process the media after all iterations is completed
		$media = $this->processMedia($media);

		// Increase limitstart for next processing
		$this->setParams('limitstart', $limitstart + $limit);
		$this->setParams('media', json_encode($media));

		return array('post' => $data, 'media' => $media);
	}

	/**
	 * Process the media data
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function processMedia($media)
	{
		$data = $this->getParams('media', array());

		if ($data) {
			$data = json_decode($data);
		}

		if ($media) {
			foreach ($media as $type => $value) {

				foreach ($value as $item) {
					$obj = new EasyBlogGdprTemplate();

					$url = $item->url;
					$path = JPATH_ROOT . '/' . $url;

					// Get the filename
					$segments = explode('/', $url);
					$filename = array_pop($segments);

					$obj->id = $item->postId;
					$obj->type = $type;
					$obj->preview = $filename;
					$obj->link = 'media/' . $url;
					$obj->media = $path;

					$data[] = $obj;
				}
			}
		}

		return $data;
	}

	/**
	 * Retrieve relative path of the media object
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getRelativeUrl($item)
	{
		if (isset($item->uri)) {
			$mm = EB::mediaManager();
			$url = $mm->getUrl($item->uri, true);
		} else {
			$url = $item->url;

			if (strpos($url, '//') !== false) {

				$uri = JURI::getInstance();
				$domain	= $uri->toString(array('host'));

				$url = str_replace('//', '', $url);

				if (strpos($url, $domain) !== false) {
					$url = str_replace($domain, '', $url);
					$url = ltrim($url, '/');
				}
			}
		}

		return $url;
	}

	/**
	 * Get the params
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getParams($name, $default = false)
	{
		$name = $this->type . '.' . $name;

		return $this->params->get($name, $default);
	}

	/**
	 * Set the params
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function setParams($name, $value)
	{
		$name = $this->type . '.' . $name;

		return $this->params->set($name, $value);
	}
}
