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
		<?php if ($src && !$isEdit) ?>
			<iframe src="<?php echo $src; ?>" width="100%" height="400" allowfullscreen="1" frameborder="0"></iframe>
		<? } ?>
	</div>
</div>