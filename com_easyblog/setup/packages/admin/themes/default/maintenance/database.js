EasyBlog.require().script('admin/maintenance/database').done(function($) {
   $('[data-base]').addController('EasyBlog.Controller.Maintenance.Database');
});
