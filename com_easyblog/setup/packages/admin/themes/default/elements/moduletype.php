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
<select name="<?php echo $name;?>" class="form-control" data-module-type-selection>
	<option value="recent" <?php echo $value == "0" || $value == 'recent' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts'); ?></option>
	<option value="author" <?php echo $value == "1" || $value == 'author' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts by Author');?></option>
	<option value="category" <?php echo $value == "2" || $value == 'category' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts by Category');?></option>
	<option value="tags" <?php echo $value == "3" || $value == 'tags' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts by Tag');?></option>
	<option value="team" <?php echo $value == "4" || $value == 'team' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts by Team');?></option>
	<option value="entry" <?php echo $value == "5" || $value == 'entry' ? ' selected="selected"' :'';?>><?php echo JText::_('Recent Posts by Active Author (Entry View)');?></option>
	<option value="viewer" <?php echo $value == "6" || $value == "viewer" ? ' selected="selected"' : '';?>><?php echo JText::_('Recent Posts by Current Logged In User');?></option>
</select>

<script type="text/javascript">
EasyBlog.ready(function($){

	// Hide tabs that shouldn't be seen
	window.hideInactiveTabs = function(current) {
		$('#myTabTabs a[href=#attrib-recent]').hide();
		$('#myTabTabs a[href=#attrib-author]').hide();
		$('#myTabTabs a[href=#attrib-category]').hide();
		$('#myTabTabs a[href=#attrib-tags]').hide();
		$('#myTabTabs a[href=#attrib-team]').hide();

		var ordering = ['recent', 'author', 'category', 'tags', 'team'];
		var currentIndex = ordering[current] || current;

		
		$('#myTabTabs a[href=#attrib-' + currentIndex + ']').show();		
	};

	$(document).ready(function($) {
		jQuery('[data-module-type-selection]').on('change', function() {
			var value = $(this).val();
			window.hideInactiveTabs(value);
			
		});

		window.hideInactiveTabs('<?php echo $value;?>');
	});
});
</script>