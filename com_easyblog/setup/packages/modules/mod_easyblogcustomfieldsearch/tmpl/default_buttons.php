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
<?php if ($params->get('savefilter', false)) { ?>
	<button class="mod-btn mod-btn-primary" data-save-filter
		<?php if (!$my->id) { ?>
		data-original-title="<?php echo JText::_('MOD_EASYBLOG_BUTTON_TOOLTIP_LOGIN_NEEDED'); ?>"
		data-placement="top"
		data-eb-provide="tooltip"
		<?php } ?>
	><?php echo JText::_('MOD_EASYBLOG_SAVEFILTER') ?></button>
<?php } ?>

<?php if ($params->get('clearfilter', false)) { ?>
	<button class="mod-btn mod-btn-default" data-clear-filter
		<?php if (!$my->id) { ?>
		data-original-title="<?php echo JText::_('MOD_EASYBLOG_BUTTON_TOOLTIP_LOGIN_NEEDED'); ?>"
		data-placement="top"
		data-eb-provide="tooltip"
		<?php } ?>
	><?php echo JText::_('MOD_EASYBLOG_CLEARFILTER') ?></button>
<?php } ?>
