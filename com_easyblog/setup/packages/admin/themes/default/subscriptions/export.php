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
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_EXPORT_HEADING');?></b>
					<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_EXPORT_INFO'); ?></div>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="page_title" class="col-md-4">
							<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_EXPORT_TYPE'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_EXPORT_TYPE');?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_EXPORT_TYPE_DESC');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-8">
							<div class="row">
								<div class="col-lg-8">
									<?php echo $this->fd->html('form.dropdown', 'type', 'site', [
										'site' => 'COM_EASYBLOG_SITE_OPTION',
										'blogger' => 'COM_EASYBLOG_BLOGGER_OPTION',
										'entry' => 'COM_EASYBLOG_BLOG_POST_OPTION',
										'category' => 'COM_EASYBLOG_CATEGORY_OPTION',
										'team' => 'COM_EASYBLOG_TEAM_OPTION'
									]); ?>
								</div>
								<div class="col-lg-4">
									<button class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_EXPORT_BUTTON');?> &raquo;</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="subscriptions.export" />
	<?php echo JHTML::_('form.token');?>
</form>
