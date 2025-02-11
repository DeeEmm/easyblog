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
<select name="<?php echo $name;?>" default="<?php echo $default;?>">
    <option value="-2"><?php echo JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS');?></option>
    <option value="alphabet" <?php echo $value == 'alphabet' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_NAME');?></option>
    <option value="active" <?php echo $value == 'active' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_ACTIVE');?></option>
    <option value="latest" <?php echo $value == 'latest' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_LATEST');?></option>
    <option value="latestpost" <?php echo $value == 'latestpost' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_LATEST_POST');?></option>
    <option value="ordering" <?php echo $value == 'ordering' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_COLUMN_ORDERING');?></option>               
</select>
