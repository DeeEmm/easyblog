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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'main_comment', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT'); ?>


				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_COMMENTS_PAGINATION', 'list_limit'); ?>

					<div class="col-md-7">
						<div class="row" data-list-length-input>
							<div class="col-md-4">
								<div class="input-group">
									<input type="text" name="comment_pagination" value="<?php echo $this->config->get('comment_pagination');?>" class="form-control text-center" />
									<span class="input-group-addon">
										<?php echo JText::_('Comments'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'comment_sort', 'COM_EASYBLOG_SETTINGS_COMMENTS_SORTING', [
						'desc' => 'COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_DESCENDING',
						'asc' => 'COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_ASCENDING'
					]);
				?>

				<?php echo $this->fd->html('settings.toggle', 'comment_bbcode', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_likes', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES'); ?>

				<?php echo $this->fd->html('settings.toggle', 'main_allowguestviewcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_autotitle', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL', 'comment_maxthreadedlevel'); ?>

					<div class="col-md-7">
						<input type="text" name="comment_maxthreadedlevel" id="comment_maxthreadedlevel" class="form-control input-mini text-center" value="<?php echo $this->config->get('comment_maxthreadedlevel');?>" />
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'comment_hidechilds', 'COM_EB_SETTINGS_COMMENTS_HIDE_CHILDS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_showsubscribe', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_SHOWSUBSCRIBE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_autosubscribe', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_allowlogin', 'COM_EASYBLOG_SETTINGS_COMMENTS_ALLOW_LOGIN_LINK'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_moderatecomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_moderateauthorcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_moderateguestcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS'); ?>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'comment_show_title', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_requiretitle', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_show_email', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_require_email', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_show_website', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_require_website', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_tnc', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'comment_tnc_article', 'COM_EB_SETTINGS_COMMENTS_TNC_ARTICLE', '', 'data-tnc-article'); ?>

				<div class="form-group <?php echo $this->config->get('comment_tnc_article') ? '' : 'hidden';?>" data-tnc-article-selection>
					<?php echo $this->fd->html('form.label', 'COM_EB_COMMENTS_TNC_ARTICLE', 'main_orphanitem_ownership'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.article', 'comment_tnc_articleid', $this->config->get('comment_tnc_articleid')); ?>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('comment_tnc_article') ? 'hidden' : '';?>" data-tnc-text>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT', 'comment_tnctext'); ?>

					<div class="col-md-7">
						<textarea name="comment_tnctext" id="comment_tnctext" class="form-control" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('comment_tnctext')); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
