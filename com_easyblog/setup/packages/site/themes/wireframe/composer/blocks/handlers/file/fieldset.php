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
	<?php echo $this->html('composer.panel.header', 'COM_EB_BLOCK_FILE_PANEL_FILE_SOURCE_SECTION'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered eb-image-source-field">
			<div class="o-form-group eb-image-source-header">
				<div class="t-flex-shrink--0 t-pr--md">
					<div class="o-aspect-ratio" style="--aspect-ratio: 1/1; width: 72px">
						<div class="eb-file-thumb-wrapper">
							<div class="eb-file-thumb">
								<i data-panel-file-source-icon></i>
							</div>
						</div>
					</div>
				</div>
				<div class="t-min-width--0">
					<div class="">
						<div class="eb-image-source-title t-text--truncate" data-panel-file-source-name></div>
					</div>
					<div class="l-cluster l-spaces--xs">
						<div>
							<div class="eb-image-source-size" data-panel-file-source-size>
							</div>
							<div>&middot;</div>
							<div>
								<a href="javascript:void(0);"
									data-panel-file-source-change-button
									data-eb-mm-browse-button
									data-eb-mm-start-uri="_cG9zdA--"
									data-eb-mm-filter="file"
								>
									<?php echo JText::_('COM_EB_BLOCK_FILE_PANEL_FILE_SOURCE_CHANGE_BUTTON'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'showicon', 'COM_EASYBLOG_BLOCKS_FILE_SHOW_EXTENSION_ICON', true, ['data-file-fieldset-icon']); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'showsize', 'COM_EASYBLOG_BLOCKS_FILE_SHOW_FILE_SIZE', true, ['data-file-fieldset-size']); ?>
	</div>
</div>
