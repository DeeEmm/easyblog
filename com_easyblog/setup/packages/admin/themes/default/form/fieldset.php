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
	<?php foreach ($fieldsets as $fieldset) { ?>
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_($fieldset->label); ?></b>
				<div class="panel-info"><?php echo JText::_($fieldset->info);?></div>
			</div>

			<div class="panel-body">
				<?php foreach ($fieldset->fields as $field) { ?>
					<div class="form-group">
						<label for="page_title" class="col-md-5">
							<?php echo JText::_($field->attributes->label); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_($field->attributes->label); ?>"
								data-content="<?php echo JText::_($field->attributes->label. '_DESC');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php
								$defaultVal = $field->attributes->default;
								$fieldType = ($this->input->get('view', '', 'cmd') == 'settings' && $field->attributes->type == 'textext') ? 'text' : $field->attributes->type;
							?>
							<?php echo $this->output('admin/form/fields/' . $fieldType, array('field' => $field, 'default' => $defaultVal, 'prefix' => $prefix)); ?>
						</div>
					</div>

				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
