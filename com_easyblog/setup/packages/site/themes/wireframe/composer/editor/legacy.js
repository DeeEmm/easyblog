EasyBlog.ready(function($){

    EasyBlog.LegacyEditor = {

        editor: "content",

        // Inserts a new item into the legacy editor
        insert: function(html) {
            window.jInsertEditorText(html, this.editor);
        },

        getContent: function() {
            <?php echo 'return ' . $editor->getContent('content'); ?>
        },

        setContent: function(value) {
            <?php echo 'return ' . $editor->setContent('content', 'value'); ?>
        },

        save: function() {
            <?php echo $editor->save('content'); ?>;
        }
    };
});