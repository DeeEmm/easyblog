EasyBlog.ready(function($){

	$('[data-mail-provider]').on('change', function(){
		var value = $(this).val();

		if (value == 'gmail') {
			$('[data-mailbox-address]').val('imap.gmail.com');
			$('[data-mailbox-port]').val('993');
			$('[data-mailbox-type]').val('imap');
		}

		if (value == 'hotmail') {
			$('[data-mailbox-address]').val('imap-mail.outlook.com');
			$('[data-mailbox-port]').val('993');
			$('[data-mailbox-type]').val('imap');
		}

	});

	$('[data-test-mailbox]').on('click', function(){

		var data = {
			server: $('[data-mailbox-address]').val(),
			port: $('[data-mailbox-port]').val(),
			service: $('[data-mailbox-type]').val(),
			ssl: $('[data-mailbox-ssl]').val(),
			mailbox: $('[data-mailbox-name]').val(),
			user: $('[data-mailbox-username]').val(),
			pass: $('[data-mailbox-password]').val()
		};

		EasyBlog.ajax('admin/views/settings/testMailbox', data)
			.done(function(output){
				$('[data-mailbox-test-result]').html(output);
			})
			.fail(function(output) {
				$('[data-mailbox-test-result]').html(output);
			});
	});
});
