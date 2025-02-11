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
<form id="adminForm" name="adminForm" method="post" action="index.php">
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FIELDS_GROUP_DETAILS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_GROUP_TITLE', 'title'); ?>

					<div class="col-md-7">
						<input type="text" name="title" id="title" value="<?php echo $this->fd->html('str.escape', $group->getTitle());?>" class="form-control" />
						<span class="small hide" style="color:red;" data-title-error><?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE_EMPTY_WARNING');?></span>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_GROUP_PUBLISH_STATE', 'state'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'state', !$group->id ? true : $group->state); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FIELDS_GROUP_PERMISSIONS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_GROUP_VIEW_ITEMS', 'read'); ?>

					<div class="col-md-7">
						<?php echo $this->html('tree.groups', 'read', $group->getAcl('read')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_GROUP_USE_ITEMS', 'write'); ?>

					<div class="col-md-7">
						<?php echo $this->html('tree.groups', 'write', $group->getAcl('write')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $group->id;?>" />
<?php echo $this->fd->html('form.action');?>
</form>
