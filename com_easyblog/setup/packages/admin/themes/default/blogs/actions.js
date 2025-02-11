EasyBlog.require()
.script('admin/toolbar', 'admin/tag/suggest')
.done(function($) {

	// Insert the dropdown to the toolbar
	$('[data-actions]').implement(EasyBlog.Controller.Admin.Toolbar, {
		"hints": {
			"empty": "<?php echo JText::_('Please select at least 1 post from the table below before submitting the form');?>"
		},
		"bindings": {
			"admin/views/blogs/move": {
				"submit": function() {

					var category = $('#move_category').val();

					if (category == '') {
						return;
					}

					$('[data-move-category]').val(category);

					$.Joomla('submitform', ['blogs.move']);
				}
			},
			"admin/views/blogs/authors": {
				"submit": function() {
					var author = $('#move_author').val();
					$('[data-move-author]').val(author);

					$.Joomla('submitform', ['blogs.changeAuthor']);			
				}
			},
			"admin/views/blogs/massAssignTags": {
				"submit": function() {

					var selected = [];

        			// find each of the tags which user enter in the text field
					$('[data-textboxlist-item]').each(function(i, e) {

						// find all the tag title 
						var tag = $(e).text();

						// Remove whitespace and comma
						var tag = $.trim(tag).replace(/,/g,"");

						// make it as array for all the selected tags
						selected.push(tag);
					});

					// set to hidden input on the page
					$('[data-assign-tags]').val(selected);

					$.Joomla('submitform', ['blogs.massAssignTags']);			
				}
			}			
		}
	});

});
