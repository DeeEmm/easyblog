<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_POST_TEMPLATES_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_POST_TEMPLATES_TITLE', 'title');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'title', $template->title, 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_POST_TEMPLATES_PUBLISHED', 'published');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'published', $template->published, 'published');?>
						</div>
					</div>

					<?php if (!$template->isBlank()) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_POST_TEMPLATES_SYSTEM', 'system');?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'system', $template->system, 'system');?>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_POST_TEMPLATES_THUMBNAIL', 'thumbnail');?>

						<div class="col-md-7" data-post-template data-id="<?php echo $template->id; ?>" data-default-thumbnail="<?php echo $template->getDefaultThumbnails(true); ?>">
							<div class="mb-20">
								<div class="eb-img-holder">
									<div class="eb-img-holder__remove" data-thumbnail-restore-default-wrap <?php echo $template->hasOverrideThumbnails() ? '' : 'style="display: none;'; ?>>
										<a href="javascript:void(0);" class="" data-thumbnail-restore-default-button>
											<i class="fdi fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REMOVE'); ?>
										</a>
									</div>
									<img src="<?php echo $template->getThumbnails(true);?>" width="200" data-thumbnail-image />
								</div>
								
							</div>

							<div>
								<input type="file" name="screenshot" />
							</div>
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
