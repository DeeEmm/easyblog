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
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm" enctype="multipart/form-data">

	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<?php foreach ($tabs as $tab) { ?>
			<div id="<?php echo $tab->id;?>" class="t-hidden <?php echo $tab->active ? 't-block' : '';?>" data-tab-content>
				<?php echo $tab->contents;?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->fd->html('admin.toolbarSearch'); ?>

	<?php echo $this->fd->html('admin.toolbarActions', 'COM_EB_OTHER_ACTIONS', [
		(object) [
			'title' => 'COM_EASYBLOG_EXPORT_SETTINGS',
			'cmd' => 'export'
		],
		(object) [
			'title' => 'COM_EASYBLOG_IMPORT_SETTINGS',
			'cmd' => 'import'
		]
	]); ?>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="page" value="<?php echo $layout;?>" />
	<input type="hidden" name="tab" value="<?php echo $activeTab;?>" data-fd-active-tab-input />
</form>
