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
<div class="input-group">
	<input type="text" id="<?php echo $id;?>" <?php echo $attributes;?> class="form-control" value="<?php echo $value;?>" style="pointer-events:none;" />
	<span class="input-group-btn"
		data-fd-copy="<?php echo $this->fd->getName();?>"
		data-original-title="<?php echo $tooltips->copy;?>"
		data-copied="<?php echo $tooltips->copied;?>"
		data-copy="<?php echo $tooltips->copy;?>"
		data-placement="bottom"
		data-<?php echo $this->fd->getComponentShortName();?>-provide="tooltip"
	>
		<a href="javascript:void(0);" class="btn btn-default">
			<i class="fdi far fa-clipboard"></i>
		</a>
	</span>
</div>
