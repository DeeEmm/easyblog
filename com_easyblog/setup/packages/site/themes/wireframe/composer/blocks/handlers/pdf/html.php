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
<div class="eb-composer-placeholder eb-composer-placeholder-pdf text-center" data-eb-composer-pdf-placeholder data-key="_cG9zdA--" data-type="pdf" contenteditable="false" data-plupload-multi-selection="0">

	<div data-plupload-drop-element>
		<i class="eb-composer-placeholder-icon fdi far fa-file-pdf"></i>
		<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_PDF_PLACEHOLDER_TITLE');?></b>
		<p class="eb-composer-placeholder-brief"></p>

		<button type="button" class="btn btn-eb-default-o btn--sm" data-eb-mm-browse-button data-eb-mm-start-uri="_cG9zdA--" data-eb-mm-filter="file">
			<?php echo JText::_('COM_EASYBLOG_BLOCKS_BROWSE_MEDIA'); ?>
		</button>

		<span class="eb-plupload-btn">
			<button type="button" class="btn btn-eb-primary btn--sm" data-plupload-browse-button>
				<i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_FILE');?>
			</button>
		</span>

		<?php echo $this->output('site/composer/progress'); ?>

		<?php echo $this->output('site/composer/blocks/error'); ?>
	</div>
</div>
