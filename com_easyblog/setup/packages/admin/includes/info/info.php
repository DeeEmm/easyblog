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

class EasyBlogInfo
{
	/**
	 * Gets the namespace
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getNamespace()
	{
		// Determines if we are on admin view
		$path = EB::isFromAdmin() ? 'admin' : 'site';
		$namespace = EBLOG_SESSION_NAMESPACE . '.' . $path;

		return $namespace;
	}

	/**
	 * Sets a message in the queue.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function set($message = '' , $class = '' )
	{
		$session = JFactory::getSession();

		if (empty($message) && empty($class)) {
			return;
		}

		// Backward compatibility for error class
		if ($class == EASYBLOG_MSG_ERROR) {
			$class = 'danger';
		}

		$obj = new stdClass();

		if ($message instanceof EasyBlogException) {
			$obj = (object) $message->toArray();
		} else {
			$obj->message = JText::_($message);
			$obj->type = $class;
		}

		$data = serialize( $obj );

		$messages = $session->get('messages', array() , $this->getNamespace());
		$messages[]	= $data;

		$session->set('messages' , $messages , $this->getNamespace());
	}

	/**
	 * Gets messages from the info queue
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMessage($clear = true)
	{
		$session = JFactory::getSession();
		$messages = $session->get('messages', array() , $this->getNamespace());

		if ($clear) {
			$session->clear('messages' , $this->getNamespace());
		}

		if ($messages) {

			// We only return a single object at a time
			$obj = new stdClass();

			foreach ($messages as $message) {
				$data = unserialize($message);

				$obj->text = JText::_($data->message);
				$obj->type = $data->type;
			}

			return $obj;
		}

		return false;
	}

	/**
	 * Generates a message in html.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function html()
	{
		$output = '';
		$message = $this->getMessage();

		// If there's nothing stored in the session, ignore this.
		if (!$message) {
			return;
		}

		$theme = EB::themes();
		$theme->set('message', $message);
		return $theme->output('site/info/default');
	}
}
