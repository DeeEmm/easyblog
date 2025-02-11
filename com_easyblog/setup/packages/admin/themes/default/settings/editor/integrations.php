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
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_INTEGRATIONS_GOOGLEDOC', '', '/administrators/integrations/integrating-with-google-docs'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrations_googledoc', 'COM_EB_INTEGRATIONS_GOOGLEDOC_ENABLE'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_googledoc_api_key', 'COM_EB_INTEGRATIONS_GOOGLEDOC_CLIENT_ID'); ?>

				<?php echo $this->fd->html('settings.text', 'integrations_googledoc_secret_key', 'COM_EB_INTEGRATIONS_GOOGLEDOC_CLIENT_KEY'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>
