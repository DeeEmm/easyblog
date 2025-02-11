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
<form name="adminForm" id="adminForm" action="index.php?option=com_easyblog" method="post" enctype="multipart/form-data">
	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', [
			(object) [
				'id' => 'general',
				'title' => 'COM_EASYBLOG_TEAMBLOGS_DETAILS',
				'active' => true
			],
			(object) [
				'id' => 'members',
				'title' => 'COM_EASYBLOG_TEAMBLOGS_MEMBERS',
				'active' => false
			],
			(object) [
				'id' => 'groups',
				'title' => 'COM_EASYBLOG_TEAMBLOGS_GROUPS',
				'active' => false
			]
		]); ?>

		<div class="tab-content">
			<div id="general" class="tab-pane t-block">
				<?php echo $this->output('admin/teamblogs/form/general'); ?>
			</div>

			<div id="members" class="tab-pane">
				<?php echo $this->output('admin/teamblogs/form/members'); ?>
			</div>

			<div id="groups" class="tab-pane">
				<?php echo $this->output('admin/teamblogs/form/groups'); ?>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="id" value="<?php echo $team->id;?>" />
	<input type="hidden" name="deletemembers" id="deletemembers" value="" />
	<input type="hidden" name="deletegroups" id="deletegroups" value="" />
</form>