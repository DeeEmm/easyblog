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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-maintenance-form>
    <div class="row">
        <div class="col-md-12">
            <div class="widget-box">
            <h3><?php echo JText::_('COM_EASYBLOG_MAINTENANCE_EXECUTING_SCRIPTS'); ?></h3>
            <p><?php echo JText::_('COM_EASYBLOG_MAINTENANCE_EXECUTING_SCRIPTS_DESC'); ?></p>

            <table class="app-table app-table-middle" data-table-scripts>
                <thead>
                    <tr>
                        <th><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE'); ?></td>
                        <th width="10%" class="center"><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATUS'); ?></td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($scripts as $script) { ?>
                    <tr data-row data-key="<?php echo $script->key; ?>">
                        <td><?php echo $script->title; ?></td>
                        <td class="center"><span class="label label-warning" data-status><i data-icon class="fdi fa fa-wrench"></i></span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <?php echo $this->fd->html('form.action'); ?>
</form>
