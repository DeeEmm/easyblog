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
<div class="eb-comment<?php echo $comment->depth ? ' is-child' . ' depth-' . $comment->depth : ''; ?><?php echo $comment->isLike ? ' is-like' : '';?><?php echo $comment->isModerated() ? ' is-moderated' : '';?>" data-comment-item data-id="<?php echo $comment->id;?>"
	<?php
		$depth = $comment->depth;

		if (!$this->isMobile()) {
			if ($depth == 1) {
				if ($rtl) {
					echo 'style="margin-right:' . $depth*65 . 'px;"';
				} else {
					echo 'style="margin-left:' . $depth*65 . 'px;"';
				}
			} else if ($depth > 1) {
				if ($rtl) {
					echo 'style="margin-right:' . ($depth*50 + 15) . 'px;"';
				} else {
					echo 'style="margin-left:' . ($depth*50 + 15) . 'px;"';
				}
			}
		} else {
			if ($depth >= 1) {
				if ($rtl) {
					echo 'style="margin-right: 65px;"';
				} else {
					echo 'style="margin-left: 65px;"';
				}
			}
		}
	?>>
	<a id="comment-<?php echo $comment->id;?>"></a>

	<?php if ($comment->isModerated()) { ?>
	<div class="under-moderation">
		<?php echo JText::_('COM_EASYBLOG_COMMENT_POSTED_UNDER_MODERATION');?>
	</div>
	<?php } ?>

	<div class="uk-comment-header uk-position-relative">
		<div class="uk-grid-medium uk-flex-middle uk-grid" uk-grid>
			<div class="uk-width-auto uk-first-column">
				<?php echo $this->html('avatar.user', $comment->author, 'default', $comment->created_by != 0); ?>
			</div>

			<div class="uk-width-expand">
				<h4 class="uk-comment-title uk-margin-remove">
					<?php if ($comment->created_by == 0) { ?>
						<a href="javascript:void(0);" class="uk-link-reset" title="<?php echo $this->fd->html('str.escape', $comment->author->getName());?>"><?php echo JText::_('COM_EASYBLOG_GUEST').' - '.$comment->name;?></a>
					<?php } else { ?>
						<a href="<?php echo $comment->author->getProfileLink();?>" class="uk-link-reset" title="<?php echo $this->fd->html('str.escape', $comment->author->getName());?>"><?php echo $comment->author->getName();?></a>
					<?php } ?>
				</h4>

				<p class="uk-comment-meta uk-margin-remove-top">

					<?php if ($this->config->get('comment_show_website') && $comment->url) { ?>
						(<a class="uk-link-reset" href="<?php echo $this->fd->html('str.escape', $comment->url);?>" rel="nofollow" target="_blank"><?php echo JText::_('COM_EB_COMMENT_WEBSITE');?></a>)
					<?php } ?>

					<?php echo JText::_('COM_EASYBLOG_ON');?> <?php echo JHTML::date($comment->created, JText::_('DATE_FORMAT_LC2')); ?>

					<?php
					if (
							(FH::isSiteAdmin() || $this->acl->get('manage_comment') || ($this->my->id == $comment->created_by && $this->acl->get('edit_comment') ) && !$this->my->guest) ||
							(FH::isSiteAdmin() || ($this->my->id == $comment->created_by && $this->acl->get('delete_comment') ) && !$this->my->guest)
						) {
					?>
				</p>




				<div class="col-cell text-right">
					<div class="eb-comment-admin dropdown">
						<b class="dropdown-toggle_" data-bp-toggle="dropdown">
							<i class="fdi fa fa-cog"></i>
							<i class="fdi fa fa-caret-down"></i>
						</b>
						<ul class="dropdown-menu">
							<?php if (FH::isSiteAdmin() || $this->acl->get('manage_comment') || ($this->my->id == $comment->created_by && $this->acl->get('edit_comment') ) && !$this->my->guest) { ?>
							<li>
								<a href="javascript:void(0);" data-comment-edit>
									<i class="fdi fa fa-pencil-alt"></i> <?php echo JText::_('COM_EASYBLOG_COMMENTS_EDIT');?>
								</a>
							</li>
							<?php } ?>

							<?php if (FH::isSiteAdmin() || $this->acl->get('manage_comment') || ($this->my->id == $comment->created_by && $this->acl->get('delete_comment') ) && !$this->my->guest) { ?>
							<li>
								<a href="javascript:void(0);" data-comment-delete>
									<i class="fdi fa fa-trash-o"></i> <?php echo JText::_('COM_EASYBLOG_COMMENTS_DELETE');?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>

	<div class="row-table align-top">

		<div class="col-cell cell-content eb-comment-content">



			<div data-comment-body>

				<div class="uk-comment-body" data-comment-preview>
					<?php if ($comment->title && $this->config->get('comment_show_title')) { ?>
					<div class="eb-comment-title"><b><?php echo $comment->title;?></b></div>
					<?php } ?>

					<?php echo $comment->comment;?>
				</div>

				<?php if (!$comment->isModerated()) { ?>
				<div class="uk-margin-top">
					<ul class="uk-subnav" uk-margin="">

						<?php if ($this->config->get('comment_likes')) { ?>
							<li>
								<span class="eb-comment-heart" data-fd-tooltip data-fd-tooltip-title="<?php echo $comment->likesAuthor;?>" data-comment-like-tooltip data-fd-tooltip-placement="bottom">
								<i class="fdi fa fa-heart"></i> <b data-comment-like-counter><?php echo $comment->likesCount;?></b>
								</span>
							</li>

							<?php if (!$this->my->guest) { ?>
							<li>
								<span class="eb-comment-likes">
								<a href="javascript:void(0);" class="like-comment" data-comment-like><?php echo JText::_('COM_EASYBLOG_COMMENTS_LIKE');?></a>
								<a href="javascript:void(0);" class="unlike-comment" data-comment-unlike><?php echo JText::_('COM_EASYBLOG_COMMENTS_UNLIKE');?></a>
								</span>
							</li>
							<?php } ?>
						<?php } ?>

						<?php if ((($this->acl->get('allow_comment') && !$this->my->guest) || ($this->acl->get('allow_comment') && $this->my->guest)) && (($comment->depth + 1) < $this->config->get('comment_maxthreadedlevel'))) { ?>
						<li>
							<span class="eb-comment-reply">
							<a href="javascript:void(0);" class="hide" data-comment-reply-cancel><?php echo JText::_('COM_EASYBLOG_COMMENTS_CANCEL');?></a>
							<a href="javascript:void(0);" data-comment-reply data-depth="<?php echo $comment->depth + 1;?>"><?php echo JText::_('COM_EASYBLOG_COMMENTS_REPLY');?></a>
							</span>
						</li>
						<?php } ?>

					</ul>

				</div>
				<?php } ?>
			</div>

			<div class="eb-comment-editor form-group hide mt-15" data-comment-edit-editor>
				<div class="eb-comment-notice" data-edit-comment-notice></div>

				<?php if ($this->config->get('comment_requiretitle') || $this->config->get('comment_show_title')) { ?>
				<div class="form-group">
					<input type="text" class="form-control" name="title" id="title" value="<?php echo $comment->title; ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_COMMENTS_TITLE_PLACEHOLDER', true); ?>" data-comment-title-edit/>
				</div>
				<?php } else { ?>
					<input type="hidden" id="title" name="title" value="" data-comment-title-edit/>
				<?php } ?>

				<textarea class="form-control textarea" rows="3" data-comment-edit-textarea data-comment-bbcode="<?php echo $this->config->get('comment_bbcode'); ?>"><?php echo $comment->raw;?></textarea>
				<div class="hide" data-comment-edit-raw><?php echo $comment->raw;?></div>
				<div class="eb-comment-editor-actions text-right mt-10">
					<a href="javascript:void(0);" class="btn btn-default btn-sm" data-comment-edit-cancel><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></a>
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-comment-edit-update><?php echo JText::_('COM_EASYBLOG_UPDATE_COMMENT_BUTTON'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
