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

<?php if ($size) { ?>
<div class="grid grid-cols-12">
	<div class="col-span-12 md:col-span-<?php echo $size;?>">
<?php } ?>

	<?php if ($help || $prefix || $postfix) { ?>
	<div class="input-group">
	<?php }?>

		<?php if ($prefix) { ?>
		<span class="input-group-addon">
			<?php echo JText::_($prefix); ?>
		</span>
		<?php } ?>

			<input type="text"
				name="<?php echo $name;?>"
				<?php echo $id ? 'id="' . $id . '"' : '';?>
				class="<?php echo $baseClass ? $baseClass : 'form-control'; ?> <?php echo str_ireplace(['o-form-control', 'form-control'], '', $class);?>"
				value="<?php echo $this->fd->html('str.escape', $value);?>"
				<?php echo $placeholder ? 'placeholder="' . JText::_($placeholder) . '"' : '';?>
				<?php echo $attributes;?>
				<?php echo $disabled ? 'disabled="disabled"' : ''; ?>
			/>

		<?php if ($postfix) { ?>
		<span class="input-group-addon">
			<?php echo JText::_($postfix); ?>
		</span>
		<?php } ?>

		<?php if ($help) { ?>
		<span class="input-group-btn">
			<a href="<?php echo $help;?>" target="_blank" class="btn btn-default">
				<i class="fdi fa fa-life-ring"></i>
			</a>
		</span>
		<?php } ?>

	<?php if ($help || $prefix || $postfix) { ?>
	</div>
	<?php } ?>

<?php if ($size) { ?>
	</div>
</div>
<?php } ?>
