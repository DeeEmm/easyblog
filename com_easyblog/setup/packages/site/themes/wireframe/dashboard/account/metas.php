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
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_SEO_SETTINGS'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_SEO_META_DESCRIPTION'); ?>

				<div class="col-md-8">
					<textarea class="form-control" cols="30" rows="3" name="metadescription" id="metadescription" data-meta-description><?php echo $this->fd->html('str.escape', $meta->description);?></textarea>
					<div class="eb-box-help">
						<b data-meta-counter><?php echo strlen($this->fd->html('str.escape', $meta->description)); ?></b> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEO_META_DESCRIPTION_INSTRUCTIONS'); ?>
					</div>
				</div>
			</div>
			<div class="form-group">
			<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_SEO_META_KEYWORDS'); ?>

				<div class="col-md-8">
					<textarea class="form-control" rows="3" name="metakeywords" id="metakeywords"><?php echo $this->fd->html('str.escape', $meta->keywords); ?></textarea>
					<div class="eb-box-help">
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_KEYWORDS_SEPARATE_WITH_COMMA'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
