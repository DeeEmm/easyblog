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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_TEAMBLOGS_GENERAL'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_TEAM_NAME', 'title'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'title', $this->fd->html('str.escape', $team->title), 'title'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS', 'alias'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'alias', $this->fd->html('str.escape', $team->alias), 'alias'); ?>
					</div>
				</div>

				<div class="form-group" style="display: block">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_TEAM_DESCRIPTION', 'write_description'); ?>

					<div class="col-md-12">
						<?php echo $editor->display( 'write_description', $team->description, '100%', '150', '5', '5' , array('article', 'image', 'readmore', 'pagebreak') ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_TEAMBLOGS_OTHERS'); ?>

			<div class="panel-body">

				<?php if ($this->config->get('layout_teamavatar')) { ?>
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_AVATAR', 'avatar'); ?>

					<div class="col-md-7">
						<?php if (!empty($team->avatar)) { ?>
						<img style="border-style:solid; float:none;" src="<?php echo $team->getAvatar(); ?>" width="60" height="60"/><br />
						<?php } ?>

						<div>
							<input class="mt-15" id="avatar" type="file" name="avatar" />
						</div>
					</div>
				</div>
				<?php } ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_CREATED', 'created'); ?>

					<div class="col-md-7">
						<div class="input-group date" data-date-picker>
						

							<?php echo $this->fd->html('form.datetimepicker', 'created', $team->created); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_PUBLISHED', 'published'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'published', is_null($team->published) ? true : $team->published); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_ACCESS', 'access'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.teamAccess', 'access', $team->access); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_ALLOW_JOIN', 'allow_join'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'allow_join', $team->allow_join); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_TEAMBLOGS_SEO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS', 'keywords'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.textarea', 'keywords', $meta->keywords, 'keywords'); ?>

						<div>
							<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS_INSTRUCTIONS'); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION', 'description'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.textarea', 'description', $meta->description, 'description'); ?>
					</div>
				</div>

				<input type="hidden" name="metaid" value="<?php echo $meta->id; ?>" />
			</div>
		</div>
	</div>
</div>

