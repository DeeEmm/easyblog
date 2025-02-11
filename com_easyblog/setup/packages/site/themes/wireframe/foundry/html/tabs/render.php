<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-fd-tab-wrapper>
	<div class="fd-tab t-mb--sm l-spaces--xs <?php echo $tabHeaderClass; ?>" data-fd-tabs-header>
		<?php foreach ($tabs as $tab) { ?>
		<div class="fd-tab__item <?php echo $tab->active ? 'is-active' : '';?> <?php echo $tabHeaderItemClass; ?>" data-fd-tab-header-item>
			<a href="#<?php echo $tab->id;?>" class="fd-tab__link" data-fd-tab>
				<?php echo JText::_($tab->label); ?>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="tab-content <?php echo $tabContentClass; ?>">
		<?php foreach ($tabs as $tab) { ?>
		<div id="<?php echo $tab->id;?>" class="<?php echo $tab->active ? 't-block' : '';?> t-hidden <?php echo $tabContentItemClass; ?>">
			<?php echo $tab->contents;?>
		</div>
		<?php } ?>
	</div>
</div>