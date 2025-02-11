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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_ALERTS_TYPE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div class="eb-swatch swatch-grid">
				<div class="row" data-eb-composer-block-alert-type>
					<?php foreach ($types as $type) { ?>
					<div class="col-sm-6">
						<div class="eb-swatch-alert" data-type="<?php echo $type;?>">
							<div class="eb-swatch-preview">
								<div>
									<div class="o-alert o-alert--<?php echo $type;?>" role="alert">
										<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_ALERTS_PREVIEW_TITLE');?></strong>
										<br />
										<?php echo JText::_('COM_EASYBLOG_BLOCKS_ALERTS_PREVIEW_CONTENT');?>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_ALERTS_' . strtoupper($type));?></span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>