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
?>
<?php if ($post->allowComments() && $post->canEdit() && !$post->allowcomment) { ?>
<div class="eb-comment-notice o-alert o-alert--warning mb-0">
	<?php echo JText::_('COM_EB_COMMENTS_LOCKED_BUT_VIEWED_BY_OWNER_ADMIN'); ?>
</div>
<?php } ?>

<?php if (!$post->allowComments()) { ?>
<div class="eb-comment-notice o-alert o-alert--warning mb-0">
	<?php echo JText::_('COM_EB_COMMENTS_LOCKED'); ?>
</div>
<?php } ?>

<a class="eb-anchor-link" name="comments" id="comments" data-allow-comment="<?php echo $post->allowcomment;?>">&nbsp;</a>

<?php echo EB::comment()->html($post, [], '', ['isEntryView' => true]);?>
