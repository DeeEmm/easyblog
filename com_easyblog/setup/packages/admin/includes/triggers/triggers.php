<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogTriggers extends EasyBlog
{
	public $events = array(
							'easyblog.prepareContent'   => 'onEasyBlogPrepareContent',
							'easyblog.beforeSave'       => 'onBeforeEasyBlogSave',
							'easyblog.commentCount'     => 'onGetCommentCount',
							'prepareContent'            => 'onContentPrepare',
							'afterDisplayTitle'         => 'onContentAfterTitle',
							'beforeDisplayContent'      => 'onContentBeforeDisplay',
							'afterDisplayContent'       => 'onContentAfterDisplay',
							'beforeSave'                => 'onContentBeforeSave'
						);

	public function trigger($event, &$row)
	{
		$params = EB::registry();
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$dispatcher = EB::dispatcher();

		// Need to make this behave like how Joomla category behaves.
		if (!isset($row->catid)) {
			$row->catid = $row->category_id;
		}

		if (!isset($this->events[$event])) {
			return false;
		}

		if (FH::isJoomla4()) {
			$result = $this->app->triggerEvent($this->events[$event], array('easyblog.blog', &$row, &$params, $limitstart));
		}

		if (!FH::isJoomla4()) {
			$result = $dispatcher->trigger($this->events[$event], array('easyblog.blog', &$row, &$params, $limitstart));
		}


		// Remove unwanted fields.
		unset($row->catid);

		return $result;
	}
}
