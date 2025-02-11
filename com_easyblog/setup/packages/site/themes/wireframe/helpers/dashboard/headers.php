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
<div class="eb-dashboard-header-wrapper">
	<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
		<?php echo $heading; ?>

		<?php if ($actions || $filters || $search) { ?>
			<div class="eb-bar eb-bar--filter-bar" data-eb-filter-bar>
				<?php if ($actions) { ?>
					<div class="eb-bar__bulk-action sm:t-w--100" data-eb-table-actions>
						<div class="t-d--flex sm:t-flex-direction--c t-mr--md sm:t-mr--no">
							<div class="sm:t-w--100 t-mr--md sm:t-mb--md sm:t-mr--no" style="width:120px;">
								<?php echo $actions; ?>
							</div>
							<a class="btn btn-default sm:t-w--100" href="javascript:void(0);" data-eb-table-apply>
								<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>
							</a>
						</div>
					</div>
				<?php } ?>

				<?php if ($filters || $search) { ?>
					<div class="eb-bar__search-action">
						<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
							<div class="t-flex-grow--1"></div>
							<div class="t-d--flex sm:t-flex-direction--c t-align-items--s t-flex-grow--0 sm:t-w--100">
								<?php if ($filters) { ?>
									<div class="t-d--flex sm:t-flex-direction--c t-flex-grow--0 t-justify-content--fe">
										<?php foreach ($filters as $filter) { ?>
										<div class="sm:t-w--100 t-mr--md sm:t-mb--md sm:t-mr--no" style="width:160px;">
											<?php echo $filter; ?>
										</div>
										<?php } ?>
									</div>
								<?php } ?>

								<?php if ($search) { ?>
									<div class="t-d--flex sm:t-flex-direction--c">
										<?php echo $search; ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<table class="eb-table table table-striped table-hover t-mb--no t-mt--md" data-table-header>
		</table>
	</div>
</div>
