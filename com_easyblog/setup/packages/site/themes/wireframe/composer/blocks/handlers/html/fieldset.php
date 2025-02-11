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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_BLOCKS_HTML'); ?>

	<div class="eb-composer-fieldset-content">
		<pre data-eb-composer-blocks-html-pre></pre>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_BLOCKS_HTML_AMP_IMAGE_SIZE'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_BLOCKS_HTML_AMP_IMAGE_SIZE_HELP'); ?>

		<?php echo $this->html('composer.field', 'composer.field.text', 'imageWidth', 'COM_EASYBLOG_COMPOSER_FIELDS_WIDTH', '', 'data-html-width'); ?>

		<?php echo $this->html('composer.field', 'composer.field.text', 'imageHeight', 'COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT', '', 'data-html-height'); ?>
	</div>
</div>
