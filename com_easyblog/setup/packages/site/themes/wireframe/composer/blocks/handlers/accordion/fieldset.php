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
	<?php echo $this->html('composer.panel.header', 'COM_EB_BLOCKS_ACCORDION_SECTIONS'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<?php echo $this->html('grid.listbox', 'control', [$sectionTitle], ['attributes' => 'data-accordion-control', 'min' => 1, 'toggleDefault' => true, 'customHTML' => $sectionTitle]); ?>
		</div>
	</div>
</div>
