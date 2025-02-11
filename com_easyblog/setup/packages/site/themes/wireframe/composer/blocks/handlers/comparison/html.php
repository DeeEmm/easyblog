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
<div class="eb-comparison" data-eb-comparison-wrapper>
	<figure class="cd-image-container">
		<img src="<?php echo JURI::root();?>components/com_easyblog/themes/wireframe/images/compare-a.jpg" data-right-image />
		<span class="cd-image-label" data-type="original" data-right-label></span>

		<span data-right-browse-placeholder></span>

		<!-- the resizable image on top -->
		<div class="cd-resize-img" style="background-image: url('<?php echo JURI::root();?>components/com_easyblog/themes/wireframe/images/compare-b.jpg');" data-left-image>
			<span class="cd-image-label" data-type="modified" data-left-label></span>

			<span data-left-browse-placeholder></span>
		</div>

		<span class="cd-handle"></span>
	</figure>
</div>

<div class="t-hidden" data-eb-comparison-template>
	<a href="javascript:void(0);" class="btn btn--sm btn-eb-default eb-comparison__btn-media"
		data-eb-comparison-browse="right"
		data-eb-mm-browse-button
		data-eb-mm-start-uri="_cG9zdA--"
		data-eb-mm-filter="image"
		data-eb-mm-disabled-panels="link-to,image-alignment,image-style"
		data-min-width="480"
		data-min-height="400"
	>
		<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
	</a>

	<a href="javascript:void(0);" class="btn btn--sm btn-eb-default eb-comparison__btn-media"
		data-eb-comparison-browse="left"
		data-eb-mm-browse-button
		data-eb-mm-start-uri="_cG9zdA--"
		data-eb-mm-filter="image"
		data-eb-mm-disabled-panels="link-to,image-alignment,image-style"
		data-min-width="480"
		data-min-height="400"
	>
		<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
	</a>
</div>
