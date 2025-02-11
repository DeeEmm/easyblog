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
<div class="eb-dashboard-form-section">
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_FEEDBURNER'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_FEEDBURNER_URL'); ?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'feedburner_url', $this->escape($feedburner->url)); ?>
				</div>
			</div>
		</div>
	</div>
</div>
