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
?>
<div class="eb-comments" data-eb-comments>
	<h4 class="uk-heading-divider">
		<?php echo JText::_('COM_EASYBLOG_COMMENTS');?>
		<?php if ( $blog->totalComments ) { ?>
			<span data-comment-counter><?php echo $blog->totalComments;?></span>
		<?php } ?>
	</h4>

	<?php if ($this->my->guest && $this->config->get('comment_allowlogin') && !$this->acl->get('allow_comment') && $this->config->get('main_allowguestviewcomment')) { ?>
		<div class="eb-composer-author row-table">
			<div class="col-cell">
				<div class="pull-right">
					<?php echo JText::_('COM_EASYBLOG_COMMENTS_ALREADY_REGISTERED');?>
					<a href="<?php echo $loginURL;?>"><?php echo JText::_('COM_EASYBLOG_COMMENTS_ALREADY_REGISTERED_LOGIN_HERE');?></a>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if (!$this->config->get('main_allowguestviewcomment') && !$this->acl->get('allow_comment') && $this->my->guest) { ?>
		<div class="eblog-message info">
			<?php echo JText::sprintf('COM_EASYBLOG_COMMENT_DISABLED_FOR_GUESTS', $loginURL); ?>
		</div>
	<?php } else { ?>
		<?php if ($this->config->get('main_allowguestviewcomment') && $this->my->guest || (!$this->my->guest)) { ?>
			<div data-comment-list class="uk-margin-bottom">
				<?php if ($comments) { ?>
					<?php foreach ($comments as $comment) { ?>
						<?php echo $this->output('site/comments/default.item', array('comment' => $comment)); ?>
					<?php } ?>
				<?php } ?>

				<?php if ($blog->allowComments() && !$comments) { ?>
					<div class="uk-background-muted uk-padding uk-panel uk-text-center" data-comment-empty>
						<span uk-icon="icon: warning" class="uk-margin-small-right"></span>
						<?php echo JText::_('COM_EASYBLOG_COMMENTS_NO_COMMENT_YET'); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } ?>

	<?php if($pagination) {?>
		<?php echo $pagination;?>
	<?php } ?>

	<?php if ((($this->acl->get('allow_comment') && !$this->my->guest) || ($this->acl->get('allow_comment') && $this->my->guest)) && $blog->allowComments()) { ?>
		<div data-comment-form-wrapper>
			<?php echo $this->output('site/comments/form'); ?>
		</div>
	<?php } ?>
</div>
