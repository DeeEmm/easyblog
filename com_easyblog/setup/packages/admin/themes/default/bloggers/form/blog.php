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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_BLOGGER_BLOG_SETTINGS', 'COM_EASYBLOG_BLOGGER_BLOG_SETTINGS_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_AVATAR', 'avatar'); ?>
					<div class="col-md-7">
						<img id="user-avatar" src="<?php echo $author->getAvatar();?>" style="border: 1px solid #eee;" width="64" />
						<?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
							<input type="file" name="avatar" id="avatar" style="display: block;" size="65" />
						<?php } ?>
					</div>
				</div>

				<div class="form-group" data-composer-editors>
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_LAYOUT_SELECT_USER_EDITOR', 'user_editor'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.editors', 'user_editor', $bloggerParams->get('user_editor'), [
							'default' => 'composer',
							'additional' => [
								'composer' => JText::_('COM_EASYBLOG_COMPOSER_EDITOR')
							]
						]); ?>

						<div class="small mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR_NOTE');?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK', 'user_permalink'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'user_permalink', $this->fd->html('str.escape', $author->permalink), 'user_permalink'); ?>
						<div class="small"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK_USAGE'); ?></div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_PAGE_TITLE', 'blog_title'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'title', $this->fd->html('str.escape', $author->title), 'blog_title'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_PAGE_DESCRIPTION', 'blog_description'); ?>
					<div class="col-md-7">
						<textarea id="blog_description" name="description" class="form-control"><?php echo $author->getDescription(true);?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_WEBSITE', 'url'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'url', $this->fd->html('str.escape', $author->url), 'url'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_NICKNAME', 'nickname'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'nickname', $this->fd->html('str.escape', $author->nickname), 'nickname'); ?>
					</div>
				</div>

				<div class="form-group" style="display: block">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_BIOGRAPHY_INFO', 'biography'); ?>

					<div class="col-md-12">
						<?php echo $editor->display('biography', $author->getBiography(true) , '100%', '200', '10', '10' , array('pagebreak', 'ninjazemanta', 'readmore', 'article')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_TWITTER', 'COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_TWITTER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_OAUTH_ALLOW_ACCESS', 'integrations_facebook_signin'); ?>

					<div class="col-md-7">
						<?php if ($twitter->id) { ?>
							<div>
								<?php echo $twitterClient->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
							</div>
						<?php } else { ?>
							<div class="mb-xs"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></div>

							<?php echo $twitterClient->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
						<?php } ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_TWITTER_MESSAGE', 'integrations_twitter_message'); ?>
					<div class="col-md-7">
						<textarea id="integrations_twitter_message" name="integrations_twitter_message" class="form-control"><?php echo (empty($twitter->message)) ? $this->config->get('main_twitter_message', 'Published a new blog entry title:{title} under category:{category}. {link}') : $twitter->message; ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES', 'integrations_twitter_auto'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'integrations_twitter_auto', $twitter->auto); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_LINKEDIN', 'COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_LINKEDIN_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_OAUTH_ALLOW_ACCESS', 'linkedin_access'); ?>

					<div class="col-md-7">
						<?php if ($linkedin->id) { ?>
							<?php echo $linkedinClient->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
						<?php } else { ?>
							<div class="mb-xs"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?></div>

							<?php echo $linkedinClient->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
						<?php } ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES', 'integrations_linkedin_auto'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'integrations_linkedin_auto', $linkedin->auto); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE', 'integrations_linkedin_private'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'integrations_linkedin_private', $linkedin->private); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
