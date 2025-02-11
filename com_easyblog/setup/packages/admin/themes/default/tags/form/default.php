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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_TAG_FORM_GENERAL');?>

				<div class="panel-body">
					<div class="form-group">
						<label for="title" class="col-md-5">
							<?php echo JText::_('COM_EASYBLOG_TAG_TITLE'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TAG_TITLE'); ?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_TAG_TITLE_TIPS');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'title', $this->fd->html('str.escape', $tag->title), 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="alias" class="col-md-5">
							<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS_TIPS');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'alias', $this->fd->html('str.escape', $tag->alias), 'alias'); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="alias" class="col-md-5">
							<?php echo JText::_('COM_EB_TAG_DESCRIPTION'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EB_TAG_DESCRIPTION'); ?>"
								data-content="<?php echo JText::_('COM_EB_TAG_DESCRIPTION_TIPS');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.textarea', 'description', $this->fd->html('str.escape', $tag->description), 'description'); ?>
						</div>
					</div>

					<?php if ($this->config->get('main_multi_language')) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_TAGS_LANGUAGE', 'language'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.languages', 'language', $tag->language); ?>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label for="published" class="col-md-5">
							<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_TAG_PUBLISH_TIPS');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'published', $tag->published); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="default" class="col-md-5">
							<?php echo JText::_('COM_EASYBLOG_DEFAULT_TAG'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_DEFAULT_TAG'); ?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_TAG_DEFAULT_TIPS');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'default', $tag->default); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="id" value="<?php echo $tag->id;?>" />
</form>
