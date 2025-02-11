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


<div class="o-form-group" data-name="font-family">
	<label class="o-control-label eb-composer-field-label" for="eb-font-family">
	Font</label>

	<div class="o-control-input">
		<div class="eb-fake-input-field t-pr--no">
			<div class="dropdown_ t-flex-grow--1">
				<a href="javascript:(0)" data-id="font-family" data-eb-font-family-menu data-bp-toggle="dropdown" data-eb-font-family-caption class="t-d--block t-text--800 eb-fake-input-field__text">
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT_DEFAULT');?>

				</a>
				<div class="dropdown-menu dropdown-menu--right dropdown-menu--color-picker">
					<div class="eb-font-family-content" data-id="font-family" data-eb-font-family-content>
						<div class="eb-list" data-type="list">
							<div class="eb-list-item-group">
								<div class="eb-list-item active" data-eb-font-family-option data-value=""><?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT_DEFAULT');?></div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Arial, sans-serif;" data-value="Arial">Arial</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Comic Sans MS, cursive;" data-value="Comic Sans MS">Comic Sans MS</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Courier, monospace;" data-value="Courier">Courier</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Georgia, serif;" data-value="Georgia">Georgia</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Tahoma, sans-serif;" data-value="Tahoma">Tahoma</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Trebuchet MS, sans-serif;" data-value="Trebuchet MS">Trebuchet MS</div>
								<div class="eb-list-item" data-eb-font-family-option style="font-family: Verdana, sans-serif;" data-value="Verdana">Verdana</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<i class="fdi fa fa-caret-down t-mr--sm"></i>
		</div>


	</div>
</div>
<div class="o-form-group" data-name="font-size">
	<label class="o-control-label eb-composer-field-label" for="eb-font-size">
	Size</label>

	<div class="o-control-input">
		<div class="t-d--flex t-align-items--c">
			<div class="t-pr--md t-w--50 t-d--flex t-align-items--c">
				<input type="text" class="o-form-control t-text--center" name="eb-font-size" id="eb-font-size" value="" data-eb-numslider-input autocomplete="off">
				<span class="t-pl--sm">px</span>
			</div>
			<div class="" data-id="font-size" data-eb-font-size-content style="width: 120px">
				<?php
					echo $this->output('site/composer/fields/numslider', array(
						'name' => 'fontsize',
						'toggle' => false,
						'resetToggle' => true
					));
				?>
			</div>
		</div>

	</div>
</div>
<div class="o-form-group eb-tabs" data-eb-tabs-mode="toggle" data-name="font-color">
	<label class="o-control-label eb-composer-field-label" for="img-size-name">
	Color</label>

	<div class="o-control-input t-d--flex t-align-items--c" data-eb-colorhex-container>
		<input class="colorpicker-hex-input o-form-control" maxlength="7" size="7" type="text" data-eb-colorhex-field>

		<div class="dropdown_">
			<div class="t-pl--xs xt-pt--xs eb-font-color-menu" data-id="font-color" data-eb-font-color-menu data-bp-toggle="dropdown">
				<span style="background-color: #000;" data-eb-font-color-caption></span>
			</div>

			<div class="dropdown-menu dropdown-menu--right dropdown-menu--color-picker">
				<div class="eb-font-color-content" data-id="font-color" data-eb-font-color-content>
					<?php echo $this->output('site/composer/fields/colorpicker', array(
						'attributes' => 'data-eb-font-color-picker'
					)); ?>
				</div>
			</div>
		</div>

	</div>
</div>

