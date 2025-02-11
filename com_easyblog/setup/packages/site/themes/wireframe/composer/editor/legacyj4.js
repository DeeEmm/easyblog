EasyBlog.ready(function($){

	EasyBlog.LegacyEditor = {

		editor: "content",

		// Inserts a new item into the legacy editor
		insert: function(html) {
			window.Joomla.editors.instances[this.editor].replaceSelection(html);
		},

		getContent: function() {
			<?php echo 'return Joomla.editors.instances["content"].getValue()'; ?>
		},

		setContent: function(value) {
			<?php echo 'return Joomla.editors.instances["content"].setValue("value")'; ?>
		},

		save: function() {
			<?php echo 'Joomla.editors.instances["content"].onSave()'; ?>;
		}
	};

	// Need to move out those editor-xtd button markup to outside in order to prevent the popup modal styling issue
	$("[data-ebd-workarea-legacy] .joomla-modal").prependTo('body');
});
