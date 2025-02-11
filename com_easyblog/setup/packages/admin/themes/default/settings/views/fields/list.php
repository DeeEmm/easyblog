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
<div class="row">
	<div class="col-lg-8">
		<?php
			$options = [];

			foreach ($field->options as $option) {
				// Since this is already the global settings, it cannot be inherited any further
				if ($option->value == -1) {
					continue;
				}

				$options[$option->value] = $option->label;
			}
		?>
		<?php echo $this->fd->html('form.dropdown', $configKey, $this->config->get($configKey), $options); ?>
	</div>
</div>
