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
EasyBlog.require()
.library("ace")
.done(function($){

	var editor = ace.edit("<?php echo $uid;?>");
	editor.setTheme("<?php echo $data->theme;?>");
	editor.getSession().setMode("ace/mode/<?php echo $data->mode;?>");

	editor.renderer.setShowGutter(<?php echo $data->show_gutter ? 'true' : 'false';?>);
	editor.setFontSize("<?php echo $data->fontsize;?>");
	editor.setReadOnly(false);
	editor.setTheme("<?php echo $data->theme;?>");

	$('#<?php echo $uid;?>').height('<?php echo $data->height; ?>px');
	editor.resize();
});
</script>
<pre id="<?php echo $uid;?>" class="eb-block-code">
<?php echo htmlentities($data->code);?> 
</pre>