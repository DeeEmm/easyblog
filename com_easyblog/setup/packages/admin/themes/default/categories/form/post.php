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
<div class="row mb-15">
	<div class="col-lg-12">
		<div style="background: #fff; line-height: 1; padding: 16px; border: 1px solid #d7d7d7;">
			<div class="eb-checkbox">
				<input id="checkbox-entry" type="checkbox" name="params[inherited]" value="1" <?php echo $inheritedParams ? 'checked="checked"' : '';?> data-category-inherit />
				<label for="checkbox-entry"></label>
			</div>

			<?php echo JText::sprintf('COM_EASYBLOG_CATEGORY_FORM_INHERIT_LABEL', 'index.php?option=com_easyblog&view=settings&layout=views&active=entry');?>
		</div>
	</div>
</div>

<div class="<?php echo $inheritedParams ? 'hide' : '';?>" data-category-post-options>
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', $manifest['post']->label, $manifest['post']->info); ?>

				<div class="panel-body">
					<?php echo $this->output('admin/categories/form/fields/renderer', ['fields' => $manifest['post']->fields, 'params' => $params]); ?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', $manifest['author']->label, $manifest['author']->info); ?>

				<div class="panel-body">
					<?php echo $this->output('admin/categories/form/fields/renderer', array('fields' => $manifest['author']->fields, 'params' => $params)); ?>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->fd->html('panel.heading', $manifest['related']->label, $manifest['related']->info); ?>

				<div class="panel-body">
					<?php echo $this->output('admin/categories/form/fields/renderer', array('fields' => $manifest['related']->fields, 'params' => $params)); ?>
				</div>
			</div>
		</div>
	</div>
	<?php //echo $form;?>
</div>
