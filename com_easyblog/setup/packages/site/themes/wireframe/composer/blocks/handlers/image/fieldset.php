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
<div class="eb-composer-fieldset eb-image-url-fieldset" data-eb-image-url-fieldset data-name="image-url">
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_URL', 'image-url'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group eb-image-url-field" data-eb-image-url-field>
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" id="image-url" value="" class="o-form-control" data-eb-image-url-field-text />
				<span class="o-input-group__btn">
					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-eb-image-url-field-update-button><?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?></a>
				</span>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-source-fieldset" data-eb-image-source-fieldset data-name="image-source">
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_SOURCE', 'image-source'); ?>
	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered eb-image-source-field" data-eb-image-source-field>
			<div class="o-form-group eb-image-source-header">
				<div class="t-flex-shrink--0 t-pr--md">
					<div class="o-aspect-ratio" style="--aspect-ratio: 1/1; width: 72px">
						<div class="eb-image-source-thumbnail" data-eb-image-source-thumbnail></div>
					</div>
				</div>

				<div class="t-min-width--0">
					<div class="">
						<div class="eb-image-source-title t-text--truncate" data-eb-image-source-title></div>
					</div>
					<div class="l-cluster l-spaces--xs">
						<div>
							<div class="eb-image-source-size" data-eb-image-source-size></div>
							<div class="">&middot;</div>
							<div class="">
								<a href="javascript:void(0);"
									data-eb-image-source-change-button
									data-eb-mm-browse-button
									data-eb-mm-start-uri="_cG9zdA--"
									data-eb-mm-filter="image"
								><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE'); ?></a>
							</div>
						</div>

					</div>
					<div class="col-cell cell-tight t-hidden">
						<button type="button" class="btn btn--sm btn-eb-default-o eb-image-source-change-button"
							data-eb-image-source-change-button
							data-eb-mm-browse-button
							data-eb-mm-start-uri="_cG9zdA--"
							data-eb-mm-filter="image"
						><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE'); ?></button>
					</div>
				</div>
			</div>
			<div class="o-form-group">
				<div class="t-d--flex t-align-items--c t-pl--md t-border-top--1">
					<div class="t-text--500 t-min-width--0">
						<div class="t-text--truncate">
							<span data-eb-image-source-url></span>
						</div>
					</div>
					<div>
						<a href="javascript:void(0);" class="t-d--block t-text--500 t-p--sm t-no-focus-outline" data-eb-preview-imageurl>
							<i class="fdi fa fa-external-link-alt"></i>
						</a>
					</div>
				</div>
			</div>

			<div class="o-form-group eb-image-variation-field can-create can-delete" data-eb-image-variation-field>
				<div class="eb-image-variation-list-container" data-eb-image-variation-list-container></div>
				<div class="eb-image-variation-create-container o-form-horizontal t-border-top--1">
					<div class="">
						<div class="o-form-group t-mb--md">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME'); ?></div>
							<div class="o-control-input">
								<input type="text" class="o-form-control eb-image-variation-name" data-eb-image-variation-name placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME_PLACEHOLDER'); ?>">
							</div>
						</div>
					</div>
					<div class=" eb-image-variation-size-field">
						<div class="o-form-group t-mb--md">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_WIDTH'); ?></div>
							<div class="o-control-input"><input type="text" class="o-form-control eb-image-variation-width" data-eb-image-variation-width></div>
						</div>
						<div class="o-form-group t-mb--md">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_HEIGHT'); ?></div>
							<div class="o-control-input"><input type="text" class="o-form-control eb-image-variation-height" data-eb-image-variation-height></div>
						</div>

					</div>
				</div>
				<div class="eb-image-variation-action">
					<button type="button" class="eb-image-variation-new-button btn btn--sm btn-link hover:t-bg--transparent" data-eb-image-variation-new-button>
						<?php echo JText::_('COM_EASYBLOG_MM_NEW_SIZE'); ?>
					</button>

					<button type="button" class="eb-image-variation-rebuild-button btn btn--sm btn-eb-default-o" data-eb-image-variation-rebuild-button>
						<i class="fdi fa fa-undo"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_REBUILD'); ?>
					</button>

					<button type="button" class="eb-image-variation-delete-button btn btn--sm btn-link hover:t-bg--transparent t-text--danger" data-eb-image-variation-delete-button>
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_DELETE'); ?>
					</button>

					<button type="button" class="eb-image-variation-cancel-button btn btn--sm btn-link hover:t-bg--transparent t-text--500" data-eb-image-variation-cancel-button>
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?>
					</button>
					<button type="button" class="eb-image-variation-create-button btn btn--sm btn-link hover:t-bg--transparent" data-eb-image-variation-create-button>
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATE'); ?>
					</button>
				</div>

				<div class="eb-hint hint-creating-variation layout-overlay style-gray size-sm">
					<div>
						<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
						<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE'); ?></span>
					</div>
				</div>

				<div class="eb-hint hint-failed-variation layout-overlay style-gray size-sm">
					<div>
						<i class="eb-hint-icon fa fa-warning"></i>
						<span class="eb-hint-text">
							<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE_ERROR'); ?>
							<span class="eb-image-source-failed-action">
								<button type="button" class="btn btn-eb-primary btn--sm " data-eb-image-variation-cancel-failed-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?></button>
							</span>
						</span>
					</div>
				</div>
			</div>

			<div class="eb-hint hint-loading layout-overlay style-gray size-sm">
				<div>
					<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
					<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS'); ?></span>
				</div>
			</div>

			<div class="eb-hint hint-failed layout-overlay style-gray size-sm">
				<div>
					<i class="eb-hint-icon fa fa-warning"></i>
					<span class="eb-hint-text">
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS_ERROR'); ?>
						<span class="eb-image-source-failed-action">
							<button type="button" class="btn btn-sm btn-default" data-eb-image-source-change-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE_IMAGE'); ?></button>
							<button type="button" class="btn btn-sm btn-primary" data-eb-image-source-retry-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_RETRY'); ?></button>
						</span>
					</span>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-size-fieldset" data-eb-image-fieldset data-eb-image-fieldset-size data-type="simple">
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_SIZE', 'image-size'); ?>

	<div class="o-loader"></div>

	<div class="eb-composer-fieldset-content">
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<div class="eb-image-size-input">
					<div class="eb-image-size-input__field">
						<label for="" class="eb-image-size-input__label">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_WIDTH');?>
						</label>
						<div class="eb-image-size-input__input">
							<input type="text" name="image-width" class="o-form-control" data-eb-image-width />
						</div>

					</div>
					<div class="eb-image-size-input__unit">px</div>
				</div>
			</div>
			<div class="o-grid__cell">
				<div class="eb-image-size-input">
					<div class="eb-image-size-input__field">
						<label for="" class="eb-image-size-input__label">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT');?>
						</label>
						<div class="eb-image-size-input__input">
							<input type="text" name="image-height" class="o-form-control" data-eb-image-height />
						</div>

					</div>
					<div class="eb-image-size-input__unit">px</div>
				</div>
			</div>
			<div class="o-grid__cell o-grid__cell--auto-size">
				<?php echo $this->html('composer.field.checkbox', 'image-lock-ratio', '<i class="fdi fa fa-lock"></i>', true, array('data-eb-image-lock-ratio')); ?>
			</div>
		</div>

		<?php echo $this->html('composer.field.checkbox', 'image-responsive-option', 'COM_EASYBLOG_EXPAND_TO_FULL_WIDTH_ON_MOBILE', true, array('data-eb-image-responsive')); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-alignment-fieldset eb-composer-fieldset--accordion is-open" data-eb-image-fieldset data-type="simple" data-eb-image-alignment data-eb-composer-block-section>

	<?php echo $this->html('composer.label', 'COM_EASYBLOG_COMPOSER_ALIGNMENT', 'image-alignment'); ?>

	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.field.alignment', null, ['wrapperAttribute' => 'data-eb-image-alignment-selection']); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-caption-fieldset eb-composer-fieldset--accordion is-open" data-eb-image-fieldset data-eb-image-caption-fieldset data-name="image-caption" data-eb-composer-block-section>
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_CAPTION', 'caption-text'); ?>

	<div class="eb-composer-fieldset-content">

		<textarea id="caption-text" class="o-form-control eb-image-caption-text-field" placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_ENTER_CAPTION_HERE', true);?>" data-eb-image-caption-text-field></textarea>

		<?php echo $this->fd->html('form.dropdown', 'caption-alignment', 'center', [
			'left' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_LEFT',
			'center' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_CENTER',
			'right' => 'COM_EASYBLOG_COMPOSER_ALIGNMENT_RIGHT'
		], ['attr' => 'data-eb-image-caption-alignment']); ?>

		<?php echo $this->html('composer.panel.help', 'COM_EB_BLOCKS_IMAGE_CAPTION_HELP'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-alt-fieldset eb-composer-fieldset--accordion is-open"  data-eb-image-fieldset data-type="standard" data-eb-image-alt-fieldset data-name="image-alt" data-eb-composer-block-section>
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_ALT', 'image-alt'); ?>

	<div class="eb-composer-fieldset-content">
		<textarea id="image-alt" class="o-form-control eb-image-alt-text-field" placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_ENTER_ALT_HERE', true);?>" data-eb-image-alt-text-field></textarea>

		<?php echo $this->html('composer.panel.help', 'COM_EB_BLOCKS_IMAGE_ALT_HELP'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-link-fieldset eb-composer-fieldset--accordion is-open" data-eb-image-fieldset data-type="simple" data-eb-composer-block-section>
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_LINK', 'image-link'); ?>

	<div class="eb-composer-fieldset-content">
		<?php echo $this->fd->html('form.dropdown', 'image-link', 'none', [
			'none' => 'COM_EASYBLOG_MM_IMAGE_LINK_NONE',
			'lightbox' => 'COM_EASYBLOG_MM_IMAGE_LINK_POPUP',
			'custom' => 'COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_SAME_WINDOW',
			'custom_new' => 'COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_NEW_WINDOW'
		], ['attr' => 'data-eb-image-link']); ?>

		<input type="text" name="popup_url" class="o-form-control t-lg-mt--md t-hidden" placeholder="http://site.com" data-eb-image-link-url />

		<?php echo $this->html('composer.panel.help', 'COM_EB_BLOCKS_IMAGE_LINK_HELP'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-style-fieldset eb-composer-fieldset--accordion is-open" data-eb-image-fieldset data-type="simple" data-eb-image-style-fieldset data-name="image-style" data-eb-composer-block-section>
	<?php echo $this->html('composer.label', 'COM_EASYBLOG_BLOCKS_IMAGE_STYLE', 'image-style'); ?>

	<div class="eb-composer-fieldset-content">
		<div>
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'clear', 'cssClass' => 'simple']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'gray', 'cssClass' => 'gray']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'polaroid', 'cssClass' => 'polaroid']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'solid', 'cssClass' => 'solid']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'dashed', 'cssClass' => 'dashed']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'dotted', 'cssClass' => 'dotted']); ?>
					<?php echo $this->output('site/composer/blocks/handlers/image/style', ['style' => 'overlay', 'cssClass' => 'overlay']); ?>
				</div>
			</div>
		</div>

	</div>
</div>
