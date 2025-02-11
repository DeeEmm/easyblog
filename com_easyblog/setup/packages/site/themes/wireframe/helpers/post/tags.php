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
<div class="eb-tags cell-top type-<?php echo $this->config->get('layout_tagstyle', '1'); ?>">
	<div class="col-cell cell-label"><?php echo JText::_('COM_EASYBLOG_POST_TAGGED');?></div>
	<div class="col-cell cell-tags">
		<?php foreach ($tags as $tag) { ?>
		<span>
			<a href="<?php echo $tag->getPermalink();?>"><?php echo $tag->getTitle();?></a>
		</span>
		<?php } ?>
	</div>
</div>
