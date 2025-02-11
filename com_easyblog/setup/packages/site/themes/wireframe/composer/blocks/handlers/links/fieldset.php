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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'preview', 'COM_EASYBLOG_BLOCKS_LINKS_DISPLAY_IMAGE', $data->showImage, 'data-links-image'); ?>
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'newwindow', 'COM_EASYBLOG_OPEN_IN_NEW_WINDOW', $data->newWindow, 'data-links-newwindow'); ?>
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'nofollow', 'COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_ATTRIBUTE_NOFOLLOW', $data->noFollow, 'data-links-nofollow'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section data-eb-composer-block-links-image>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_LINKS_IMAGES'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div class="eb-composer-field-links text-center">
				<div class="btn-group">
					<button type="button" class="btn btn-eb-default-o btn-sm" data-images-previous>
						<i class="fdi fa fa-arrow-left"></i>
					</button>
					<button type="button" class="btn btn-eb-default-o btn-sm" data-images-next>
						<i class="fdi fa fa-arrow-right"></i>
					</button>
				</div>

				<div class="eb-links-media" data-images>
					<i class="fdi far fa-image" data-image-placeholder></i>
				</div>

				<div class="eb-links-result">
					<span data-image-current-index></span> / <span data-images-total></span>
				</div>
			</div>
		</div>
	</div>
</div>