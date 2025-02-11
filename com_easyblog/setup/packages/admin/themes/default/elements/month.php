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
<select name="<?php echo $name;?>">
    <option value=""<?php echo !$value ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_MENU_SELECT_MONTH');?></option>
    <option value="01"<?php echo $value == "01" ? ' selected="selected"' : '';?>><?php echo JText::_('JANUARY');?></option>
    <option value="02"<?php echo $value == "02" ? ' selected="selected"' : '';?>><?php echo JText::_('FEBRUARY');?></option>
    <option value="03"<?php echo $value == "03" ? ' selected="selected"' : '';?>><?php echo JText::_('MARCH');?></option>
    <option value="04"<?php echo $value == "04" ? ' selected="selected"' : '';?>><?php echo JText::_('APRIL');?></option>
    <option value="05"<?php echo $value == "05" ? ' selected="selected"' : '';?>><?php echo JText::_('MAY');?></option>
    <option value="06"<?php echo $value == "06" ? ' selected="selected"' : '';?>><?php echo JText::_('JUNE');?></option>
    <option value="07"<?php echo $value == "07" ? ' selected="selected"' : '';?>><?php echo JText::_('JULY');?></option>
    <option value="08"<?php echo $value == "08" ? ' selected="selected"' : '';?>><?php echo JText::_('AUGUST');?></option>
    <option value="09"<?php echo $value == "09" ? ' selected="selected"' : '';?>><?php echo JText::_('SEPTEMBER');?></option>
    <option value="10"<?php echo $value == "10" ? ' selected="selected"' : '';?>><?php echo JText::_('OCTOBER');?></option>
    <option value="11"<?php echo $value == "11" ? ' selected="selected"' : '';?>><?php echo JText::_('NOVEMBER');?></option>
    <option value="12"<?php echo $value == "12" ? ' selected="selected"' : '';?>><?php echo JText::_('DECEMBER');?></option>
</select>