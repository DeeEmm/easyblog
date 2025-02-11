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
<div class="eb-nmm-image-preview <?php echo $classes;?>" data-mm-info-preview>
	<?php if ($icon) { ?>
	<i class="<?php echo $icon;?>"></i>
	<?php } ?>

	<?php if ($image) { ?>
		<div style="background-image:url('<?php echo $image;?>');"></div>
	<?php } ?>
</div>
