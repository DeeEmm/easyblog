<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (!$postTemplate->isBlank()) { ?>
	<input class="o-form-control mb-10" name="template_title" placeholder="<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_TITLE_PLACEHOLDER');?>" value="<?php echo $this->fd->html('str.escape', $postTemplate->title);?>" />

	<?php if ($postTemplate->id) { ?>
	<a href="javascript:void(0);" class="btn btn-eb-primary btn--lg btn-block btn-update" data-composer-save-template>
		<?php echo JText::_('COM_EASYBLOG_UPDATE_TEMPLATE'); ?>
	</a>
	<?php } else { ?>
	<a href="javascript:void(0);" class="btn btn-eb-primary btn--lg btn-block btn-update" data-composer-save-template>
		<?php echo JText::_('COM_EASYBLOG_CREATE_TEMPLATE'); ?>
	</a>
	<?php } ?>
	<div class="t-text--left">
		<div class="eb-checkbox mt-10">
			<input type="checkbox" id="system" name="system" value="1" data-template-system <?php echo $postTemplate->system ? ' checked="checked"' : '';?> />
			<label for="system">
				<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_AS_GLOBAL'); ?>
			</label>
		</div>

		<?php if ($postTemplate->canLock()) { ?>
		<div class="eb-checkbox mt-10">
			<input type="checkbox" id="lock" name="lock" value="1" <?php echo $postTemplate->isLocked() ? ' checked="checked"' : '';?> />
			<label for="lock">
				<?php echo JText::_('COM_EB_LOCK_THIS_POST_TEMPLATE'); ?>
			</label>
		</div>
		<?php } ?>
	</div>

	<?php if ($postTemplate->canDelete()) { ?>
	<div class="eb-side-main-action__ft t-lg-mt--md">
		<div>
			<a href="javascript:void(0);" class="t-text--danger" data-composer-delete-template>
				<?php echo JText::_('COM_EASYBLOG_DELETE_TEMPLATE'); ?>
			</a>
		</div>
	</div>
	<?php } ?>
<?php } else { ?>
	<div>
		<?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_BLANK_TEMPLATES_DESC'); ?>
	</div>
<?php } ?>
