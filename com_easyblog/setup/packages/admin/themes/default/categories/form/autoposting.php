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
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS_INFO'); ?>

			<div class="panel-body">
				<?php if ($associated) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_GROUPS', 'facebook_group_id'); ?>

						<div class="col-md-7">
							<?php if ($fbGroups) { ?>
							<select name="params[facebook_group_id][]" id="params[facebook_group_id]" class="form-control" multiple="multiple" size="10">
								<?php foreach ($fbGroups as $group) { ?>
								<option value="<?php echo $group->id;?>" <?php echo ($storedFbGroups && in_array($group->id, $storedFbGroups)) ? ' selected="selected"' : '';?>>
									<?php echo $group->name;?>
								</option>
								<?php } ?>
							</select>
							<?php } ?>
						</div>
					</div>
				<?php } else { ?>
				<div class="form-group">
					<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_GROUPS_UNAVAILABLE');?>
				</div>
				<?php } ?>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES', 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES_INFO'); ?>

			<div class="panel-body">
				<?php if ($associated) { ?>
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_PAGE', 'facebook_page_id'); ?>

						<div class="col-md-7">
							<?php if ($fbPages) { ?>
							<select name="params[facebook_page_id][]" id="params[facebook_page_id]" class="form-control" multiple="multiple">
								<?php foreach ($fbPages as $page) { ?>
								<option value="<?php echo $page->id;?>" <?php echo ($storedFbPages && in_array($page->id, $storedFbPages)) ? ' selected="selected"' : '';?>>
									<?php echo $page->name;?>
								</option>
								<?php } ?>
							</select>
							<?php } ?>
						</div>
					</div>
				<?php } else { ?>
				<div class="form-group">
					<div>
						<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_PAGES_UNAVAILABLE');?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

	</div>
</div>
