<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="form-group">
	<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_SELECT_VALUES', 'params[placeholder]'); ?>

	<div class="col-md-7" data-select-container>
		<?php if ($options) { ?>
			<?php $i = 1; ?>
			<?php foreach ($options as $option) { ?>
			<div class="mt-5" data-select-row>
				<div class="form-inline">
					<div class="col-cell" style="width:40%; white-space: nowrap; padding: 0 5px 0 0;">
						<input type="text" name="field_options_title[]" class="form-control" placeholder="Title for value" value="<?php echo $this->fd->html('str.escape', $option->title);?>"  data-select-value/>
					</div>
					<div class="col-cell" style="width:40%; white-space: nowrap; padding: 0 5px 0 0;">
						<input type="text" name="field_options_values[]" class="form-control" placeholder="Value stored in db" value="<?php echo $this->fd->html('str.escape', $option->value);?>"  data-select-db />
					</div>

					<div class="col-cell cell-tight">
						<a href="javascript:void(0);" class="btn btn-danger btn-sm text-center<?php echo ($field->id && $i > 1) ? '' : ' hide'; ?>" data-select-remove><i class="fdi fa fa-minus-circle"></i></a>
						<a href="javascript:void(0);" class="btn btn-primary btn-sm text-center" data-select-add-<?php echo $element;?>><i class="fdi fa fa-plus-circle"></i></a>
					</div>
				</div>
			</div>
			<?php $i++; ?>
			<?php } ?>
		<?php } ?>
	</div>


</div>
