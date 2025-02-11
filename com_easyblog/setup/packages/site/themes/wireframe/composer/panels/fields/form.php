<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($fields) { ?>
<div class="eb-composer-fieldset t-pb--no" data-eb-composer-panel-fields data-panel-field data-group-id="<?php echo $group->id;?>" data-category-id="<?php echo $id;?>">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_($group->title);?></strong>
	</div>
</div>

	<?php foreach ($fields as $field) {
		$customFieldValue = $field->getDisplay($post);

		// those custom field already have the default value by default
		if ($field->type == 'heading' || $field->type == 'select') {
			$customFieldValue = true;
		}
	?>
	<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-name="" data-eb-composer-block-section="">
		<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
			<strong>
				<?php if ($field->required) { ?>
				<span class="required">*</span>
				<?php } ?>
				<?php echo $field->getTitle(); ?>
			</strong>
			<i class="eb-composer-fieldset-header__icon" data-panel-icon></i>
		</div>

		<div class="eb-composer-fieldset-content">
			<div class="o-form-group" data-wrapper-field-class>
				<div class="o-control-input l-stack l-spaces--sm">
					<?php echo $field->getForm($post, 'fields');?>
				</div>
			</div>

			<div class="t-text--muted">
				<?php echo $field->getHelp();?>
			</div>

			<div class="o-form-group <?php echo $customFieldValue ? '' : 'hide' ;?>" data-field-type-<?php echo $field->type; ?>>
				<label class="o-control-label">
					<?php echo JText::_('COM_EB_COMPOSER_FIELD_CLASS_PREFIX'); ?>
					<i data-html="true" data-placement="top"
						data-title="<?php echo JText::_('COM_EB_COMPOSER_FIELD_CLASS_PREFIX'); ?>"
						data-content="<?php echo JText::_('COM_EB_COMPOSER_FIELD_CLASS_PREFIX_DESC'); ?>" data-eb-provide="popover" class="fdi fa fa-question-circle"></i>
				</label>

				<div class="o-control-input">
					<?php echo $field->getClassForm($post, 'fields');?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } ?>
