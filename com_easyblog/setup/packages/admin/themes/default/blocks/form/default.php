<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EB_BLOCKS_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCKS_TITLE', 'title', '', '', false); ?>

						<div class="col-md-7">
							<?php echo JText::_($block->title);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCKS_DESCRIPTION', 'desc', '', '', false); ?>

						<div class="col-md-7">
							<?php echo JText::_($block->description);?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCKS_PUBLISHED', 'published'); ?>

						<div class="col-md-7">
							<?php if ($block->published == 2) { ?>
								<?php echo JText::_('COM_EASYBLOG_BLOCKS_CORE_BLOCK'); ?>
							<?php } else { ?>
								<?php echo $this->fd->html('form.toggler', 'published', $block->published); ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<?php if ($forms) { ?>
				<div class="panel">
					<?php echo $this->fd->html('panel.heading', 'General', 'General settings for the block is available under this section'); ?>

					<div class="panel-body">
						<?php foreach ($forms as $form) { ?>
						<div class="form-group">
							<?php echo $this->fd->html('form.label', $form->label, 'params[' . $form->name . ']'); ?>

							<div class="col-md-7">
								<?php if ($form->type == 'dropdown') { ?>
									<?php echo $this->fd->html('form.dropdown', 'params[' . $form->name . ']', $params->get('default', 'h2'), $form->options); ?>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="id" value="<?php echo $block->id;?>" />

</form>
