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
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_GOOGLEADS'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_GOOGLEADS_ENABLE'); ?>

				<div class="col-md-8">
					<?php echo $this->fd->html('form.toggler', 'adsense_published', $adsense->published); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE'); ?>

				<div class="col-md-8">
					<textarea id="adsense_code" name="adsense_code" class="form-control" rows="3"><?php echo $adsense->code; ?></textarea>
					<div class="eb-box-help">
						<?php echo JText::_('COM_EB_DASHBOARD_GOOGLEADS_CODE_NOTE');?><br />
						<?php if ($this->config->get('integration_google_adsense_responsive')) { ?>
							<pre><?php echo $this->fd->html('str.escape', '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXX" data-ad-slot="xxxx" data-ad-format="auto"></ins>');?></pre>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE_HELP');?>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_GOOGLEADS_APPEARANCE'); ?>

				<div class="col-md-8">
					<?php echo $this->fd->html('form.dropdown', 'adsense_display', $adsense->display, [
						'both' => 'COM_EASYBLOG_ADSENSE_HEADER_AND_FOOTER',
						'header' => 'COM_EASYBLOG_ADSENSE_HEADER',
						'footer' => 'COM_EASYBLOG_ADSENSE_FOOTER',
						'beforecomments' => 'COM_EASYBLOG_ADSENSE_BEFORE_COMMENTS',
						'userspecified' => 'COM_EASYBLOG_ADSENSE_USER_SPECIFIED'
					], ['attr' => 'data-adsense-appearence']); ?>

					<div class="eb-box-help hide" data-adsense-appearence-help>
						<?php echo JText::_('COM_EASYBLOG_ADSENSE_DISPLAY_NOTE'); ?>

						 <br /><br />
						 <pre>{eblogads} <br /> -- or -- <br /> {eblogads right} <br /> -- or -- <br /> {eblogads left} <br /></pre>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
