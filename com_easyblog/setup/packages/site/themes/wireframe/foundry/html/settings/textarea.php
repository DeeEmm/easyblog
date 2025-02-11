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
<div class="form-group">
	<?php echo $this->fd->html('form.label', JText::_($title), $name, JText::_($title), JText::_($desc)); ?>

	<div class="col-md-7">
		<textarea class="form-control" name="<?php echo $name;?>"><?php echo $this->fd->config()->get($name);?></textarea>

		<?php if ($instructions) { ?>
		<div class="o-alert o-alert--warning mt-10">
			<?php echo JText::_($instructions);?>
		</div>
		<?php } ?>
	</div>
</div>
