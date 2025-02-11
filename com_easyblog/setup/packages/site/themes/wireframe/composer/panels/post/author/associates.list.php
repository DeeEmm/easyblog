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
<div class="eb-composer-pick-item"
	data-avatar="<?php echo $associate->avatar;?>"
	data-id="<?php echo $associate->source_id;?>"
	data-type="<?php echo $associate->source_type;?>"
	data-title="<?php echo $associate->title;?>"
	data-associates-item
>
	<div class="eb-radio">
		<input type="radio" name="radio-associates" id="radio-<?php echo $associate->type;?>-<?php echo $associate->source_type; ?><?php echo $associate->source_id;?>" value="<?php echo $associate->source_id;?>" data-associates-checkbox
		<?php echo $associate->source_id == $source_id && $associate->source_type == $source_type ? ' checked="checked"' : '';?>
		/>
		<label for="radio-<?php echo $associate->type;?>-<?php echo $associate->source_type; ?><?php echo $associate->source_id;?>" class="row-table">
			<div class="col-cell">
				<img src="<?php echo $associate->avatar;?>" class="avatar" width="30" height="30" />
			</div>
			<div class="col-cell">
				<?php echo $associate->title;?>
			</div>
		</label>
	</div>
</div>
