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
<div class="form-group <?php echo $wrapperClass;?>" <?php echo $wrapperAttributes;?>>
	<?php echo $this->fd->html('form.label', $title, $name, JText::_($title), JText::_($desc)); ?>

	<div class="col-md-7">
		<?php echo $this->fd->html('form.dropdown', $name, $this->fd->config()->get($name), $options, ['attr' => $attributes]);?>

		<?php if ($note) { ?>
		<div class="t-mt--md">
			<?php echo $note;?>
		</div>
		<?php } ?>
	</div>
</div>