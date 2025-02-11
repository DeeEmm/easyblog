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
<div class="ebd-workarea show-guide is-loading" data-ebd-workarea>
	<div class="ebd">
		<?php if ($templateEditor && ($postTemplate->id || $postTemplate->data)) { ?>
			<?php echo $postTemplate->getDocument()->getEditableContent(); ?>
		<?php } else { ?>
			<?php if (!$templateEditor && ($post->isNew() && $singleTemplate) && !$post->isDraft()) { ?>
				<?php echo $singleTemplate->getDocument()->getEditableContent($postTemplateIsLocked); ?>
			<?php } else { ?>
				<?php echo $post->renderEditorContent($postTemplateIsLocked); ?>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="ebd-block is-dropzone hide" data-ebd-dropzone data-ebd-dropzone-placeholder>
		<div>
			<span class="droptext"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_DROP_BLOCK_HERE');?></span>
		</div>
	</div>
</div>
