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
<div class="col-sm-4">
	<div class="eb-swatch-item" data-eb-image-style-selection data-value="<?php echo $style;?>">
		<div class="eb-swatch-preview is-responsive">
			<div>
				<div class="eb-image style-<?php echo $cssClass;?>">
					<div class="eb-image-figure"><div></div></div>
					<div class="eb-image-caption"><span>— ‑</span></div>
				</div>
			</div>
		</div>
		<div class="eb-swatch-label">
			<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_' . strtoupper($style));?></span>
		</div>
	</div>
</div>
