EasyBlog.require().script('admin/maintenance/form').done(function($) {
   $('[data-maintenance-form]').addController('EasyBlog.Controller.Maintenance.Form');
});
