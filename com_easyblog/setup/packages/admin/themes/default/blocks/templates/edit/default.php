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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-grid-eb>
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EB_BLOCK_TEMPLATES_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCK_TEMPLATES_TITLE', 'title');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'title', $template->title, 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCKS_TEMPLATES_DESCRIPTION', 'description'); ?>

						<div class="col-md-7">
							<textarea name="description" id="help" class="form-control"><?php echo $this->fd->html('str.escape', $template->description);?></textarea>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCK_TEMPLATES_PUBLISHED', 'published');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'published', $template->published, 'published');?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_BLOCK_TEMPLATES_GLOBAL', 'global');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'global', $template->global, 'global');?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $template->id;?>" />

	<?php echo $this->fd->html('form.action', '', '', ''); ?>
</form>
