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

class EasyBlogGroups
{
	public function useGroups()
	{
		return $this->isEnabled();
	}

	public function isEnabled()
	{
		// Since we only support jomsocial now, load up their form
		if (JPluginHelper::isEnabled('system', 'groupeasyblog') && $this->testExists('jomsocial')) {
			return true;
		}

		return false;
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

	public function getGroupSourceType()
	{
		$source = 'jomsocial';

		return $source;
	}

	public function addCommentStream( $blog , $comment , $external )
	{
		return $this->addCommentStreamJomsocial( $blog , $comment , $external );
	}

	/**
	 * Creates a stream item for the respective 3rd party plugin
	 *
	 * @param	TableBlog $blog
	 */
	public function addStream( $blog , $isNew , $key , $source )
	{
		// Since we only support jomsocial now, load up their form
		return $this->addStreamJomsocial( $blog , $isNew , $key , $source );
	}

	private function addCommentStreamJomsocial($blog, $comment, $external)
	{
		$jsCoreFile	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= EB::config();

		if (!JFile::exists($jsCoreFile)) {
			return false;
		}

		// We do not want to add activities if new blog activity is disabled.
		if (!$config->get('integrations_jomsocial_comment_new_activity')) {
			return false;
		}

		require_once($jsCoreFile);

		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

		JTable::addIncludePath(JPATH_ROOT . '/components/com_community/tables');

		$group = JTable::getInstance('Group', 'CTable');
		$group->load($external->id);

		$command = 'easyblog.comment.add';

		$blogTitle = EBString::substr($blog->title, 0, 30) . '...';
		$blogLink = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $comment->post_id, false, true);

		$content = '';

		if ($config->get('integrations_jomsocial_submit_content')) {
			$content = $comment->comment;
			$content = EB::comment()->parseBBCode($content);
			$content = nl2br($content);
			$content = strip_tags($content);
			$content = EBString::substr($content, 0, $config->get('integrations_jomsocial_comments_length'));
		}

		$obj = new stdClass();
		$obj->title = JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_COMMENT_ADDED', $blogLink, $blogTitle);
		$obj->content = ($config->get('integrations_jomsocial_submit_content')) ? $content : '';
		$obj->cmd = $command;
		$obj->actor = $comment->created_by;
		$obj->target = 0;
		$obj->app = 'easyblog';
		$obj->cid = $comment->id;
		$obj->group_access = $group->approvals;
		$obj->groupid = $group->id;

		if ($config->get('integrations_jomsocial_activity_likes')) {
			$obj->like_id = $comment->id;
			$obj->like_type = 'com_easyblog.comments';
		}

		if ($config->get('integrations_jomsocial_activity_comments')) {
			$obj->comment_id = $comment->id;
			$obj->comment_type = 'com_easyblog.comments';
		}

		// add JomSocial activities
		CFactory::load ('libraries', 'activities');
		CActivityStream::add($obj);
	}

	public function getGroupContribution( $postId, $sourcetype = 'jomsocial', $type = 'id')
	{
		$db = EB::db();

		$externalTblName    = '';


		if( $sourcetype == 'jomsocial' )
		{
			$externalTblName    = '#__community_groups';
		}

		$query  = '';

		if( $type == 'name' || $type == 'title' )
		{
			$query  = 'SELECT b.`name` FROM `#__easyblog_post` as a ';
			$query  .= ' INNER JOIN ' . $db->NameQuote( $externalTblName ) . ' as b ON a.`source_id` = b.`id`';
		}
		else
		{
			$query  = 'SELECT a.`source_id` as `group_id` FROM `#__easyblog_post` as a';
		}

		$query  .= ' WHERE a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP);
		$query  .= ' AND a.`id` = ' . $db->Quote($postId);


		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}
}
