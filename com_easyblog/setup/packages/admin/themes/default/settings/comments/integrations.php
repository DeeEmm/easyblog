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
<div class="row form-horizontal">
	<div class="col-lg-6">

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_DESC', '/administrators/comments/using-multiple-comment-systems'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_easyblog', 'COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'main_comment_multiple', 'COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO', '', '/administrators/comments/integrating-with-komento'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_komento', 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS', '', '/administrators/comments/integrating-with-easySocial-comments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_easysocial', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS', '', '/administrators/comments/integrating-with-easydiscuss-comments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_easydiscuss', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS', '', '/administrators/comments/integrating-with-disqus'); ?>

			<div class="panel-body">

				<?php echo $this->fd->html('settings.toggle', 'comment_disqus', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?>

				<?php echo $this->fd->html('settings.text', 'comment_disqus_code', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE', '', [
					'help' => 'https://stackideas.com/docs/easyblog/administrators/comments/integrating-with-disqus'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS', '', '/administrators/comments/integrating-with-hypercomments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_hypercomments', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?>

				<?php echo $this->fd->html('settings.text', 'comment_hypercomments_widgetid', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_WIDGETID'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS', '', '/administrators/comments/integrating-with-facebook-comments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_facebook', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'comment_facebook_colourscheme', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COLOUR_SCHEME', [
						'light' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT',
						'dark' => 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK'
					]);
				?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE', '', '/administrators/comments/integrating-with-intense-debate'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_intensedebate', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?>

				<?php echo $this->fd->html('settings.text', 'comment_intensedebate_code', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE', '', [
					'help' => 'https://stackideas.com/docs/easyblog/administrators/comments/integrating-with-intense-debate'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT', '', '/administrators/comments/integrating-with-jcomments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_jcomments', 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM', '', '/administrators/comments/integrating-with-compojoom-comments'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_compojoom', 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_COMMENTS_JLEX_COMMENTS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_jlex', 'COM_EB_SETTINGS_COMMENTS_JLEX_COMMENTS'); ?>
			</div>
		</div>
	</div>
</div>
