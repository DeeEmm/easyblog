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
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_FIELDSET_HEADER_GENERAL'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'show_image', 'COM_EASYBLOG_BLOCKS_POST_SHOW_IMAGE', true, array('data-post-option-image')); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'show_intro', 'COM_EASYBLOG_BLOCKS_POST_SHOW_INTRO', true, array('data-post-option-intro')); ?>

		<?php echo $this->html('composer.field', 'composer.field.toggler', 'show_link', 'COM_EASYBLOG_BLOCKS_POST_SHOW_LINK', true, array('data-post-option-link')); ?>
	</div>
</div>
