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
EasyBlog.ready(function($){

	<?php if ($submitOnClick) { ?>
	$('[data-checkbox-option]').change(function() {
		$('[data-submit]').click();
	});
	<?php } ?>

	$('[data-show-all-options]').on('click', function() {

		var parent = $(this).parent();
		var optionsItem = parent.find('[data-item-hide]');
		var showLess = parent.find('[data-show-less-options]');
		
		optionsItem.each(function() {
			$(this).removeClass('hide');
		});

		// Hide the button block
		$(this).addClass('hide');
		showLess.removeClass('hide');
	});

	$('[data-show-less-options]').on('click', function() {

		var parent = $(this).parent();
		var optionsItem = parent.find('[data-item-hide]');
		var showAll = parent.find('[data-show-all-options]');

		optionsItem.each(function() {
			$(this).addClass('hide');
		});

		// Hide the button block
		$(this).addClass('hide');
		showAll.removeClass('hide');
	});

	<?php if ($my->id) { ?>
	$('[data-save-filter]').on('click', function() {
		var checked = [];
		var button = $(this);
		var notice = button.siblings('[data-filter-saved]');
		var clearButton = button.parents().find('[data-clear-filter]');

		$('input[type=checkbox]').each(function () {

			if (this.checked) {
				var query = {};
				var name = this.name.replace('[]', '');

				query.name = name;
				query.value = $(this).val();

				checked.push(query);
			}
		});

		var query = {};
		query.name = 'inclusion';
		query.value = "<?php echo $catinclusion; ?>";

		checked.push(query);

		var query = {};
		query.name = 'filtermode';
		query.value = "<?php echo $filterMode; ?>";

		checked.push(query);

		var jsonString = JSON.stringify(checked);
		EasyBlog.ajax('site/controllers/posts/saveFilter', {
			"jsonString" : jsonString,
			"view": "<?php echo $view; ?>",
			"layout": "<?php echo $layout; ?>",
			"id": "<?php echo $catid; ?>"
		})
		.done(function(result) {
			button.addClass('disabled');
			notice.removeClass('hidden');

			setTimeout(function(){
				button.removeClass('disabled');
				notice.addClass('hidden');
			}, 2000); 

			clearButton.removeClass('disabled');
		})
		.fail(function(result) {
			
		});
	});

	$('[data-clear-filter]').on('click', function() {
		
		EasyBlog.ajax('site/controllers/posts/clearFilter', {
			"view": "<?php echo $view; ?>",
			"layout": "<?php echo $layout; ?>",
			"id": "<?php echo $catid; ?>"
		})
		.done(function(result) {
			$('input[type=checkbox]').each(function() { 
				this.checked = false; 
			}); 
			window.location.href = result;
		})
		.fail(function(result) {
			
		});
	});

	<?php } ?>
});
</script>
