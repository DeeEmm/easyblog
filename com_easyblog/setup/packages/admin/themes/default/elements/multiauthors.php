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
<?php if ($isJoomla4) { ?>
<joomla-field-fancy-select>
<?php } ?>
    <select name="<?php echo $name;?>[]" multiple="multiple">
        <?php foreach ($authors as $author) { ?>
            <option value="<?php echo $author->id;?>"<?php echo in_array($author->id, $value) ? ' selected="selected"' : '';?>><?php echo $author->name;?></option>
        <?php } ?>
    </select>
<?php if ($isJoomla4) { ?>
</joomla-field-fancy-select>
<?php } ?>