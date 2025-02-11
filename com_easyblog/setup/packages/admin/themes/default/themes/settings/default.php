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
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-lg-6">
			<?php foreach ($themeObj->config as $group) { ?>
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', $group->label, $group->description); ?>

				<div class="panel-body">
					<?php foreach ($group->params as $param) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', $param->label, 'params_' . $param->name); ?>

						<div class="col-md-7">
							<?php if ($param->type == 'color') { ?>
								<?php echo $this->fd->html('form.colorpicker', 'params_' . $param->name, $params->get('params_' . $param->name, $param->default), $param->default); ?>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>

		<div class="col-lg-6">
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $themeObj->element;?>" />
	<?php echo $this->fd->html('form.action', 'themes.saveSettings'); ?>
</form>
