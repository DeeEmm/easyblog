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
<div class="row" data-fd-form-user="<?php echo $this->fd->getName();?>" data-id="<?php echo $id;?>">
	<div class="col-lg-<?php echo $columns;?>">
		<div class="input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="form-control" value="<?php echo $userName;?>" disabled="disabled" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" data-fd-remove>
					<i class="fdi fa fa-times"></i>
				</button>
				<button class="btn btn-default" type="button" data-fd-browse data-id="<?php echo $id;?>">
					<i class="fdi fa fa-search"></i>
					<?php if ($browseTitle) { ?>
					&nbsp; <?php echo JText::_($browseTitle);?>
					<?php } ?>
				</button>
			</span>
		</div>

		<?php echo $this->fd->html('form.hidden', $name, $value, $id, $attributes); ?>
	</div>
</div>
