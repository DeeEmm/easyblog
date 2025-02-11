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
	<?php echo $this->fd->html('form.label', $title, $name, JText::_($title), JText::_($desc)); ?>

	<div class="col-md-7">
		<?php if ($size) { ?>
		<div class="row">
			<div class="col-sm-<?php echo $size;?>">
		<?php } ?>

			<?php if ($prefix || $postfix) { ?>
			<div class="input-group">
			<?php }?>

				<?php if ($prefix) { ?>
				<span class="input-group-addon">
					<?php echo JText::_($prefix); ?>
				</span>
				<?php } ?>

				<?php echo $this->fd->html('form.password', $name, $this->fd->config()->get($name), $name, [
					'attributes' => $attributes,
					'class' => 'form-control ' . $class
				]); ?>

				<?php if ($postfix) { ?>
				<span class="input-group-addon">
					<?php echo JText::_($postfix); ?>
				</span>
				<?php } ?>

			<?php if ($prefix || $postfix) { ?>
			</div>
			<?php } ?>

		<?php if ($size) { ?>
			</div>
		</div>
		<?php } ?>

		<?php if ($instructions) { ?>
		<div class="o-alert o-alert--warning mt-10">
			<?php echo $instructions;?>
		</div>
		<?php } ?>
	</div>
</div>
