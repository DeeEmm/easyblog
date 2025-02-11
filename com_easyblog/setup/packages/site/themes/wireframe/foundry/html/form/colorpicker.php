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
<style type="text/css">
body .minicolors-swatch.minicolors-sprite.minicolors-input-swatch {
	top: 7px;
}
</style>

<div data-fd-colorpicker="<?php echo $this->fd->getName();?>">
	<input type="text" name="<?php echo $name;?>" class="form-control minicolors hex minicolors-input" value="<?php echo $value; ?>" style="padding-left: 30px;" />

	<?php if ($revert) { ?>
		<?php echo $this->fd->html('button.link', null, '<i class="fdi fa fa-undo"></i>', 'default', 'default', ['attributes' => 'data-fd-colorpicker-revert data-color="' . $revert . '"']); ?>
	<?php } ?>
</div>
