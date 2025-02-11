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
<?php if ($legacy) { ?>
	<div id="eb" class="es mt-20" style="margin-top: 10px;">
		<?php echo $comments->getHtml(); ?>
	</div>
<?php } else { ?>
	<div id="es" style="margin-top: 20px;" class="<?php echo ES::responsive()->isMobile() ? 'is-mobile' : 'is-desktop';?>" data-stream-actions>
		<?php echo $comments->html($options); ?>
	</div>
<?php } ?>

