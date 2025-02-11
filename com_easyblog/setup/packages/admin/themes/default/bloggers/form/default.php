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
<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data" data-eb-form>

	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', [
			(object) [
				'id' => 'general',
				'title' => 'COM_EASYBLOG_BLOGGER_DETAILS',
				'active' => $activeTab === 'general'
			],
			(object) [
				'id' => 'blog',
				'title' => 'COM_EASYBLOG_BLOGGER_BLOG_SETTINGS',
				'active' => $activeTab === 'blog'
			],
			(object) [
				'id' => 'integrations',
				'title' => 'COM_EASYBLOG_BLOGGER_FORM_INTEGRATIONS',
				'active' => $activeTab === 'integrations'
			]
		]); ?>

		<div class="tab-content">
			<div id="general" class="t-hidden <?php echo $activeTab === 'general' ? 't-block' : '';?>">
				<?php echo $this->output('admin/bloggers/form/general'); ?>
			</div>

			<div id="blog" class="t-hidden <?php echo $activeTab === 'blog' ? 't-block' : '';?>">
				<?php echo $this->output('admin/bloggers/form/blog'); ?>
			</div>

			<div id="integrations" class="t-hidden <?php echo $activeTab === 'integrations' ? 't-block' : '';?>">
				<?php echo $this->output('admin/bloggers/form/integrations'); ?>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $user->id;?>" />
	<?php echo $this->fd->html('form.action'); ?>
	<?php echo $this->fd->html('form.hidden', 'active', $activeTab, '', 'data-fd-active-tab-input'); ?>
</form>
