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
<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">
	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<?php $i = 0; ?>
			<?php foreach ($groups as $group) { ?>
			<div id="<?php echo str_ireplace(array('.','-',' '), '_', $group);?>" class="t-hidden <?php echo $i === 0 ? ' t-block' : '';?>">
				<div class="row">
					<div class="col-lg-8">
						<div class="panel">
							<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_ACL_GROUP_' . strtoupper($group), 'COM_EASYBLOG_ACL_GROUP_' . strtoupper($group) . '_DESC', isset($docs[$group]) ? '/administrators/access-control/' . $docs[$group] : ''); ?>

							<div class="panel-body">
								<?php if (isset($ruleset->rules[$group]) && is_array($ruleset->rules[$group])) { ?>
									<?php foreach ($ruleset->rules[$group] as $key => $value) { ?>
										<div class="form-group">
											<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_ACL_OPTION_' . strtoupper($key), $key); ?>

											<div class="col-md-7 acl-<?php echo $value ? 'yes' : 'no';?>" data-acl-container>
												<?php echo $this->fd->html('form.toggler', $key, $value, $key, 'data-acl-value'); ?>

												<div>
													<span class="hidden acl-result-yes text-success"><i class="fdi fa fa-check-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key) . '_RESULT_YES'); ?></span>
													<span class="hidden acl-result-no text-danger"><i class="fdi fa fa-times-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key) . '_RESULT_NO'); ?></span>
												</div>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php $i++;?>
			<?php } ?>

			<div id="textfilters" class="tab-pane">
				<div class="row">
					<div class="col-lg-8">
						<div class="panel">
							<div class="panel-head">
								<b><?php echo JText::_('COM_EASYBLOG_ACL_TEXT_FILTERS');?></b>
								<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_ACL_TEXT_FILTERS_INFO');?></div>
							</div>

							<div class="panel-body">
								<div class="form-group">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_DISALLOWED_HTML_TAGS', 'disallow-tags'); ?>
									<div class="col-md-7">
										<textarea id="disallow-tags" name="disallow_tags" class="form-control"><?php echo $filter->disallow_tags;?></textarea>
									</div>
								</div>
								<div class="form-group">
									<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES', 'disallow-attributes'); ?>

									<div class="col-md-7">
										<textarea id="disallow-attributes" name="disallow_attributes" class="form-control"><?php echo $filter->disallow_attributes;?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="id" id="id" value="<?php echo !empty($ruleset->id)? $ruleset->id : ''; ?>" />
	<input type="hidden" name="name" value="<?php echo !empty($ruleset->name)? $ruleset->name : ''; ?>" />
</form>
