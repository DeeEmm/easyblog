
EasyBlog.require()
.library('datetimepicker')
.done(function($)
{
    $('[data-date-picker]')._datetimepicker({
        component: "eb"
    });

});