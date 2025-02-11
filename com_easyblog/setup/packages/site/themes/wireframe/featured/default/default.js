EasyBlog.require()
.script('site/posts/listings')
.done(function($) {

	$('[data-blog-listings]').implement(EasyBlog.Controller.Listings, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"autoload": false,
		"gdpr_enabled": <?php echo $this->config->get('gdpr_iframe_enabled') ? 'true' : 'false'; ?>,
		"dropcap": <?php echo $this->config->get('layout_dropcaps') ? 'true' : 'false';?>,
		"columnStyle": "<?php echo $postStyles->column;?>",
		"rowStyle": "<?php echo $postStyles->row;?>",
		"userId" : <?php echo $this->my->id ? $this->my->id : 0; ?>,
		"isPollsEnabled": <?php echo $this->config->get('main_polls') ? 'true' : 'false'; ?>
	});
})
