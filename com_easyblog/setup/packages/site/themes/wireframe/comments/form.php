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
<form class="eb-comment-form reset-form" method="post" action="<?php echo JRoute::_('index.php');?>" data-comment-form data-captcha="<?php echo $this->config->get('comment_captcha_type');?>">
	<div class="eb-composer-author row-table">
		<?php if ($this->config->get('layout_avatar')) { ?>
		<span class="col-cell cell-tight pr-15">
			<?php echo $this->html('avatar.user', $user); ?>
		</span>
		<?php } ?>

		<div class="col-cell">

			<?php if ($this->my->guest && $this->config->get('comment_allowlogin')) { ?>
			<div class="pull-right">
				<?php echo JText::_('COM_EASYBLOG_COMMENTS_ALREADY_REGISTERED');?>
				<a href="<?php echo $loginURL;?>"><?php echo JText::_('COM_EASYBLOG_COMMENTS_ALREADY_REGISTERED_LOGIN_HERE');?></a>
			</div>
			<?php } ?>

			<strong><?php echo $user->getName();?></strong>
			<div class="text-small text-muted"><?php echo $date->format(JText::_('DATE_FORMAT_LC1'));?></div>
		</div>
	</div>

	<div class="eb-comment-notice" data-comment-notice></div>

	<?php if ($this->acl->get('allow_comment')) { ?>
	<div class="eb-comment-editor">

		<?php if ($this->config->get('comment_requiretitle') || $this->config->get('comment_show_title')) { ?>
		<div class="form-group">
			<input type="text" class="form-control" name="title" id="title" placeholder="<?php echo JText::_('COM_EASYBLOG_COMMENTS_TITLE_PLACEHOLDER', true); ?>" data-comment-title/>
		</div>
		<?php } else { ?>
			<input type="hidden" id="title" name="title" value="" data-comment-title/>
		<?php } ?>

		<div class="form-group">
			<textarea id="comment" name="comment" class="form-control textarea" rows="5" data-comment-editor data-comment-bbcode="<?php echo $this->config->get('comment_bbcode'); ?>"></textarea>
		</div>

		<?php if ($this->my->guest) { ?>
			<div class="form-group">
				<input type="text" class="form-control" id="esname" name="esname" placeholder="<?php echo JText::_('COM_EASYBLOG_NAME', true);?>" data-comment-name/>
			</div>

			<?php if ($email || $website) { ?>
			<div class="form-group">
				<?php if ($email) { ?>
				<div class="col-cell<?php if ($email & $website) { echo " cell-half"; } ?>">
					<input type="text" class="form-control" name="esemail" id="esemail" placeholder="<?php echo JText::_('COM_EASYBLOG_EMAIL', true); ?>" data-comment-email/>
				</div>
				<?php } ?>

				<?php if ($website) { ?>
				<div class="col-cell<?php if ($email & $website) { echo " cell-half"; } ?>">
					<input type="text" class="form-control" name="url" id="url" placeholder="<?php echo JText::_('COM_EASYBLOG_WEBSITE', true); ?>" data-comment-website/>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		<?php } ?>

		<?php if (!$this->my->guest && $website) { ?>
		<div class="form-group">
			<input type="text" class="form-control" name="url" id="url" placeholder="<?php echo JText::_('COM_EASYBLOG_WEBSITE', true); ?>" data-comment-website/>
		</div>
		<?php } ?>

		<?php if ($subscribed) { ?>
		<div>
			<div id="unsubscription-message" class="unsubscription-message">
				<?php echo JText::_('COM_EASYBLOG_ENTRY_AUTO_SUBSCRIBE_SUBSCRIBED_NOTE'); ?>
				<a href="javascript:void(0);" title="" data-blog-unsubscribe data-type="entry" data-subscription-id="<?php echo $subscribed;?>"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_BLOG'); ?></a>
			</div>
		</div>
		<?php } ?>

		<?php if ($showSubscribe) { ?>
		<div class="eb-checkbox">
			<input type="checkbox" name="subscribe-to-blog" id="subscribe-to-blog" value="1"<?php echo $this->config->get('comment_autosubscribe') ? ' checked="checked"' : '';?> data-comment-subscribe />
			<label for="subscribe-to-blog">
				<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG');?>

				<?php if (!$this->my->guest) { ?>
					(<?php echo $this->my->email;?>)
				<?php } else { ?>
					(<?php echo JText::_('COM_EASYBLOG_ENTRY_AUTO_SUBSCRIBE_NOTE');?>)
				<?php } ?>
			</label>
		</div>
		<?php } ?>

		<?php if ($this->config->get('comment_tnc')) { ?>
		<div class="eb-checkbox">
			<input type="checkbox" name="tnc" id="tnc" value="1" data-comment-terms/>
			<label for="tnc">
				<?php echo JText::sprintf('COM_EASYBLOG_COMMENTS_TNC_AGREE', '<a href="javascript:void(0);" data-comment-tnc>' . JText::_('COM_EASYBLOG_COMMENTS_TNC_TEXT') . '</a>'); ?>
			</label>
		</div>
		<?php } ?>

		<?php echo EB::captcha()->getHTML();?>

		<div class="form-action">
			<button type="button" class="btn btn-primary text-sm leading-sm px-md py-xs" data-comment-submit><?php echo JText::_('COM_EASYBLOG_SUBMIT_COMMENT');?></button>
		</div>
	</div>
	<?php } ?>

	<input type="hidden" name="id" value="<?php echo $blog->id; ?>" data-comment-blog-id />
	<input type="hidden" name="parent_id" id="parent_id" value="0" data-comment-parent-id />
	<input type="hidden" name="comment_depth" id="comment_depth" value="0" data-comment-depth />
	<input type="hidden" name="email" id="email" value="<?php echo $this->my->email; ?>" data-comment-email />
</form>