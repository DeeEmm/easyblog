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
<br />
<div style='margin:4px;'>
	<?php $i = 0; ?>
	<?php foreach ($keys as $key) { ?>
	<kbd style='margin-top: 8px;background:#4B5563;color:#fff;padding: 2px 4px;border-radius: 4px;'><?php echo $key;?></kbd>
	<?php echo $i != (count($keys) - 1) ? '+' : '';?>
	<?php $i++;?>
	<?php } ?>
</div>
