<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogEvent
{
	public function useEvents()
	{
		return $this->isEnabled();
	}


	public function isEnabled()
	{
		// TODO: Some checking required here
		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'eventeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns the group form html
	 */
	public function getFormHTML( $uid = '0' , $blogSource = '')
	{
		$contents	= '';

		// TODO: Check whether to load groupjive,jomsocial or any other group collaboration tools here.

		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'eventeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			$contents	= $this->jomsocialForm( $uid , $blogSource );
		}

		return $contents;
	}

	public function testExists( $source )
	{
		switch( $source )
		{
			case 'jomsocial':
				return JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php' );
			break;
		}
	}

	public function getSourceType()
	{
		$source = 'jomsocial';

		return $source;
	}

	private function jomsocialForm( $uid = '0' , $blogSource )
	{
		$my		= JFactory::getUser();
		$file	= JPATH_ROOT . '/components/com_community/libraries/core.php';


		if (!JFile::exists($file)) {
			return false;
		}

		require_once( $file );
		$model	= CFactory::getModel( 'Events' );

		$rows	= $model->getEvents( null , $my->id , null , null , false , false , null , null , CEventHelper::ALL_TYPES , 0 , 999999 );
		$events	= array();

		JTable::addIncludePath( JPATH_ROOT . '/components/com_community/tables' );
		foreach ($rows as $row) {
			$event 		= JTable::getInstance( 'Event' , 'CTable' );
			$event->load( $row->id );

			$data			= new stdClass();
			$data->id		= $event->id;
			$data->title	= $event->title;
			$data->avatar	= $event->getAvatar();

			$events[]		= $data;
		}

		$theme = EB::themes();
		$theme->set( 'blogSource' , $blogSource );
		$theme->set( 'external'		, $uid );
		$theme->set( 'selectedEvent', $uid );
		$theme->set( 'events'	, $events );

		return $theme->output('site/integrations/jomsocial/events');

	}

	/**
	 * Triggered to add activity stream for events on jomsocial
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function addCommentStream($blog, $comment, $external)
	{
		return $this->addCommentStreamJomsocial($blog, $comment, $external);
	}

	/**
	 * Creates a stream item for the respective 3rd party plugin
	 *
	 * @param	TableBlog $blog
	 */
	public function addStream($blog, $isNew, $key, $source)
	{
		return $this->addStreamJomsocial($blog, $isNew, $key, $source);
	}

	private function addCommentStreamJomsocial( $blog , $comment , $external )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EB::config();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integrations_jomsocial_comment_new_activity' ) )
		{
			return false;
		}

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}

		require_once( $jsCoreFile );

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		$event				= JTable::getInstance( 'Group' , 'CTable' );
		$event->load( $external->uid );

		$config				= EB::config();
		$command			= 'easyblog.comment.add';

		$blogTitle			= EBString::substr( $blog->title , 0 , 30 ) . '...';
		$blogLink			= EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $comment->post_id, false, true);

		$content        = '';
		if($config->get('integrations_jomsocial_submit_content'))
		{
			$content		= $comment->comment;
			$content		= EasyBlogCommentHelper::parseBBCode( $content );
			$content		= nl2br( $content );
			$content		= strip_tags( $content );
			$content		= EBString::substr( $content, 0 , $config->get( 'integrations_jomsocial_comments_length' ) );
		}

		$obj			= new stdClass();
		$obj->title		= JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_COMMENT_ADDED' , $blogLink , $blogTitle );
		$obj->content	= ($config->get('integrations_jomsocial_submit_content')) ? $content : '';
		$obj->cmd 		= $command;
		$obj->actor   	= $comment->created_by;
		$obj->target  	= 0;
		$obj->app		= 'easyblog';
		$obj->cid		= $comment->id;
		$obj->eventid	= $event->id;

		if( $config->get( 'integrations_jomsocial_activity_likes' ) )
		{
			$obj->like_id   = $comment->id;
			$obj->like_type = 'com_easyblog.comments';
		}

		if( $config->get( 'integrations_jomsocial_activity_comments' ) )
		{
			$obj->comment_id    = $comment->id;
			$obj->comment_type  = 'com_easyblog.comments';
		}
		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function getContribution( $postId, $sourcetype = 'jomsocial', $type = 'id')
	{
		$db		= EB::db();

		$externalTblName    = '';

		if( $sourcetype == 'jomsocial' )
		{
			$externalTblName    = '#__community_events';
		}

		$query  = '';
		if( $type == 'name' || $type == 'title' )
		{
			$query  = 'SELECT b.`name` FROM `#__easyblog_post` as a ';
			$query  .= ' INNER JOIN ' . $db->NameQuote( $externalTblName ) . ' as b ON a.`source_id` = b.`id`';
		}
		else
		{
			$query  = 'SELECT a.`source_id` as `event_id` FROM `#__easyblog_post` as a';
		}

		$query  .= ' WHERE a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT);
		$query  .= ' AND a.`id` = ' . $db->Quote($postId);


		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}
}
