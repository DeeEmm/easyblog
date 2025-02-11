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
<?php if ($includeDivGroup) { ?>
<div class="o-select-group">
<?php } ?>
	<select name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php echo $className;?>" <?php echo $attributes;?>>
		<?php echo $formElement; ?>
	</select>
<?php if ($includeDivGroup) { ?>
	<label for="" class="o-select-group__drop"></label>
</div>
<?php } ?>
