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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_BEFORE_YOU_MIGRATE', '', '/administrators/migrations/migrating-from-wordpress'); ?>

			<div class="panel-body">
				<p>Before running the migrator, please ensure that:</p>
				<ul style="padding: 0 0 0 10px; margin: 0;">
					<li class="mb-10">
						Backup your existing database just in case anything goes wrong.
					</li>
					<li class="mb-10">
						<p>You have copied the xml export file into the following folder:</p>
						<code><?php echo JPATH_COMPONENT;?>/xmlfiles</code>
					</li>
					<li class="mb-10">
						From the options below, select an author to be associated with these imported posts.
						The default user will be the site administrator.
					</li>
					<li class="mb-10">
						Please wait for the migration process to be completed before moving to another page.
					</li>
					<li class="mb-10">
						As the xml files doesn't contain any image files, you will have to manually copy the files over from Wordpress.
					</li>
				</ul>

				<?php if ($files) { ?>
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_WP_WORDPRESS_XML_FILE', 'wpxmlfiles'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'wpxmlfiles', '', call_user_func(function() use($files) {
							$data = [];

							foreach ($files as $file) {
								$data[$file] = $file;
							}

							return $data;
						}), ['id' => 'wpxmlfiles', 'attr' => 'data-xml-wordpress']); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_MIGRATOR_WP_BLOG_IMPORT_AS', 'authorid'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.user', 'authorid', EB::getDefaultSAIds(), 'authorid', ['attributes' => 'data-author-id']); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_MIGRATOR_WP_FIRST_IMAGE_COVER', 'firstimagecover'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'firstimagecover', false, '', 'data-firstimage-cover'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_MIGRATOR_WP_IMAGE_PATH', 'wpimagepath_from'); ?>

					<div class="col-md-7">
						<?php echo JText::_('COM_EB_MIGRATOR_WP_IMAGE_PATH_FROM'); ?>
						<?php echo $this->fd->html('form.text', 'wpimagepathfrom', '', '', array('attr' => 'data-wpimagepath-from', 'placeholder' => JText::_('COM_EB_MIGRATOR_WP_IMAGE_PATH_FROM_PLACEHOLDER'))); ?>
						<br ?>
						<?php echo JText::_('COM_EB_MIGRATOR_WP_IMAGE_PATH_TO'); ?>
						<?php echo $this->fd->html('form.text', 'wpimagepathto', '', '', array('attr' => 'data-wpimagepath-to', 'placeholder' => JText::_('COM_EB_MIGRATOR_WP_IMAGE_PATH_TO_PLACEHOLDER'))); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_MIGRATOR_WP_WRAP_WITH_PARAGRAPH', 'wraptext'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'wraptext', false, '', 'data-paragraph-wrap'); ?>
					</div>
				</div>


				<div class="mt-20 text-right">
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-migrate-xmlwp><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></a>
				</div>
				<?php } else { ?>
					<div class="o-alert o-alert--danger mt-20">There are no export files found in the folder. To be able to migrate from Wordpress, you need to upload the xml file into the path specified above.</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<?php if ($files) { ?>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_PROGRESS'); ?>

			<div class="panel-body">
				<div data-progress-empty><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div data-progress-status style="overflow:auto; height:98%;max-height:300px;"></div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_MIGRATOR_STATISTIC'); ?>

			<div class="panel-body">
				<div data-progress-stat style="overflow:auto; height:98%;"></div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<input type="hidden" name="layout" value="wordpress" />
<input type="hidden" name="current" value="0" data-migrate-current />
