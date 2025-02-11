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
<div class="btn-group" data-toolbar-view data-type="location">
	<button type="button" class="btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-composer-toolbar-location
		data-eb-provide="tooltip"
		data-html="1"
		data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?><?php echo $this->html('composer.shortcut', ['shift', 'l']); ?>"
		data-placement="bottom"
	>
		<i class="fdi far fa-compass fa-fw"></i>
	</button>

	<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--location">
		<div class="eb-comp-toolbar-dropdown-menu__hd">
			<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
				<i class="fdi far fa-compass fa-fw"></i>
			</div>
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?>
			<div class="eb-comp-toolbar-dropdown-menu__hd-action">
				<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>
		</div>

		<div class="eb-comp-toolbar-dropdown-menu__bd">
			<?php if ($this->config->get('location_service_provider') != 'osm') { ?>
				<?php echo $this->output('site/composer/toolbar/maps'); ?>
			<?php } else { ?>
				<?php echo $this->output('site/composer/toolbar/osm'); ?>
			<?php } ?>
		</div>
	</div>
</div>
