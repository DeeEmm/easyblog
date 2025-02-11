<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-panels" data-eb-composer-panels data-remember-panel="<?php echo $isPanelPreferencesEnabled ? 1 : 0; ?>">
	<div class="eb-composer-panel-tab-wrapper">
		<div class="eb-composer-panel-tabs">
			<div class="eb-composer-panel-tab mobile-show" data-composer-mobile-hide-info>
				<div>
					<i class="fdi fa fa-chevron-left" style="font-size: 18px;"></i>
				</div>
			</div>

			<div class="eb-composer-panel-tab <?php echo $templateEditor ? 'hide' : 'active'; ?>" data-eb-composer-panel-tab data-id="post-options">
				<div>
					<i class="fdi fa fa-pencil-alt lg:t-d--none"></i>
					<span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_POST');?></span>
				</div>
			</div>

			<?php if ($this->config->get('layout_composer_fields') && !$templateEditor) { ?>
			<div class="eb-composer-panel-tab<?php echo !$displayFieldsTab ? ' hide' : '';?>" data-eb-composer-panel-tab data-id="fields">
				<div>
					<i class="fdi fa fa-th-large lg:t-d--none"></i>
					<span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOM_FIELDS');?></span>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->config->get('layout_dashboardseo') && !$templateEditor) { ?>
			<div class="eb-composer-panel-tab" data-eb-composer-panel-tab data-id="seo">
				<div>
					<i class="fdi fa fa-globe lg:t-d--none"></i>
					<span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_SEO');?></span>
				</div>
			</div>
			<?php } ?>

			<div class="eb-composer-panel-tab <?php echo $templateEditor ? 'active' : ''; ?>" data-eb-composer-panel-tab data-id="blocks">
				<div>
					<i class="fdi fa fa-cube lg:t-d--none"></i>
					<span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS');?></span>
				</div>
			</div>

		</div>

		<div class="eb-composer-panel-group">
			<?php echo $this->output('site/composer/panels/post/default'); ?>

			<?php echo $this->output('site/composer/panels/fields/default'); ?>

			<?php echo $this->output('site/composer/panels/blocks/default'); ?>

			<?php if ($this->config->get('layout_dashboardseo')) { ?>
				<?php echo $this->output('site/composer/panels/seo/default'); ?>
			<?php } ?>
		</div>
	</div>
</div>
