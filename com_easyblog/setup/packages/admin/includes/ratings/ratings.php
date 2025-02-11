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

class EasyBlogRatings extends EasyBlog
{
	/**
	 * New method to retrieve ratings form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html(EasyBlogPost $post, $elementId, $text = '', $disabled = false)
	{
		// If ratings has been disabled, do not proceed here.
		if (!$this->config->get('main_ratings')) {
			return false;
		}

		// Generate the hash for the current user
		$hash = !$this->my->guest ? '' : JFactory::getSession()->getId();

		// Determines if the current user has already voted
		$voted = $post->hasVoted($this->my->id);

		$locked = false;
		
		if (($voted && !$this->config->get('main_ratings_revote')) || ($this->my->guest && !$this->config->get('main_ratings_guests') || $disabled)) {
			$locked = true;
		}

		if ($this->my->id == $post->getAuthor()->id && !$this->config->get('main_ratings_allow_author')) {
			$locked = true;
		}

		// Get the rating value for the post
		$value = $post->getRatings();

		// Only display ratings on entry view
		$entry = $this->input->get('view', '', 'cmd') == 'entry' ? true : false;

		$lockedMessage = 'COM_EB_NOT_ALLOWED_TO_RATE_POST';

		if ($voted) {
			$lockedMessage = 'COM_EASYBLOG_RATINGS_ALREADY_RATED';
		}

		$template = EB::themes();
		$template->set('lockedMessage', $lockedMessage);
		$template->set('entry', $entry);
		$template->set('voted', $voted);
		$template->set('elementId', $elementId);
		$template->set('rating', $value->ratings);
		$template->set('total', $value->total);
		$template->set('locked', $locked);
		$template->set('text', $text);
		$template->set('uid', $post->id);
		$template->set('type', EASYBLOG_RATINGS_ENTRY);

		return $template->output('site/ratings/form');
	}
}