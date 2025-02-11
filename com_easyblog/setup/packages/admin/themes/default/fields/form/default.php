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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FIELDS_FIELD_DETAILS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_GROUP', 'group_id'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'group_id', $field->group_id, $fieldGroups); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_TYPE', 'type'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'type', $field->type, $fieldTypes, ['attr' => 'data-field-type']); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_TITLE', 'title'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'title', $this->fd->html('str.escape', $field->title), 'title'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_HELP', 'help'); ?>

					<div class="col-md-7">
						<textarea name="help" id="help" class="form-control"><?php echo $this->fd->html('str.escape', $field->help);?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_PUBLISHED', 'state'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'state', is_null($field->state) ? true : $field->state); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_REQUIRED', 'required'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'required', is_null($field->required) ? false : $field->required); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FIELDS_FIELD_PROPERTIES'); ?>

			<div class="panel-body" data-field-form>
				<?php echo $form;?>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $field->id;?>" />
<?php echo $this->fd->html('form.action');?>
</form>
