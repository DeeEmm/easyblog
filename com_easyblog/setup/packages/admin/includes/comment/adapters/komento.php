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

require_once(__DIR__ . '/base.php');

class EasyBlogCommentKomento extends EasyBlogCommentBase
{
	/**
	 * Determines if Komento exists on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_komento/bootstrap.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Renders the comment form 
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html(EasyBlogPost &$blog)
	{
		if (!$this->exists()) {
			return;
		}

		if (isset($blog->komentodisable) && $blog->komentodisable) {
			
			$blog->text .= '{KomentoDisable}';
		}

		if (isset($blog->komentoenable) && $blog->komentoenable) {
			$blog->text .= '{KomentoEnable}';
		}

		if (isset($blog->komentolock) && $blog->komentolock) {
			$blog->text .= '{KomentoLock}';
		}

		$options = array('trigger'=>'onDisplayComments');

		if (!$blog->allowComments()) {
			$options['lock'] = 1;
		}

		$output = Komento::commentify('com_easyblog', $blog, $options);

		return $output;
	}

	/**
	 * Renders the comment count for Komento
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCount(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return;
		}

		$model = Komento::getModel('Comments');
		$count = $model->getCount('com_easyblog', $post->id);

		return $count;
	}

	/**
	 * Retrieves comments for preview purpose
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPreviewComments($postId, $limit)
	{
		$config	= Komento::getConfig('com_easyblog'); 
		
		if (!$this->exists() || !$config->get('layout_frontpage_preview')) {
			return;
		}

		$options = array('limit' => $limit);
		$model = Komento::getModel('Comments');
		$comments = $model->getComments('com_easyblog', $postId);

		// Komento 3
		if (class_exists('KT')) {
			$comments = KT::formatter('comment', $comments);
		}

		return $comments;
	}

	public function cleanup(EasyBlogPost &$post)
	{
		$post->komentodisable = false;
		$post->komentoenable = false;
		$post->komentolock = false;

		if (isset($post->text)) {
			$post->text = $this->stripParameters($post, $post->text);
		}

		$post->intro = $this->stripParameters($post, $post->intro);
		$post->content = $this->stripParameters($post, $post->content);
	}


	public function stripParameters(EasyBlogPost &$post, $content)
	{
		if (EBString::strpos($content, '{KomentoDisable}') !== false) {
			$post->komentodisable = true;
			$content = EBString::str_ireplace('{KomentoDisable}', '', $content);
		}

		if (EBString::strpos($content, '{KomentoEnable}') !== false) {
			$post->komentoenable = true;
			$content = EBString::str_ireplace('{KomentoEnable}', '', $content);
		}

		if (EBString::strpos($content, '{KomentoLock}') !== false) {
			$post->komentolock = true;
			$content = EBString::str_ireplace('{KomentoLock}', '', $content);
		}

		return $content;
	}
}
