EasyBlog.require()
.script('admin/vendors/flot')
.done(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'new') {
			window.location = '<?php echo EB::composer()->getComposeUrl(); ?>';
			return false;
		}

		return false;
	});

	// Flot will not build correctly if the DOM object is not currently on the
	// page (in this case hidden in another tab).

	// Initial load.
	var tabname = 'posts';
	var posts = <?php echo $postsCreated; ?>;
	var data = [{ data: posts, label: "<?php echo JText::_('COM_EASYBLOG_CHART_POSTS', true);?>" }];
	var placeholder = jQuery('[data-chart-'+tabname+']');
	var tick = getTicks(tabname);

	graphPlotting(placeholder, data, tick);

	// On each tab switch
	jQuery('[data-fd-tab]').on('click', function() {
		tabname = jQuery(this).attr('href').replace('#', '');

		// Exclude pending tab as it do not render graph.
		if (tabname != 'pending') {
			data = getData(tabname);
			placeholder = jQuery('[data-chart-'+tabname+']');
			tick = getTicks(tabname);

			// Need to set timeout to let the DOM to properly rendered.
			setTimeout(function() {
				graphPlotting(placeholder, data, tick);
			}, 50);

		}
	});

	function graphPlotting(placeholder, data, tick) {
		var options = {
			series: {
				lines: { show: true,
						lineWidth: 1,
						fill: true,
						fillColor: { colors: [ { opacity: 0.1 }, { opacity: 0.13 } ] }
					 },
				points: { show: true,
						 lineWidth: 2,
						 radius: 3
					 },
				shadowSize: 0,
				stack: true
			},
			grid: {
				hoverable: true,
				clickable: true,
				tickColor: "#f9f9f9",
				borderWidth: 0,
				backgroundColor: "#fff",
			},
			colors: ["#a7b5c5", "#30a0eb"],
			xaxis: {
				min: 0.0,
				max: 6,
				//mode: null,
				ticks: tick,
				tickLength: 0, // hide gridlines
				axisLabelUseCanvas: true,
				tickDecimals: 0,
				axisLabelFontSizePixels: 12,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 5
			},
			yaxis: {
				tickDecimals: 0
			},
			shadowSize: 0
		};

		$.plot(placeholder, data, options);
	}

	function getData(tabname) {
		if (tabname == 'posts') {
			var posts = <?php echo $postsCreated; ?>;
			return [{ data: posts, label: "<?php echo JText::_('COM_EASYBLOG_CHART_POSTS', true);?>" }];
		} else if (tabname == 'comments') {
			var comments = <?php echo $commentsCreated; ?>;
			return [{ data: comments, label: "<?php echo JText::_('COM_EASYBLOG_CHART_COMMENTS', true);?>" }];
		} else if (tabname == 'reactions') {
			var reactions = <?php echo $reactionCreated; ?>;
			return [{ data: reactions, label: "<?php echo JText::_('COM_EASYBLOG_REACTIONS', true);?>" }];
		}
	}

	function getTicks(tabname) {
		if (tabname == 'posts') {
			return <?php echo $commentsTicks;?>;
		} else if (tabname == 'comments') {
			return <?php echo $postsTicks;?>;
		} else if (tabname == 'reactions') {
			return <?php echo $reactionTicks;?>;
		}
	}
});
