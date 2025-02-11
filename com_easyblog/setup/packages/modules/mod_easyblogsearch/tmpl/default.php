<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod eb-mod-search mod-easyblogsearch<?php echo $params->get('moduleclass_sfx'); ?>">
	<form name="search-blogs" action="<?php echo JRoute::_('index.php');?>" method="post">
		<div class="input-group">
			<input type="text" name="query" id="search-blogs" class="form-control" placeholder="<?php echo JText::_($params->get('placeholder', 'MOD_EASYBLOGSEARCH_PLACEHOLDER'));?>" />
			<span class="input-group-btn">
				<button class="btn btn-default"><?php echo JText::_('MOD_EASYBLOGSEARCH_SEARCH');?></button>
			</span>
		</div>

		<?php if ($categoryId) { ?>
		<input type="hidden" name="category_id" value="<?php echo $categoryId;?>" />
		<?php } ?>

		<?php echo EB::themes()->fd->html('form.action', 'search.query'); ?>
	</form>
</div>
