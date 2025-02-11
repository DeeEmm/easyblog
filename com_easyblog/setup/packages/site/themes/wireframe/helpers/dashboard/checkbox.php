<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-checkbox<?php echo ($disabled) ? ' disabled' : ''; ?>">
	<input id="checkbox-<?php echo $value;?>" type="checkbox" name="<?php echo $element;?>" value="<?php echo $value;?>"
	<?php echo  ($disabled) ? ' disabled="disabled"' : ''; ?>
	data-eb-table-checkbox />
	<label for="checkbox-<?php echo $value;?>"></label>
</div>
