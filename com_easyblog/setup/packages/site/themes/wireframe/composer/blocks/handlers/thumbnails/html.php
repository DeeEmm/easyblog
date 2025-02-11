<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-thumbs is-empty">
	<div class="eb-thumbs-col"></div>
	<div class="eb-thumbs-col"></div>
	<div class="eb-thumbs-col"></div>
	<div class="eb-thumbs-col"></div>
</div>
<div class="eb-composer-placeholder eb-thumbs-upload-placeholder text-center"
	data-key="_cG9zdA--"
	data-type="image"
	contenteditable="false">
	<div class="eb-composer-placeholder-content" data-plupload-drop-element>
		<i class="eb-composer-placeholder-icon fdi fa fa-th"></i>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS');?></b>
		<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS_INFO');?></p>

		<p data-eb-file-error class="hide eb-composer-placeholder-error t-text--danger"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS_INVALID_FILE');?></p>

		<a href="javascript:void(0);" class="btn btn-eb-default-o btn--sm btn-browse" 
			data-eb-thumbnails-browse
			data-eb-mm-browse-button
			data-eb-mm-start-uri="_cG9zdA--"
			data-eb-mm-filter="image"
			data-eb-mm-browse-place="local"
			data-eb-mm-browse-type="thumbnails"
			data-eb-mm-disabled-panels="link-to,image-alignment,image-style,image-source"
		>
			<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
		</a>

		<?php if ($this->acl->get('upload_image')) { ?>
		<button type="button" class="btn btn--sm btn-eb-primary eb-thumbs-add-thumbnail-button" data-plupload-browse-button>
			<i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_IMAGE');?>
		</button>
		<?php } ?>

		<?php echo $this->output('site/composer/progress'); ?>
	</div>
</div>
