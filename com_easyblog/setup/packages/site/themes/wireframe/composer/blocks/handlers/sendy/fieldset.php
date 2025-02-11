<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('Newsletter Attributes (Required)'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.text', 'url', 'URL to Sendy Installation', '', null, array('placeholder' => 'https://newsletter.site.com', 'attr' => 'data-field-sendy-link')); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'list_id', 'List ID', '', null, array('placeholder' => 'Enter the list id from Sendy', 'attr' => 'data-field-sendy-id')); ?>
	</div>
</div>

<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('Info'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'title', 'Display Title', true, 'data-field-sendy-title-toggle'); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'title_text', 'Title', '', null, array('placeholder' => 'Enter the title that should appear in the block', 'attr' => 'data-field-sendy-title')); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'info', 'Display Info', true, 'data-field-sendy-info-toggle'); ?>
		<?php echo $this->html('composer.field', 'composer.field.textarea', 'info_text', 'Info', '', null, array('placeholder' => 'Enter the title that should appear in the block', 'attr' => 'data-field-sendy-info')); ?>
	</div>
</div>

<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('Form'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'name', 'Display Name Field', true, 'data-field-sendy-name-toggle'); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'email_placeholder', 'E-mail Placeholder', '', null, array('placeholder' => 'Enter a placeholder for this input', 'attr' => 'data-field-sendy-email-placeholder')); ?>
		<?php echo $this->html('composer.field', 'composer.field.text', 'name_placeholder', 'Name Placeholder', '', null, array('placeholder' => 'Enter a placeholder for this input', 'attr' => 'data-field-sendy-name-placeholder')); ?>

		<?php echo $this->html('composer.field', 'composer.field.text', 'button', 'Button Label', '', null, array('placeholder' => 'Enter the label for the button', 'attr' => 'data-field-sendy-button')); ?>
	</div>
</div>
