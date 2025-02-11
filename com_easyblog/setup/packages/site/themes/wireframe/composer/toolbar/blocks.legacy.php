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
<?php foreach($blocks as $category => $blockItems) { ?>
	<?php foreach ($blockItems as $block) { ?>
		<textarea class="t-hidden" data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
	<?php } ?>
<?php } ?>
