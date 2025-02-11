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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section data-name="module">
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_MODULES'); ?>

	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.panel.help', 'COM_EB_BLOCKS_MODULE_HELP'); ?>

		<div class="t-lg-mt--lg o-form-group eb-image-variation-field" data-eb-module-field>
			<div class="eb-image-variation-list-container" data-eb-module-list-container>
				<div class="eb-list eb-module-list" style="border-bottom: none; border-top: none;" data-module-list>
					<?php foreach($data->installedModules as $module) { ?>
					<div class="eb-list-item eb-mm-variation-item"
						 data-module-item
						 data-module="<?php echo $module->module; ?>"
						 data-title="<?php echo $module->title; ?>"
						 data-id="<?php echo $module->id; ?>">
						<div>
							<i class="fdi fa fa-cube"></i>
							<span data-title><?php echo $module->title ?> (<?php echo $module->position ? $module->position : JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MODULES_NO_POSITION'); ?>)</span>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>





