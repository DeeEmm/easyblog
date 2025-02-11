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
<div class="o-control-input t-w--100" data-calendar-wrapper="<?php echo $hash;?>">
	<div class="eb-sidebar-form-date">
		<input type="text"
			class="eb-sidebar-form-date__input"
			name="input_<?php echo $name;?>"
			id="<?php echo $id;?>"
			placeholder="<?php echo $placeholder; ?>"
			maxlength="16"
			value="<?php echo $displayValue;?>"
			data-input
		/>

		<div class="eb-sidebar-form-date__icon">
			<a href="javascript:void(0);" class="t-text--500 t-hidden" data-cancel>
				<i class="fdi fa fa-undo"></i>
			</a>

			<a href="javascript:void(0);" class="t-text--500 ml-5 t-hidden" data-remove>
				<i class="fdi fa fa-times"></i>
			</a>

			<a href="javascript:void(0);" class="t-text--500 ml-5" data-calendar>
				<i class="fdi far fa-calendar-alt"></i>
			</a>
		</div>
	</div>

	<input type="hidden" name="<?php echo $name;?>" value="<?php echo $value;?>" data-datetime />
	<div data-field-error class="t-text--danger t-hidden" style="margin-top: 5px;"></div>
</div>