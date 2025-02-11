EasyBlog.require()
.script('site/posts/entry')
<?php if ($this->config->get('main_google_analytics_script')) { ?>
.script('https://www.googletagmanager.com/gtag/js?id=<?php echo $this->config->get("main_google_analytics_id"); ?>')
<?php } ?>

.done(function($) {
	var trackingId = '<?php echo $this->config->get("main_google_analytics_id"); ?>';

	<?php if ($this->config->get('main_google_analytics') && $this->config->get('main_google_analytics_id')) { ?>
		var gaExists = false;

		// Determine if similar GA function is exists on the page, eg: from the template. #1343
		if (typeof gtag === 'function' || typeof ga === 'function' || typeof _gaq === 'function') {
			gaExists = true;
		}

		// We still load our own gtag method to be use in infinite scroll.
		window.dataLayer = window.dataLayer || [];
		window.ezb.gtag = function() {
			dataLayer.push(arguments);
		}

		window.ezb.gtag('js', new Date());

		// Track the page for the first time
		if (!gaExists) {
			window.ezb.gtag('config', trackingId);
		}
	<?php } ?>

	$('[data-eb-posts]').implement(EasyBlog.Controller.Entry, {
		"postId": <?php echo $post->id; ?>,
		"isEbd": <?php echo $post->isEbd() ? 'true' : 'false'; ?>,
		"autoload": true,
		"dropcap": <?php echo $this->config->get('layout_dropcaps') ? 'true' : 'false'; ?>,
		"ga_enabled": <?php echo ($this->config->get('main_google_analytics') && $this->config->get('main_google_analytics_id')) ? 'true' : 'false'; ?>,
		"ga_tracking_id": trackingId,
		"currentPageUrl": "<?php echo $this->fd->html('str.escape', $post->getExternalPermalink()); ?>",
		"isPreview": <?php echo $preview ? 'true' : 'false'; ?>,
		"userId" : <?php echo $this->my->id ? $this->my->id : 0; ?>,
		"isPollsEnabled": <?php echo $this->config->get('main_polls') ? 'true' : 'false'; ?>
	});
});
