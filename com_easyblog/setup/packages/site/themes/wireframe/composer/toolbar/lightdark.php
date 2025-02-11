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
<div class="eb-lightdark-switch <?php echo $appearance == 'light' ? 'is-light' : 'is-dark';?>" data-composer-appearance>
	<div class="eb-lightdark-switch__sun t-px--xs">
		<i class="fdi far fa-sun fa-fw"></i>
	</div>
	<div class="o-onoffswitch">
		<input type="checkbox" id="composer-appearance" class="o-onoffswitch__checkbox" value="1" data-composer-appearance-input <?php echo $appearance == 'dark' ? 'checked="checked"' : '';?>>
		<label class="o-onoffswitch__label t-mb--no" for="composer-appearance"></label>

		<input type="hidden" name="light-dark" value="1">
	</div>
	<div class="eb-lightdark-switch__moon t-px--xs">
		<i class="fdi far fa-moon fa-fw "></i>
	</div>
</div>
