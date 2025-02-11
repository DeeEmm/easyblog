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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_FEEDBURNER_URL', 'feedburner_url'); ?>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'feedburner_url', $this->fd->html('str.escape', $feedburner->url), 'feedburner_url'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">

		<?php if ($this->config->get('integration_google_adsense_enable')) { ?>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_ENABLE', 'adsense_published'); ?>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'adsense_published', $adsense->published); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_CODE', 'adsense_code'); ?>

					<div class="col-md-7">
						<textarea id="adsense_code" name="adsense_code" class="form-control"><?php echo $adsense->code; ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_DISPLAY_IN', 'adsense_display'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'adsense_display', $adsense->display, [
							'both' => 'COM_EASYBLOG_BOTH_HEADER_AND_FOOTER_OPTION',
							'header' => 'COM_EASYBLOG_HEADER_OPTION',
							'footer' => 'COM_EASYBLOG_FOOTER_OPTION',
							'beforecomments' => 'COM_EASYBLOG_BEFORE_COMMENTS_OPTION',
							'userspecified' => 'COM_EASYBLOG_ADSENSE_USER_SPECIFIED'
						]); ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
