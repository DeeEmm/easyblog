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

require_once(JPATH_ROOT . '/libraries/foundry/vendor/nahid/jsonq/src/JsonQueriable.php');
require_once(EBLOG_ROOT . '/views/views.php');

use Nahid\JsonQ\JsonQueriable;

class EasyBlogViewLottie extends EasyBlogView
{
	use JsonQueriable;

	/**
	 * Retrieve the Lottie Player when a Lottie JSON file is uploaded
	 *
	 * @since   6.0.0
	 * @access  public
	 */
	public function getPlayer()
	{
		$postId = $this->input->get('postId', 0 ,'int');
		$post = EB::post($postId);

		// Ensure that the users really have access to the post
		if (!$post->id || ($post->isNew() && !$post->canCreate()) || (!$post->isNew() && !$post->canEdit())) {
			return $this->ajax->reject();
		}

		$author = $post->getAuthor();

		// Ensure that he/she is the owner of the post
		if ($author->id !== (int) $this->my->id) {
			return $this->ajax->reject();
		}

		$file = $this->input->files->get('lottie', '');
		$loop = $this->input->get('loop', 0 ,'int');
		$autoplay = $this->input->get('autoplay', 0 ,'int');
		$hover = $this->input->get('hover', 0 ,'int');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			return $this->ajax->reject();
		}

		$content = file_get_contents($file['tmp_name']);

		// Ensure that this is really an JSON file
		if (JFile::getExt($file['name']) !== 'json' || $file['type'] !== 'application/json' || !$this->isJson($content)) {
			return $this->ajax->reject();
		}

		$tempPath = $file['tmp_name'];
		$newPath = '/images/easyblog_lottie/' . $post->id . '/' . $file['name'];

		// Ensure that there is no same file being uploaded
		if (!JFile::exists($newPath)) {
			// Upload the JSON file
			JFile::upload($tempPath, JPATH_ROOT . $newPath);
		}

		$result = new stdClass();
		$result->url = $newPath;

		$themes = EB::themes();
		$themes->set('url', $result->url);
		$themes->set('loop', $loop);
		$themes->set('autoplay', $autoplay);
		$themes->set('hover', $hover);
		$themes->set('isEdit', false);

		$html = $themes->output('site/blocks/lottie');
		$result->html = $html;
		$result->name = $file['name'];

		return $this->ajax->resolve($result);
	}
}
