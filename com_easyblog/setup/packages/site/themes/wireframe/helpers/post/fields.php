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
<div class="eb-entry-fields">
	<?php foreach ($fields as $field) { ?>
		<?php if ($field->group->hasValues($post)) { ?>
		<h4 class="eb-section-heading reset-heading"><?php echo $field->group->getTitle();?></h4>
		<ul class="eb-fields-list reset-list <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
			<?php foreach ($field->fields as $customField) { ?>

				<?php 
					// retrieve field value
					$fieldValue = $customField->getDisplay($post);
					
					// retrieve custom class value for these field
					$fieldClassValue = $customField->getFieldClassValue($customField->id, $post->id);
				?>

				<?php if ($fieldValue) { ?>
				<li class="field-<?php echo strtolower($customField->type);?> <?php echo $fieldClassValue ? $fieldClassValue : ''; ?>">
					<label><?php echo JText::_($customField->title); ?></label>

					<?php if ($customField->type != 'heading') { ?>
					<div><?php echo $fieldValue;?></div>
					<?php } ?>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
		<?php } ?>
	<?php } ?>
</div>
