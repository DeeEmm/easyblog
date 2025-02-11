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
<div class="eb-post-category comma-seperator">
	<?php if ($icon) { ?>
	<i class="fdi far fa-folder-open mr-5"></i>
	<?php } ?>

	<?php foreach ($categories as $category) { ?>
	<span>
		<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
	</span>
	<?php } ?>
</div>