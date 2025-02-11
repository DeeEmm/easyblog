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
		$options = [
			'default' => 'COM_EB_FRONTPAGE_LAYOUT_STANDARD_OPTION',
			'card' => 'COM_EB_FRONTPAGE_LAYOUT_CARD_OPTION',
			'simple' => 'COM_EB_LAYOUT_SIMPLE_OPTION',
			'nickel' => 'COM_EB_LAYOUT_NICKEL_OPTION'
		];
		?>
		<?php echo $this->fd->html('form.dropdown', $configKey, $this->config->get($configKey), $options); ?>
	</div>
</div>
