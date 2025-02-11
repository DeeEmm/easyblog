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
<div class="eb-share">
	<div class="eb-share-buttons<?php echo ' is-' . $this->config->get('social_button_size'); ?> <?php echo FH::getCurrentLanguageTag() == 'en-GB' ? 'is-english': ''; ?>">

		<?php foreach ($buttons as $button) { ?>
		<div class="eb-share-<?php echo $button->getName();?>">
			<?php echo $button->html(); ?>
		</div>		
		<?php } ?>
	</div>
</div>
