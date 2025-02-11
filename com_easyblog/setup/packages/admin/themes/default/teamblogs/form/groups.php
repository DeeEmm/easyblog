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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS', 'COM_EASYBLOG_TEAMBLOGS_GROUPS_DESC'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS', 'groups'); ?>

					<div class="col-md-7">
						<?php echo $this->html('tree.groups', 'groups', $groups); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
