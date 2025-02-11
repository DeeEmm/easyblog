
$(document).ready(function(){
	loading = $('[data-installation-loading]'),
	submit = $('[data-installation-submit]'),
	retry = $('[data-installation-retry]'),
	form = $('[data-installation-form]'),
	completed = $('[data-installation-completed]'),
	source = $('[data-source]'),
	installAddons = $('[data-installation-install-addons]'),
	steps = $('[data-installation-steps]');
});

var eb = {

	init: function() {
	},

	options: {
		"apikey": "<?php echo $input->get('apikey', '');?>",
		"path": null,
		"controller": "install"
	},
	ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&ajax=1",

	ajax: function(task, properties, callback) {

		var prop = $.extend(eb.options, properties);

		var dfd = $.Deferred();

		$.ajax({
			type: "POST",
			url: eb.ajaxUrl + "&controller=" + prop.controller + "&task=" + task,
			data: prop
		}).done(function(result) {
			callback && callback.apply(this, [result]);

			dfd.resolve(result);
		});

		return dfd;
	},

	addons: {

		installModules: function(modules, path) {
			return eb.ajax('installModules', {
				"controller": "addons",
				"path": path,
				"modules": modules
			});
		},

		installPlugins: function(plugins, path) {
			return eb.ajax('installPlugins', {
				"controller": "addons",
				"path": path,
				"plugins": plugins
			});
		},

		runScript: function(script) {
			// Run the maintenace scripts
			return $.ajax({
				type: 'POST',
				url: eb.ajaxUrl + '&controller=maintenance&task=execute',
				data: {
					script: script
				}
			});
		},

		retrieveList: function() {

			var progress = $('[data-addons-progress]');
			var selection = $('[data-addons-container]');
			var syncProgress = $('[data-sync-progress]');

			// Show loading
			loading.removeClass('d-none');

			// Hide submit
			submit.addClass('d-none');

			eb.ajax('list', {"controller": "addons", "path": eb.options.path}, function(result){

				// Hide the retrieving message
				$('[data-addons-retrieving]').addClass('d-none');

				loading.addClass('d-none');
				installAddons.removeClass('d-none');

				selection.html(result.html);

				// Get files for maintenance
				var scripts = result.scripts;
				var maintenanceMsg = result.maintenanceMsg;

				// Set the submit
				installAddons.on('click', function() {

					// Hide the container
					selection.addClass('d-none');

					// Show the installation progress
					progress.removeClass('d-none');
					syncProgress.removeClass('d-none');

					// Install the selected items
					var modules = [];
					var plugins = [];

					$('[data-checkbox-module]:checked').each(function(i, el) {
						modules.push($(el).val());
					});

					$('[data-checkbox-plugin]:checked').each(function(i, el) {
						var plugin = {
										"element": $(el).val(),
										"group": $(el).data('group')
									};

						plugins.push(plugin);
					});

					var progressBar = $('[data-progress-bar]');
					var progressBarResult = $('[data-progress-bar-result]');
					var totalScripts = scripts.length;
					var eachScript = 100 / totalScripts;
					var syncProgressBar = $('[data-sync-progress-bar]');
					var syncProgressBarResult = $('[data-sync-progress-bar-result]');

					var runMaintenance = function() {
						var frame = $('[data-progress-execscript]');

						frame.addClass('active')
							.removeClass('pending');

						var item = $('<li>');

						item.html(maintenanceMsg);

						$('[data-script-logs]').append(item);

						var scriptIndex = 0,
							dfd = $.Deferred();

						var runNextScript = function() {
							if (scripts[scriptIndex] == undefined) {

								$.ajax({
									type: 'POST',
									url: eb.ajaxUrl + '&controller=maintenance&task=finalize'
								}).done(function(result) {
									var item = $('<li>');
									item.addClass('text-success').html(result.message);
									$('[data-progress-execscript-items]').append(item);

									$('[data-progress-execscript]')
										.find('.progress-state')
										.html(result.stateMessage)
										.addClass('text-success')
										.removeClass('text-info');
								});

								dfd.resolve();
								return;
							}

							eb.addons
								.runScript(scripts[scriptIndex])
								.done(function(data) {
									scriptIndex++;

									// update the progress bar here
									var currentWidth = parseInt(syncProgressBar[0].style.width);
									var percentage = Math.round(currentWidth + eachScript);

									syncProgressBar.css('width', percentage + '%');
									syncProgressBarResult.html(percentage + '%');

									var item = $('<li>');

									item.html(data.message);

									$('[data-script-logs]').append(item);

									runNextScript();
								});

						};

						runNextScript();

						return dfd;
					};

					var installModules = function() {
						dfd = $.Deferred();

						// Even if there is no modules selected, we still need to run this
						// to configure Stackideas Toolbar module.
						// if (modules.length < 1) {
						// 	return dfd.resolve();
						// }

						eb.addons
							.installModules(modules, result.modulePath)
								.done(function(data) {
									$('[data-progress-active-message]').html(data.message);

									progressBar.css('width', '50%');
									progressBarResult.html('50%');

									return dfd.resolve();
								});

						return dfd;
					};

					var installPlugins = function() {
						dfd = $.Deferred();

						if (plugins.length < 1) {
							return dfd.resolve();
						}

						eb.addons
							.installPlugins(plugins, result.pluginPath)
								.done(function(data) {
									var progressBarResult = $('[data-progress-bar-result]');

									$('[data-progress-active-message]').html(data.message);

									// Update the width of the progress bar
									progressBar.css('width', '100%');

									// We need to update the progress bar here
									progressBarResult.html('100%');

									return dfd.resolve();
								});

						return dfd;
					};

					// Show loading indicator
					loading.removeClass('d-none');
					installAddons.addClass('d-none');

					// Install Modules
					installModules().done(function() {
						installPlugins().done(function() {

							// Show complete
							$('[data-progress-active-message]').addClass('d-none');
							$('[data-progress-complete-message]').removeClass('d-none');
							$('[data-progress-bar]').css('width', '100%');
							$('[data-progress-bar-result]').html('100%');

							runMaintenance().done(function() {

								// When everything is done, update the submit button
								loading.addClass('d-none');
								submit.removeClass('d-none');

								$('[data-sync-progress-active-message]').addClass('d-none');
								$('[data-sync-progress-complete-message]').removeClass('d-none');
								$('[data-sync-progress-bar]').css('width', '100%');
								$('[data-sync-progress-bar-result]').html('100%');

								submit.on('click', function() {
									form.submit();
								});
							})
						});
					});
				});
			});
		}
	},

	installation: {
		path: null,

		showRetry: function(step) {

			steps.addClass('error');

			retry
				.data('retry-step', step)
				.removeClass('hide');

			// Hide the submit
			submit.addClass('hide');

			// Hide the loading
			loading.addClass('hide');
		},

		extract: function() {

			eb.installation.setActive('data-progress-extract');

			eb.ajax('extract', {}, function(result) {

				// Update the progress
				eb.installation.update('data-progress-extract', result, '10%');

				if (!result.state) {
					eb.installation.showRetry('extract');
					return false;
				}

				// Set the path
				eb.options.path = result.path;

				// Run the next command
				eb.installation.runSQL();
			});
		},

		download: function() {

			eb.installation.setActive('data-progress-download');

			eb.ajax('download', {}, function(result) {

				// Set the progress
				eb.installation.update('data-progress-download', result, '10%');

				if (!result.state) {
					eb.installation.showRetry('download');
					return false;
				}

				// Set the installation path
				eb.options.path = result.path;

				eb.installation.runSQL();
			});
		},

		runSQL: function() {

			// Install the SQL stuffs
			eb.installation.setActive('data-progress-sql');

			eb.ajax('sql', {}, function(result) {

				// Update the progress
				eb.installation.update('data-progress-sql', result, '15%');

				if (!result.state) {
					eb.installation.showRetry('runSQL');
					return false;
				}

				// Run the next command
				eb.installation.installFoundry();
			});
		},

		installFoundry : function() {

			// Install the admin stuffs
			eb.installation.setActive('data-progress-foundry');

			eb.ajax('foundry', {}, function(result) {
				// Set the progress
				eb.installation.update('data-progress-foundry', result, '18%');

				if (!result.state) {
					eb.installation.showRetry('installFoundry');
					return false;
				}

				eb.installation.installAdmin();
			});
		},

		installAdmin: function() {

			// Install the admin stuffs
			eb.installation.setActive('data-progress-admin');

			// Run the ajax calls now
			eb.ajax('copy', {"type": "admin"}, function(result) {

				// Update the progress
				eb.installation.update('data-progress-admin', result, '20%');

				if (!result.state) {
					eb.installation.showRetry('installAdmin');
					return false;
				}

				eb.installation.installSite();
			});
		},

		installSite : function() {

			// Install the admin stuffs
			eb.installation.setActive('data-progress-site');

			eb.ajax('copy', { "type" : "site" }, function(result) {


				// Update the progress
				eb.installation.update('data-progress-site', result, '25%');

				if (!result.state) {
					eb.installation.showRetry('installSite');
					return false;
				}

				eb.installation.installLanguages();
			});
		},

		installLanguages : function() {
			// Install the admin stuffs
			eb.installation.setActive('data-progress-languages');

			eb.ajax('copy', {"type": "languages"}, function(result) {

				// Set the progress
				eb.installation.update('data-progress-languages', result, '30%');

				if (!result.state) {
					eb.installation.showRetry('installLanguages');
					return false;
				}

				eb.installation.installMedia();
			});

		},

		installMedia : function() {

			// Install the admin stuffs
			eb.installation.setActive('data-progress-media');

			eb.ajax('copy', {"type": "media"}, function(result) {
				// Set the progress
				eb.installation.update('data-progress-media', result, '35%');

				if (!result.state) {
					eb.installation.showRetry('installMedia');
					return false;
				}

				eb.installation.installToolbar();
			});
		},

		installToolbar : function() {

			// Install the admin stuffs
			eb.installation.setActive('data-progress-toolbar');

			eb.ajax('toolbar', {}, function(result) {
				// Set the progress
				eb.installation.update('data-progress-toolbar', result, '40%');

				if (!result.state) {
					eb.installation.showRetry('installToolbar');
					return false;
				}

				eb.installation.syncDB();
			});
		},

		syncDB: function() {

			// Synchronize the database
			eb.installation.setActive('data-progress-syncdb');

			eb.ajax('sync', {}, function(result) {
				eb.installation.update('data-progress-syncdb', result, '45%');

				if (!result.state) {
					eb.installation.showRetry('syncDB');
					return false;
				}

				eb.installation.postInstall();
			});
		},

		postInstall : function() {

			// Perform post installation stuffs here
			eb.installation.setActive('data-progress-postinstall');

			eb.ajax('post', {}, function(result) {

				// Set the progress
				eb.installation.update('data-progress-postinstall', result, '100%');

				if (!result.state) {
					eb.installation.showRetry('postInstall');
					return false;
				}

				completed
					.removeClass('d-none')
					.show();

				loading
					.addClass('d-none');

				submit
					.removeClass('d-none');

				submit.on('click', function() {

					source.val(eb.options.path);

					form.submit();
				});

			});
		},

		update: function(element, obj) {
			var logItem = $('[' + element + ']');

			logItem.removeClass("is-loading")
				.addClass(obj.state ? 'is-complete' : 'is-error');
		},

		setActive: function(item) {
			var logItem = $('[' + item + ']');

			logItem
				.removeClass('is-error is-complete')
				.addClass('is-loading');
		}
	}
}
