<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-ebd-workarea-legacy>
<?php if (!$templateEditor) { ?>
	<?php if ($post->isNew() && $singleTemplate) { ?>
		<?php echo $editor->display('content', $singleTemplate->data, '100%', '350', '10', '10', array('image', 'pagebreak','ninjazemanta'), null, 'com_easyblog'); ?>
	<?php } else { ?>
		<?php echo $editor->display('content', htmlspecialchars($post->renderEditorContent(), ENT_COMPAT, 'UTF-8'), '100%', '350', '10', '10', array('image', 'pagebreak','ninjazemanta'), null, 'com_easyblog'); ?>
	<?php } ?>
<?php } else { ?>
<?php echo $editor->display('content', $postTemplate->data, '100%', '350', '10', '10', array('image', 'pagebreak','ninjazemanta'), null, 'com_easyblog'); ?>
<?php } ?>
</div>
