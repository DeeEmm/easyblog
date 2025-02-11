<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $name;?>" default="<?php echo $default;?>" class="form-control">
    <option value="-1"<?php echo $value == "-1" ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS');?></option>
    <option value="-2"<?php echo $value == "-2" ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_INHERIT_FROM_SETTINGS');?></option>
    <option value="-3"<?php echo $value == "-3" ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_INHERIT_FROM_JOOMLA');?></option>
    <option value="5"<?php echo $value == "5" ? ' selected="selected"' : '';?>>5</option>
    <option value="10"<?php echo $value == "10" ? ' selected="selected"' : '';?>>10</option>
    <option value="15"<?php echo $value == "15" ? ' selected="selected"' : '';?>>15</option>
    <option value="20"<?php echo $value == "20" ? ' selected="selected"' : '';?>>20</option>
    <option value="25"<?php echo $value == "25" ? ' selected="selected"' : '';?>>25</option>
    <option value="30"<?php echo $value == "30" ? ' selected="selected"' : '';?>>30</option>
    <option value="50"<?php echo $value == "50" ? ' selected="selected"' : '';?>>50</option>
    <option value="100"<?php echo $value == "100" ? ' selected="selected"' : '';?>>100</option>
</select>

