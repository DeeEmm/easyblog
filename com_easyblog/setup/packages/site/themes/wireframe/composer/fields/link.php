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

if (!isset($classname)) {
	$classname = '';
} else {
	$classname = ' ' . $classname;
}

if (!isset($url)) $url = '';
if (!isset($title)) $title = '';
if (!isset($preview)) $preview = '';
if (!isset($openInNewPage)) $openInNewPage = false;
?>
<div class="eb-link-item<?php echo $classname; ?>" data-type="link" data-eb-link data-eb-link-item>
	<div class="eb-link-preview" data-eb-link-preview>
		<div class="t-d--flex">
			<span class="eb-link-preview-caption t-bg--primary-100 t-text--primary-400 t-rounded--lg t-px--xs t-text--truncate" data-eb-link-preview-caption><?php echo $preview; ?></span>
			<div class="t-ml--auto">
				<button type="button" class="t-text--danger t-border--0 t-bg--transparent" data-eb-link-remove-button>
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_REMOVE'); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="eb-link-input">
		<input class="eb-link-url-field o-form-control" type="text" value="<?php echo $url; ?>" placeholder="https://" data-eb-link-url-field>
		<textarea class="eb-link-title-field o-form-control" placeholder="<?php echo JText::_('Enter link description'); ?>" data-eb-link-title-field><?php echo $title; ?></textarea>
	</div>

	<div class="eb-link-actions">
	<?php if ($this->config->get('main_anchor_nofollow')) { ?>
		<?php echo $this->output('site/composer/fields/checkbox', array(
			'classname' => 'eb-link-blank-option',
			'attributes' => 'data-eb-link-follow-option',
			'label' => JText::_('COM_EB_COMPOSER_BLOCKS_BUTTON_ATTRIBUTE_FOLLOW'),
			'checked' => $openInNewPage
		)); ?>
	<?php } else { ?>
		<?php echo $this->output('site/composer/fields/checkbox', array(
			'classname' => 'eb-link-blank-option',
			'attributes' => 'data-eb-link-nofollow-option',
			'label' => JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_BUTTON_ATTRIBUTE_NOFOLLOW'),
			'checked' => $openInNewPage
		)); ?>
	<?php } ?>
	</div>

	<div class="eb-link-actions">
		<?php echo $this->output('site/composer/fields/checkbox', array(
			'classname' => 'eb-link-blank-option',
			'attributes' => 'data-eb-link-blank-option',
			'label' => JText::_('COM_EASYBLOG_COMPOSER_OPEN_IN_NEW_PAGE'),
			'checked' => $openInNewPage
		)); ?>

	</div>
</div>
