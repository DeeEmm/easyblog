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
<div class="eb-responsive-twitch">
	<div data-twitch-preview>
		<?php if ($src && !$isEdit) { ?>
			<iframe src="<?php echo $src; ?>" width="400" height="<?php echo $height; ?>" allowfullscreen="1" frameborder="0" allow="autoplay *; encrypted-media *; fullscreen *" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation" style="width:100%;max-width:660px;overflow:hidden;background:transparent;"></iframe>
		<?php } ?>
	</div>
</div>