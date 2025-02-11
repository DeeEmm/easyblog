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
	<?php echo $this->html('composer.panel.header', 'COM_EB_BLOCKS_RULE_STYLE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group" data-eb-composer-rule-style>
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<?php foreach ($styles as $style) { ?>
					<div class="col-sm-6">
						<div class="eb-swatch-item eb-composer-quote-preview selected active" data-style="eb-block-rule-<?php echo $style;?>">
							<div class="eb-swatch-preview">
								<hr class="eb-block-rule-<?php echo $style;?>" />
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EB_BLOCKS_RULE_' . strtoupper($style));?></span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>