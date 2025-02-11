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
<div class="db-activity" data-fd-tab-wrapper>
	<?php echo $this->fd->html('adminwidgets.tabs', [
		(object) [
			'id' => 'posts',
			'label' => 'COM_EASYBLOG_DASHBOARD_TAB_POSTS',
			'active' => true
		],
		(object) [
			'id' => 'comments',
			'label' => 'COM_EASYBLOG_DASHBOARD_TAB_COMMENTS',
			'active' => false
		],
		(object) [
			'id' => 'reactions',
			'label' => 'COM_EASYBLOG_REACTIONS',
			'active' => false
		]
	]); ?>

	<div class="o-tab-content p-sm">
		<div id="posts" class="t-block t-hidden" data-fd-tab-content>
			<?php echo $this->output('admin/easyblog/widgets/posts'); ?>
		</div>

		<div id="comments" class="t-hidden" data-fd-tab-content>
			<?php echo $this->output('admin/easyblog/widgets/comments'); ?>
		</div>

		<div id="reactions" class="t-hidden" data-fd-tab-content>
			<?php echo $this->output('admin/easyblog/widgets/reactions'); ?>
		</div>
	</div>
</div>

