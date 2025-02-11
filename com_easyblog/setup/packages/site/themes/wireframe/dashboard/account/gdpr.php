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
	<?php echo $this->html('dashboard.miniHeading', 'COM_EB_GDPR_DOWNLOAD_INFORMATION'); ?>

	<div class="eb-dashboard-form-section__form t-mt--md">
		<div class="form-horizontal">
			<div class="">
				<div class="gdpr-description">
					<div class="t-mb--md"><?php echo JText::_('COM_EB_GDPR_INCLUDED_INFORMATION');?></div>
					<div><?php echo JText::_('COM_EB_GDPR_INCLUDED_INFORMATION_DESC');?></div>

					<div>
						<?php echo JText::_('COM_EB_GDPR_EXTRA_INFO');?>
					</div>

					<div class="gdpr-download-link center mt-20">
						<a class="btn btn-default" href="javascript:void(0);" data-gdpr-download-link><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_INFORMATION');?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
