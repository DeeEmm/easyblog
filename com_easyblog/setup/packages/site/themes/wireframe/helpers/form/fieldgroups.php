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
<div class="row" data-form-fieldgroup-wrapper>
	<div class="col-lg-10">
		<div class="input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="form-control" value="<?php echo $groupTitle;?>" disabled="disabled" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" data-form-remove-fieldgroup>
					<i class="fdi fa fa-times"></i>
				</button>
				<button class="btn btn-default" type="button" data-form-browse-fieldgroup>
					<i class="fdi fa fa-bars"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_BROWSE_FIELD_GROUP');?>
				</button>
			</span>
		</div>
		<input type="hidden" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" <?php echo $attributes; ?> />
	</div>
</div>

<script type="text/javascript">
EasyBlog.ready(function($) {

	window.insertGroup = function(id, name) {
		$('#<?php echo $id;?>-placeholder').val(name);
		$('#<?php echo $id;?>').val(id);

		EasyBlog.dialog().close();
	}

	$('[data-form-remove-author]').on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-form-author-wrapper]');

		// Reset the form
		parent.find('input[type=hidden]').val('');
		parent.find('input[type=text]').val('');
	});

	$('[data-form-browse-fieldgroup]').on('click', function() {
		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/fields/browseGroup')
		});
	});

});
</script>
