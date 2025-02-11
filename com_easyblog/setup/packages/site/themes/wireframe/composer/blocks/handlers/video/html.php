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
<div class="eb-composer-placeholder eb-composer-placeholder-video text-center"
	data-eb-composer-video-placeholder
	data-key="_cG9zdA--"
	data-type="video"
	contenteditable="false"
	data-plupload-multi-selection="0"
>

	<div data-plupload-drop-element>
		<i class="eb-composer-placeholder-icon fdi fa fa-film"></i>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_PLACEHOLDER_TITLE');?></b>
		<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_PLACEHOLDER_DESC');?></p>

		<p data-eb-file-error class="hide eb-composer-placeholder-error t-text--danger"><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_INVALID_FILE');?></p>

		<button type="button" class="btn btn-eb-default-o btn--sm"
			data-eb-mm-browse-button
			data-eb-mm-start-uri="_cG9zdA--"
			data-eb-mm-filter="video"
		>
			<?php echo JText::_('COM_EASYBLOG_BLOCKS_BROWSE_MEDIA'); ?>
		</button>

		<span class="eb-plupload-btn">
			<button type="button" class="btn btn-eb-primary btn--sm" data-plupload-browse-button>
				<i class="fdi fa fa-upload"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_FILE');?>
			</button>
		</span>

		<?php echo $this->output('site/composer/progress'); ?>

		<?php echo $this->output('site/composer/blocks/error'); ?>
	</div>
</div>
