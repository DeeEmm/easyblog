<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($files) { ?>
	<?php foreach($files as $file) { ?>
	<a href="javascript:void(0)" 
		class="eb-comp-toolbar-post"
		data-eb-googleimport-list-item
		data-id="<?php echo $file->id; ?>"
		data-title="<?php echo EB::string()->escape($file->title);; ?>"
	>
		<div class="eb-comp-toolbar-post__title"><?php echo $file->title; ?></div>
	</a>
	<?php } ?>
<?php } ?>