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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section data-lottie-url-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_LOTTIE_URL'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" class="o-form-control" data-lottie-field-input />
				<span class="o-input-group__btn">
					<a href="javascript:void(0);" class="btn btn-eb-primary-o btn--sm" data-lottie-update>
						<i class="fdi fa fa-save"></i>
					</a>
				</span>
			</div>
		</div>
	</div>
</div>
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section data-lottie-settings-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_LOTTIE_SETTINGS'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EB_LOTTIE_SETTINGS_LOOP', 'lottie_loop', true); ?>

			<?php echo $this->html('composer.field.toggler', 'lottie_loop', '', ['attributes' => 'data-lottie-loop']); ?>
		</div>

		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EB_LOTTIE_SETTINGS_AUTOPLAY', 'lottie_autoplay', true); ?>

			<?php echo $this->html('composer.field.toggler', 'lottie_autoplay', '', ['attributes' => 'data-lottie-autoplay']); ?>
		</div>

		<div class="o-form-group">
			<?php echo $this->html('composer.field.label', 'COM_EB_LOTTIE_SETTINGS_HOVER', 'lottie_hover', true); ?>

			<?php echo $this->html('composer.field.toggler', 'lottie_hover', '', ['attributes' => 'data-lottie-hover']); ?>
		</div>
	</div>
</div>