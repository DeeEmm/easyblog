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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-eb-form>

	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', [
			(object) [
				'id' => 'general',
				'title' => 'COM_EASYBLOG_CATEGORIES_EDIT_FORM_TITLE',
				'active' => $active === 'general'
			],
			(object) [
				'id' => 'access',
				'title' => 'COM_EASYBLOG_CATEGORIES_EDIT_FORM_PERMISSIONS',
				'active' => $active === 'access'
			],
			(object) [
				'id' => 'entry',
				'title' => 'COM_EASYBLOG_CATEGORIES_ENTRY_LAYOUT_TAB',
				'active' => $active === 'entry'
			],
			(object) [
				'id' => 'autoposting',
				'title' => 'COM_EB_CATEGORIES_AUTOPOSTING_TAB',
				'active' => $active === 'autoposting'
			]
		]); ?>


		<div class="tab-content">
			<div id="general" class="t-hidden <?php echo $active === 'general' ? 't-block' : '';?>">
				<?php echo $this->output('admin/categories/form/general'); ?>
			</div>

			<div id="entry" class="t-hidden <?php echo $active === 'entry' ? 't-block' : '';?>">
				<?php echo $this->output('admin/categories/form/post'); ?>
			</div>

			<div id="access" class="t-hidden <?php echo $active === 'access' ? 't-block' : '';?>">
				<?php echo $this->output('admin/categories/form/access'); ?>
			</div>

			<div id="autoposting" class="t-hidden <?php echo $active === 'autoposting' ? 't-block' : '';?>">
				<?php echo $this->output('admin/categories/form/autoposting'); ?>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.token'); ?>
	<?php echo $this->fd->html('form.action', 'save'); ?>
	<?php echo $this->fd->html('form.hidden', 'id', $category->id); ?>
	<?php echo $this->fd->html('form.hidden', 'savenew', 0, 'savenew'); ?>
	<?php echo $this->fd->html('form.hidden', 'active', '', '', 'data-fd-active-tab-input'); ?>

</form>
