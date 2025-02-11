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
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_META_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-group">
						<label for="page_title" class="col-md-5">
							<?php echo JText::_('COM_EASYBLOG_META_TYPE_TITLE'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_META_TYPE_TITLE'); ?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_APIKEY_DESC');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-7">
							<b><?php echo $meta->getTitle(); ?></b>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_META_TAG_ALLOW_INDEXING', 'indexing'); ?>
						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'indexing', $meta->indexing); ?>
						</div>
					</div>

					<?php if ($meta->type == META_TYPE_POST) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_CANONICAL_LINK', 'canonical'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'canonical', $meta->canonical); ?>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_META_TAG_EDIT_KEYWORDS', 'keywords'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.textarea', 'keywords', $meta->keywords, 'keywords'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_META_TAG_EDIT_DESCRIPTION', 'description'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.textarea', 'description', $meta->description, 'description'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $meta->id;?>" />
	<?php echo $this->fd->html('form.action', 'meta.save'); ?>
</form>
