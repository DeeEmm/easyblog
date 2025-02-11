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
<div class="eb-mm-info" data-eb-mm-info data-key="<?php echo $file->key; ?>">
	<div class="eb-composer-viewport eb-mm-info-preview">
		<div class="eb-composer-viewport-content" data-scrolly-viewport>
			<div class="ebd-workarea is-standalone eb-mm-workarea" data-eb-mm-workarea>
				<div class="ebd" data-eb-mm-document>
					<?php echo $this->output('site/composer/media/info/' . $file->type); ?>
				</div>
			</div>
		</div>
	</div>
</div>
