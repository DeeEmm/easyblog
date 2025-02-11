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
<form method="post" action="<?php echo JRoute::_('index.php');?>" enctype="multipart/form-data">
	<div class="eb-dashboard-form-section">
		<?php echo $this->html('snackbar.standard', (!$tag->id) ? 'COM_EASYBLOG_DIALOG_CREATE_TAG_TITLE' : 'COM_EASYBLOG_DIALOG_EDIT_TAG_TITLE'); ?>

		<div class="eb-dashboard-form-section__form">
			<div class="form-horizontal clear">
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_NAME'); ?></label>
					<div class="col-md-7">
						<input type="text" id="tags" name="tags" class="form-control" value="<?php echo $this->escape($tag->title);?>" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_PLACEHOLDER'); ?>" />

						<?php if (!$tag->id) { ?>
						<div>
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_HELP');?>
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EB_DASHBOARD_TAG_DESCRIPTION'); ?></label>

					<div class="col-md-7">
						<textarea name="tag_description" class="form-control"><?php echo $this->escape($tag->description); ?></textarea>
					</div>
				</div>

				<?php if ($this->config->get('main_multi_language')) { ?>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_POST_LANGUAGE');?></label>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.languages', 'tag_language', $tag->language, 'data-composer-language'); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-left">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>" class="btn btn-default">
				<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>
			</a>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" data-submit-button>
				<?php echo ($tag->id) ? JText::_('COM_EASYBLOG_UPDATE_BUTTON') : JText::_('COM_EASYBLOG_CREATE_BUTTON'); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $tag->id;?>" />
	<?php echo $this->fd->html('form.action', $tag->id ? 'tags.save' : 'tags.create'); ?>
</form>
