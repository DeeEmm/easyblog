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
<form name="adminForm" id="adminForm" class="themesForm" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_THEMES_UPLOAD_TITLE', 'COM_EASYBLOG_THEMES_UPLOAD_DESC'); ?>

				<div class="panel-body">
					<div>
						<input type="file" name="package" id="package" class="input" style="width:265px;" data-uniform />
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', 'themes.upload'); ?>
</form>
