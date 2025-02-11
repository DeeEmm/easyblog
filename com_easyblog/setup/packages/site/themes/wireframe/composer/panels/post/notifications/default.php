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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('notifications', true) ? 'is-open' : ''; ?>" data-name="notifications" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_CUSTOM_NOTIFICATIONS'); ?>

	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_CUSTOM_NOTIFICATIONS_INFO'); ?>

		<div class="eb-composer-category">
			<div class="eb-composer-category-list">
				<div class="eb-composer-category-viewport">
					<div class="eb-composer-category-tree">
						<div class="eb-composer-category-item-group" style="padding-top: 0;">
							<div class="eb-composer-category-item-group-body">
								<div class="eb-composer-category-item-group-viewport" style="overflow-y: scroll;">
									<?php foreach ($aclRuleSets as $group) { ?>
									<div class="eb-composer-category-item selected" style="overflow: visible;">
										<b>
											<input type="checkbox" id="<?php echo $group->id; ?>" name="params[aclNotification][]" value="<?php echo $group->id;?>" <?php echo isset($group->selected) && $group->selected ? 'checked' : '';?>>
										</b>
										<label for="<?php echo $group->id;?>">
											<span><?php echo $group->name; ?></span>
										</label>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>