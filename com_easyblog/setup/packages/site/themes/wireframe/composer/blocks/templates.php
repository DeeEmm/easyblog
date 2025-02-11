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
<div class="eb-composer-block-menu ebd-block" data-eb-composer-block-menu data-id="<?php echo $block->id; ?>" data-keywords="<?php echo $block->title; ?>">
	<div>
		<i class="fdi fa fa-archive"></i>
		<span><?php echo $block->title; ?></span>
	</div>
	<textarea data-eb-composer-block-meta data-id="<?php echo $block->id; ?>"><?php echo json_encode($block, JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
</div>
