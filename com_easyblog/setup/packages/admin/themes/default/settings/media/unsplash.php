<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->fd->html('panel.heading', 'COM_EB_INTEGRATIONS_UNSPLASH', '', '/administrators/integrations/integrating-with-unsplash'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'unsplash_enabled', 'COM_EB_ENABLE_UNSPLASH_SETTINGS'); ?>

				<?php echo $this->fd->html('settings.text', 'unsplash_app_name', 'COM_EB_UNSPLASH_APP_NAME_SETTINGS'); ?>

				<?php echo $this->fd->html('settings.text', 'unsplash_access_key', 'COM_EB_UNSPLASH_ACCESS_KEY_SETTINGS'); ?>

				<?php echo $this->fd->html('settings.text', 'unsplash_limit', 'COM_EB_UNSPLASH_LIMIT_SETTINGS', '', [
					'postfix' => 'COM_EB_UNSPLASH_LIMIT_SETTINGS_PREFIX',
					'size' => 5
				]); ?>
			</div>
		</div>
	</div>
</div>
