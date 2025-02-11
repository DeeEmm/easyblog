
EasyBlog.require()
.done(function($) {
	var template = $('[data-team-member-template]').detach().html();

	window.getTemplate = function(id, name) {
		var item = $(template);

		item.attr('data-id', id);
		item.attr('id', 'members-' + id);
		item.find('[data-member-id]').val(id);
		item.find('[data-member-name]').html(name);

		return item;
	};

	window.insertUser = function(id, name) {

		var elementId = 'member-' + id;
		var element = ('#' + elementId);
		var template = window.getTemplate(id, name);

		$('[data-members-list]').append(template);

		// Upon selecting the user, close the dialog
		EasyBlog.dialog().close();
	}

	$(document).on('click.eb.remove.member', '[data-remove-member]', function() {

		var id  = $(this).parents('[data-team-member]').data('id');

		// Remove the record
		$('#members-' + id).remove();

		if ($('#deletemembers').val() == '') {
			$('#deletemembers').val(id);
		} else {
			var members = $('#deletemembers').val();
			$('#deletemembers').val(members + ',' + id);
		}
	});

	$(document).on('click.eb.team.set.admin', '[data-set-admin]', function() {

		var parentItem  = $(this).parents('[data-team-member]');
		var button = $(this);

		var teamid  = parentItem.data('teamid');
		var userid  = parentItem.data('id');

		EasyBlog.ajax('admin/views/teamblogs/markAdmin', {
			"userid" : userid,
			"teamid" : teamid
		})
		.done(function(result, label) {
			button.removeAttr('data-set-admin');

			button.text(label);
			button.attr('data-remove-admin', '');
			button.attr('class', 'remove_admin');

		})
		.fail(function(message) {
			EasyBlog.dialog({
				content: message
			});
		})
	});

	$(document).on('click.eb.team.remove.admin', '[data-remove-admin]', function() {

		var parentItem  = $(this).parents('[data-team-member]');
		var button = $(this);

		var teamid  = parentItem.data('teamid');
		var userid  = parentItem.data('id');

		EasyBlog.ajax('admin/views/teamblogs/removeAdmin', {
			"userid" : userid,
			"teamid" : teamid
		})
		.done(function(result, label) {
			button.removeAttr('data-remove-admin');

			button.text(label);
			button.attr('data-set-admin', '');
			button.attr('class', 'set_admin');


		})
	   .fail(function(message) {
			EasyBlog.dialog({
				content: message
			});
		})
	});

	$('[data-browse-members]').on('click', function() {

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/bloggers/browse')
		})
	});
});
