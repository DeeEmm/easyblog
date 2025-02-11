EasyBlog.require()
.script('site/posts/listings')
.done(function($) {
	$('[data-eb-grid-listings]').implement(EasyBlog.Controller.Listings, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false'; ?>,
		"autoload": <?php echo $showLoadMore ? 'true' : 'false'; ?>,
		"isGrid": true,
		"excludeIds": <?php echo json_encode($excludeBlogs); ?>,
		"userId" : <?php echo $this->my->id ? $this->my->id : 0; ?>,
		"isPollsEnabled": <?php echo $this->config->get('main_polls') ? 'true' : 'false'; ?>
	});
});