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
<?php if ($menuItems) { ?>
<amp-sidebar id="sidebar" layout="nodisplay" side="<?php echo $isRtl ? 'left' : 'right'; ?>">
	<button class="close-btn" on="tap:sidebar.toggle" type="reset">×</button>
	<div class="sidebar-nav">
		<nav>
			<ul>
				<?php foreach ($menuItems as $item) { ?>
				<li>
					<a href="<?php echo $item->flink;?>"><?php echo $item->title;?></a>
				</li>
				<?php } ?>
			</ul>
		</nav>
	</div>
</amp-sidebar>
<?php } ?>
