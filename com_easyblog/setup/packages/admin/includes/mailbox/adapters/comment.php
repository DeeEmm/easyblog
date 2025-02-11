<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\DateHelper;

class EasyBlogMailboxAdapterComment extends JObject
{
	public function execute()
	{
		/*
		 * Check enabled
		 */
		$config	= EB::config();
		$debug = $this->input->getBool('debug', false);
		$acl = EB::acl();

		if(!$config->get( 'main_comment_email' ) )
		{
			return;
		}

		$interval = (int) $config->get('main_remotepublishing_mailbox_run_interval');
		$nextrun = (int) $config->get('main_remotepublishing_mailbox_next_run');
		$nextrun = EB::date($nextrun)->toUnix();
		$timenow = EB::date()->toUnix();

		if ($nextrun !== 0 && $timenow < $nextrun) {

			if (!$debug) {
				echo 'time now: ' . EB::date($timenow)->toSql() . "<br />\n";
				echo 'next email run: ' . EB::date($nextrun)->toSql() . "<br />\n";
				return;
			}
		}

		$newnextrun	= EB::date('+ ' . $interval . ' minutes', DateHelper::getOffset())->toUnix();

		// use $configTable to avoid variable name conflict
		$configTable		= EB::table('configs');
		$configTable->load('config');
		$parameters = new JParameter($configTable->params);

		$parameters->set( 'main_remotepublishing_mailbox_next_run' , $newnextrun );
		$configTable->params = $parameters->toString('ini');

		$configTable->store();

		/*
		 * Connect to mailbox
		 */
		require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'classes'.DS.'mailbox.php');
		$mailbox		= new EasyblogMailbox();
		if (!$mailbox->connect())
		{
			$mailbox->disconnect();
			echo 'Comment Mailbox: Could not connect to mailbox.';
			return false;
		}

		$total 	= 0;

		/*
		 * Get data from mailbox
		 */
		$total_mails	= $mailbox->getMessageCount();

		if ($total_mails < 1)
		{
			// No mails in mailbox
			$mailbox->disconnect();
			echo 'Comment Mailbox: No emails found.';
			return false;
		}

		// Let's get the correct mails
		$messages 	= $mailbox->searchMessages( 'UNSEEN' );

		if( $messages )
		{
			$prefix 	= '/\[\#(.*)\]/is';
			$filter		= JFilterInput::getInstance();
			$db			= EB::db();

			foreach( $messages as $messageSequence )
			{
				$info 		= $mailbox->getMessageInfo( $messageSequence );
				$from		= $info->fromemail;
				$senderName	= $info->from[0]->personal;
				$subject	= $filter->clean( $info->subject );

				// @rule: Detect if this is actually a reply.
				preg_match( '/\[\#(.*)\]/is' , $subject , $matches );

				// If the title doesn't match the comment specific title, just continue the block.
				if( empty( $matches ) )
				{
					continue;
				}



				$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'email' ) . '=' . $db->Quote( $from );
				$db->setQuery( $query );

				$userId 	= $db->loadResult();

				$commentId 	= $matches[1];

				$refComment 	= EB::table('Comment');
				$refComment->load( $commentId );

				// Get the message contents.
				$message	= new EasyblogMailboxMessage($mailbox->stream, $messageSequence);
				$message->getMessage();
				$content	= $message->getPlain();

				// If guest commenting is not allowed, and user's email does not exist in system, pass this.
				if (!$acl->get('allow_comment') && !$userId) {
					continue;
				}


				// Apply akismet filtering
				if ($config->get('comment_akismet')) {
					$data = array(
							'author'    => $senderName,
							'email'     => $from,
							'website'   => JURI::root() ,
							'body'      => $content,
							'permalink' => EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $refComment->post_id )
						);

					if (EB::akismet()->isSpam($data)) {
						continue;
					}
				}

				$model 	= EB::model( 'Comment' );

				$comment 		= EB::table('Comment');
				$comment->name 	= $senderName;
				$comment->email = $from;
				$comment->comment 	= $content;
				$comment->post_id 	= $refComment->post_id;

				$date 	= EB::date();
				$comment->created 	= $date->toSql();
				$comment->modified	= $date->toSql();
				$comment->published	= 1;

				if( $userId )
				{
					$comment->created_by 	= $userId;
				}

				$comment->sent 		= 0;

				$isModerated		= false;
				// Update publish status if the comment requires moderation
				if( ($config->get( 'comment_moderatecomment') == 1) || ( !$userId && $config->get( 'comment_moderateguestcomment') == 1) )
				{
					$comment->set( 'published' , EBLOG_COMMENT_STATUS_MODERATED );
					$isModerated	= true;
				}

				$post = EB::post($comment->post_id);

				// If moderation for author is disabled, ensure that the comment is published.
				// If the author is the owner of the blog, it should never be moderated.
				if (!$config->get( 'comment_moderateauthorcomment' ) && $post->created_by == $userId) {
					$comment->set( 'published' , 1 );
					$isModerated	= false;
				}

				if (!$comment->store()) {
					echo 'Error storing comment: ' . $comment->getError();
					return;
				}

				// @rule: Process notifications
				$comment->processEmails($isModerated, $post);

				// Update the sent flag
				$comment->updateSent();

				$total++;
			}
		}

		/*
		 * Disconnect from mailbox
		 */
		$mailbox->disconnect();

		/*
		 * Generate report
		 */
		echo JText::sprintf( 'Comment Mailbox: %1s comments fetched from mailbox: ' . $config->get( 'main_remotepublishing_mailbox_remotesystemname' ) . '.' , $total );
	}

}
