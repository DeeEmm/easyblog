<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (!$this->acl->get('publish_entry')) { ?>
<div class="eblog-message warning">
    <?php echo JText::_('COM_EASYBLOG_DASHBOARD_ALL_NEW_ENTRY_WILL_BE_MODERATE_BY_ADMINS'); ?>
</div>
<?php } ?>

<div>
    <strong><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_DIALOG_TITLE'); ?></strong>
    <?php echo JText::_('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR'); ?>
</div>

