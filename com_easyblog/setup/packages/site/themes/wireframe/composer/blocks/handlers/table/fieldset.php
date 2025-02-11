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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_TABLE_STYLE'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'striped', 'COM_EASYBLOG_BLOCKS_TABLE_STYLE_STRIPED', $data->striped, 'data-table-striped'); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'bordered', 'COM_EASYBLOG_BLOCKS_TABLE_STYLE_BORDERED', $data->striped, 'data-table-bordered'); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'hover', 'COM_EASYBLOG_BLOCKS_TABLE_STYLE_HOVER', $data->hover, 'data-table-hover'); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'condensed', 'COM_EASYBLOG_BLOCKS_TABLE_STYLE_CONDENSED', $data->hover, 'data-table-condensed'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_TABLE_ROWS_COLUMNS'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_ROWS'); ?>
			</label>
			<div class="o-control-input">
				<div class="o-input-group">
					<input type="text" name="rows" id="rows" value="<?php echo $data->rows; ?>" class="o-form-control t-text--center" data-table-rows />
					<span class="o-input-group__btn">
						<a href="javascript:void(0);" class="btn btn-eb-default-o" data-table-rows-remove><i class="fdi fa fa-minus"></i></a>
						<a href="javascript:void(0);" class="btn btn-eb-default-o" data-table-rows-add><i class="fdi fa fa-plus"></i></a>
					</span>
				</div>
			</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label eb-composer-field-label">
				<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_COLUMNS'); ?>
			</label>
			<div class="o-control-input">
				<div class="o-input-group">
					<input type="text" name="columns" id="columns" value="<?php echo $data->columns; ?>" class="o-form-control t-text--center" data-table-columns />
					<span class="o-input-group__btn">
						<a href="javascript:void(0);" class="btn btn-eb-default-o" data-table-columns-remove><i class="fdi fa fa-minus"></i></a>
						<a href="javascript:void(0);" class="btn btn-eb-default-o" data-table-columns-add><i class="fdi fa fa-plus"></i></a>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
