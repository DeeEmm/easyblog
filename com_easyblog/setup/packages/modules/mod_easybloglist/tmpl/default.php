<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
EasyBlog.ready(function($) {
    $('[data-module-easybloglist-<?php echo $uid;?>]').on('change', function() {
        var item = $(this).children(':selected');

        window.location = item.data('permalink');
    });
});
</script>
<div id="eb" class="eb-mod mod-easybloglist<?php echo $modules->getWrapperClass();?>">
	<select class="form-control" name="blog-list-item" data-module-easybloglist-<?php echo $uid;?>>
		<option value="0" <?php echo !$selected ? ' selected="selected"' : '';?>><?php echo JText::_('MOD_EASYBLOGLIST_SELECT_AN_ENTRY'); ?></option>

		<?php foreach ($posts as $post) { ?>
		<option value="<?php echo $post->id;?>" data-permalink="<?php echo $post->getPermalink();?>"<?php echo $selected == $post->id ? ' selected="selected"' :'';?>><?php echo $post->title; ?></option>
		<?php } ?>
	</select>
</div>