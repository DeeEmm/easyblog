
EasyBlog.require()
.done(function($)
{
    $(document).on('click.eb.select.add.options', '[data-select-add-<?php echo $element;?>]', function() {
        var parent  = $(this).parents('[data-select-row]');
        var newItem = $(parent).clone();

        $(newItem).addClass('mt-5');
        $(newItem).find('[data-select-value]').val('');
        $(newItem).find('[data-select-db]').val('');
        $(newItem).find('[data-select-remove]').removeClass('hide');
        $(newItem).removeClass('hide');

        $('[data-select-container]').append(newItem);
    });

    $(document).on('click.eb.select.remove.options', '[data-select-remove]', function() {

        var parent  = $(this).parents('[data-select-row]');

        $(parent).remove();
    });
});