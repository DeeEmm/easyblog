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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_COMMENTS_EDIT_COMMENT_DETAILS'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_TITLE', 'title'); ?>

						<div class="col-md-7">	
							<?php echo $this->fd->html('form.text', 'title', $this->fd->html('str.escape', $comment->title), 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_NAME', 'name'); ?>
						
						<div class="col-md-7">
							<?php if ($comment->created_by) { ?>
								<span><a href="index.php?option=com_users&view=user&layout=edit&id=<?php echo $comment->created_by;?>" target="_blank"><?php echo $comment->getAuthor()->getName();?></a></span>
							<?php } else { ?>
								<?php echo $this->fd->html('form.text', 'name', $this->fd->html('str.escape', $comment->name), 'name'); ?>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_EMAIL', 'email'); ?>
						<div class="col-md-7">
							<?php if ($comment->created_by) { ?>
								<span><a href="index.php?option=com_users&view=user&layout=edit&id=<?php echo $comment->created_by;?>" target="_blank"><?php echo $comment->getAuthor()->user->email;?></a></span>
							<?php } else { ?>
								<?php echo $this->fd->html('form.text', 'email', $this->fd->html('str.escape', $comment->email), 'email'); ?>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_WEBSITE', 'url'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'url', $this->fd->html('str.escape', $comment->url), 'url'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_CREATED', 'created'); ?>
						
						<div class="col-md-7">

							<div class="input-group date" data-created>
								<span data-preview>
									<?php if ($comment->created) { ?>
										<?php echo $this->fd->html('str.date', $comment->created, JText::_('COM_EASYBLOG_DATE_DMY24H')); ?>
									<?php } else { ?>
										<?php echo JText::_('COM_EASYBLOG_COMPOSER_NOW');?>
									<?php } ?>
								</span>

								<a href="javascript:void(0);" class="btn btn-default btn-xs" data-calendar>
									<i class="fdi far fa-calendar-alt"></i>
								</a>

								<a href="javascript:void(0);" class="btn btn-default btn-xs" data-cancel style="display: none;">
									<i class="fdi fa fa-undo"></i>
								</a>

								<input type="hidden" name="created" data-datetime value="<?php echo $comment->created;?>" />        
							</div>
						</div>
					</div>


					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_COMMENTS_COMMENT_PUBLISH', 'published'); ?>

						<div class="col-md-7">
						   <?php echo $this->fd->html('form.toggler', 'published', $comment->published); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_IP_ADDRESS', 'ip'); ?>
						
						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'ip', $comment->ip, 'ip'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_COMMENTS_EDIT_COMMENT_MESSAGE'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.textarea', 'comment', $comment->comment, 'comment', ['attributes' => 'data-comment-editor', 'rows' => 5]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="id" value="<?php echo $comment->id;?>" />
</form>
