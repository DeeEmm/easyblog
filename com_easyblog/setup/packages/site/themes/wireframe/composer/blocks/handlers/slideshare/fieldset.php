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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_BLOCKS_SLIDESHARE_URL'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" data-fs-slideshare-source value="" class="o-form-control" />
				<span class="o-input-group__btn">
					<a data-fs-slideshare-update="" class="btn btn-eb-primary" href="javascript:void(0);">
						<?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?>
					</a>
				</span>
			</div>
		</div>
	</div>
</div>
