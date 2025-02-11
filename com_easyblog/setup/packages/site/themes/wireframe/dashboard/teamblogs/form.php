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
		<?php echo $this->html('snackbar.standard', (!$teamblog->id) ? 'COM_EASYBLOG_DASHBOARD_TEAMBLOGS_CREATE' : 'COM_EASYBLOG_DASHBOARD_TEAMBLOGS_EDIT'); ?>

		<div class="eb-dashboard-form-section__form">
			<div class="form-horizontal clear">
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_NAME'); ?></label>
					<div class="col-md-7">
						<input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->escape($teamblog->title);?>" placeholder="<?php JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_NAME_REQUIRED'); ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_ALIAS'); ?></label>
					<div class="col-md-7">
						<input name="alias" id="alias" class="form-control input-sm" maxlength="255" value="<?php echo $this->escape($teamblog->alias);?>" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_ALIAS_OPTIONAL'); ?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_DESCRIPTION');?></label>
					<div class="col-md-7">
						<?php if (FH::isJoomla5()) { ?>
							<?php echo $editor->display('description', $teamblog->get( 'description') , '99%', '200', '10', '10', ['image', 'readmore', 'pagebreak'], '', '', 'com_easyblog', []); ?>

							<?php } else { ?>
								<?php echo $editor->display('description', $teamblog->get( 'description') , '99%', '200', '10', '10', ['image', 'readmore', 'pagebreak'], [], 'com_easyblog'); ?>
						<?php } ?>

					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_PRIVACY'); ?></label>
					<div class="col-md-5">
						<?php echo $this->fd->html('form.dropdown', 'access', $teamblog->access, EB::privacy()->getOptions('teamblog')); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_ALLOW_JOIN'); ?></label>
					<div class="col-md-5">
						<?php echo $this->fd->html('form.toggler', 'allow_join', $teamblog->allow_join); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOGS_AVATAR'); ?></label>
					<div class="col-md-7">
						<?php if(! empty($teamblog->avatar)) { ?>
							<img style="border-style:solid;" src="<?php echo $teamblog->getAvatar(); ?>" width="60" height="60"/><br />
						<?php } ?>

						<input id="file-upload" type="file" name="Filedata" size="33" title="<?php echo JText::_('COM_EASYBLOG_PICK_AN_IMAGE');?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-left">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?></a>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" data-submit-button>
				<?php echo ($teamblog->id) ? JText::_('COM_EASYBLOG_UPDATE_BUTTON') : JText::_('COM_EASYBLOG_CREATE_BUTTON'); ?>
			</button>
		</div>
	</div>

	<?php if ($teamblog->id) { ?>
		<input type="hidden" name="id" value="<?php echo $teamblog->id;?>" />
		<?php echo $this->fd->html('form.action', 'teamblogs.save'); ?>
	<?php } else { ?>
		<?php echo $this->fd->html('form.action', 'teamblogs.create'); ?>
	<?php } ?>
</form>
