<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
					<b><?php echo JText::_( 'COM_EASYBLOG_SUBSCRIPTION_IMPORT_HEADING' );?></b>
					<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_IMPORT_INFO'); ?></div>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="page_title" class="col-md-4">
							<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_IMPORT_CSV'); ?>

							<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_IMPORT_BUTTON');?>"
								data-content="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_IMPORT_BUTTON_DESC');?>" data-eb-provide="popover" class="fdi fa fa-question-circle pull-right"></i>
						</label>

						<div class="col-md-8">
							<input type="file" name="package" id="package" class="input" style="width:265px;" />
							<button class="btn btn-sm btn-primary"><i class="fdi fa fa-upload"></i>&nbsp; <?php echo JText::_( 'COM_EASYBLOG_SUBSCRIPTION_IMPORT_BUTTON' );?> </button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="subscriptions.importFile" />
	<?php echo JHTML::_('form.token');?>
</form>
