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
<div class="row" data-fd-article="<?php echo $this->fd->getName();?>" data-id="<?php echo $id;?>">
	<div class="col-lg-10">
		<div class="input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="form-control" value="<?php echo $articleTitle;?>" disabled="disabled" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" data-fd-article-remove>
					<i class="fdi fa fa-times"></i>
				</button>
				<button class="btn btn-default" type="button" data-fd-article-browse>
					<i class="fdi fa fa-search"></i>&nbsp; <?php echo JText::_('FD_BROWSE'); ?>
				</button>
			</span>
		</div>
		<input type="hidden" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" <?php echo $attributes; ?> />
	</div>
</div>
