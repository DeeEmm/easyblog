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
<?php if ($columns) { ?>
<div class="ebd-block" data-type="thumbnails">
	<div class="eb-thumbs">
	<?php foreach ($columns as $column) { ?>
		<div class="eb-thumbs-col">
		<?php foreach ($column as $image) { ?>
			<?php if ($image) { ?>
			<div class="eb-thumb">
				<div><?php echo $image;?></div>
			</div>
			<?php } ?>
		<?php } ?>
		</div>
	<?php } ?>
	</div>
</div>
<?php } ?>
