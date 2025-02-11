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
<div class="eb-settings-info">
	<i class="fdi fa fa-info-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SETTINGS_ENTRY_LAYOUT_NOTE');?>
</div>

<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', $fieldsets->entry['post']->label, $fieldsets->entry['post']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->entry['post']->fields, 'key' => 'layout')); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', $fieldsets->entry['author']->label, $fieldsets->entry['author']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->entry['author']->fields, 'key' => 'layout')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', $fieldsets->entry['related']->label, $fieldsets->entry['related']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->entry['related']->fields, 'key' => 'layout')); ?>
			</div>
		</div>
	</div>
</div>
