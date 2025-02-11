<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!doctype html>
<html amp lang="<?php echo $langTag; ?>" <?php echo $isRtl ? 'dir="rtl"' : ''; ?>>
<head>
	<meta charset="utf-8">
	<base href="/"/>
	<?php if ($this->config->get('amp_analytics', false)) { ?>
		<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
	<?php } ?>
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>

	<?php if ($socialEnabled) { ?>
		<script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>
	<?php } ?>
	<?php if (strpos($ampContent, '<amp-iframe') !== false) { ?>
		<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
	<?php } ?>
	<?php if ($entryParams->get('post_related', true) && $relatedPosts) { ?>
		<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
		<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
	<?php } ?>

	<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>

	<?php if (in_array('instagram', $availableBlocks)) { ?>
		<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('pinterest', $availableBlocks)) { ?>
		<script async custom-element="amp-pinterest" src="https://cdn.ampproject.org/v0/amp-pinterest-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('dailymotion', $availableBlocks)) { ?>
		<script async custom-element="amp-dailymotion" src="https://cdn.ampproject.org/v0/amp-dailymotion-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('soundcloud', $availableBlocks)) { ?>
		<script async custom-element="amp-soundcloud" src="https://cdn.ampproject.org/v0/amp-soundcloud-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('vimeo', $availableBlocks)) { ?>
		<script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('twitter', $availableBlocks)) { ?>
		<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>
	<?php } ?>
	<?php if (in_array('facebook', $availableBlocks)) { ?>
		<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>
	<?php } ?>

	<?php if ($socialEnabled && $this->config->get('social_button_type') == 'addthis') { ?>
		<script async custom-element="amp-addthis" src="https://cdn.ampproject.org/v0/amp-addthis-0.1.js"></script>
	<?php } ?>

	<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
	<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>

	<title><?php echo $pageTitle; ?></title>
	<link rel="canonical" href="<?php echo $url; ?>" />
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Heebo" rel="stylesheet">
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "BlogPosting",
		"mainEntityOfPage": {
			"@type": "WebPage",
			"@id": "<?php echo $post->getPermalink(true, true); ?>"
		},
		"headline": "<?php echo $this->fd->html('str.escape', $post->getTitle()); ?>",
		"datePublished": "<?php echo $post->getPublishDate()->toISO8601(); ?>",
		"dateModified": "<?php echo $post->getModifiedDate()->toISO8601(); ?>",
		"author": {
			"@type": "Person",
			"name": "<?php echo $post->getAuthorName(); ?>"
		},
		"image": {
			"@type": "ImageObject",
			"url": "<?php echo $ampImageUrl; ?>"
		},
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo $siteName; ?>"<?php if ($logoObj) { ?>,
			"logo": {
				"@type": "ImageObject",
				"url": "<?php echo $logoObj->url; ?>",
				"width": "<?php echo $logoObj->width; ?>",
				"height": "<?php echo $logoObj->height; ?>"
			}
			<?php } ?>
		}
	}
	</script>

	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>

	<?php echo $this->output('site/entry/amp/stylesheets')?>
</head>
<body>

	<?php if ($this->config->get('amp_analytics', false)) { ?>
	<amp-analytics type="googleanalytics">
		<script type="application/json">
		{
			"vars": {
				"account": "<?php echo $this->config->get('main_google_analytics_id'); ?>"
			},
			"triggers": {
				"trackPageview": {
					"on": "visible",
					"request": "pageview"
				}
			}
		}
		</script>
	</amp-analytics>
	<?php } ?>

	<header>
		<span class="brand-logo">
			<?php echo $this->config->get('main_title'); ?>
		</span>
		<button on="tap:sidebar.toggle" class="toggle-btn" title="Toggle Sidebar" >
			☰
		</button>
	</header>

	<?php echo $this->output('site/entry/amp/sidebar', array('menuItems' => $menuItems)); ?>

	<?php if ($post->image && $coverInfo) { ?>
		<amp-img layout="responsive" <?php echo $coverInfo; ?> src="<?php echo $post->getImage('large', false, true);?>" ></amp-img>
	<?php } ?>

	<div class="blog-meta">
		<div class="blog-meta__author">
			<?php if ($post->hasAuthorAlias() || !EB::isBlogger($post->getAuthor()->id)) { ?>
				<?php echo JText::_('COM_EASYBLOG_AMP_BY'); ?> <?php echo $post->getAuthorName();?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYBLOG_AMP_BY'); ?> <a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName();?></a>
			<?php } ?>
			<?php echo JText::_('COM_EASYBLOG_AMP_ON'); ?> <?php echo $post->getDisplayDate('created')->format(JText::_('DATE_FORMAT_LC1')); ?>
		</div>

		<?php if ($post->category) { ?>
		<div class="blog-meta__cat">
			<?php echo JText::_('COM_EASYBLOG_AMP_CATEGORY'); ?> <a href="<?php echo $post->category->getPermalink();?>"><?php echo $post->category->getTitle();?></a>
		</div>
		<?php } ?>
	</div>

	<div class="blog-content">
		<div class="heading">
			<h1><a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle(); ?></a></h1>
		</div>

		<?php echo $adsense->header;?>

		<?php echo $ampContent; ?>

		<?php if ($socialEnabled) { ?>
			<?php if ($this->config->get('social_button_type') == 'addthis')  { ?>
				<amp-addthis width="320" height="92"  data-pub-id="<?php echo $this->config->get('social_addthis_customcode'); ?>" data-widget-id="uylx" data-widget-type="inline"></amp-addthis>
			<?php } else { ?>
				<div class="blog-social">
					<amp-social-share type="email" width="45" height="33"></amp-social-share>

					<?php if ($this->config->get('main_facebook_like', true)) { ?>
						<amp-social-share type="facebook" data-param-app_id="<?php echo $this->config->get('main_facebook_like_appid'); ?>" width="45" height="33"></amp-social-share>
					<?php } ?>

					<?php if ($this->config->get('main_twitter_button', true)) { ?>
						<amp-social-share type="twitter" width="45" height="33"></amp-social-share>
					<?php } ?>

					<?php if ($this->config->get('main_linkedin_button', true)) { ?>
						<amp-social-share type="linkedin" width="45" height="33"></amp-social-share>
					<?php } ?>

					<?php if ($this->config->get('main_pinit_button', true)) { ?>
						<amp-social-share type="pinterest" width="45" height="33"></amp-social-share>
					<?php } ?>

					<amp-social-share type="whatsapp" data-share-endpoint="whatsapp://send" data-param-text="<?php echo JText::sprintf('COM_EASYBLOG_AMP_WHATSAPP_SHARE', $this->fd->html('str.escape', $post->getTitle())); ?>" width="45" height="33"></amp-social-share>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<?php if ($entryParams->get('post_related', true) && $relatedPosts) { ?>
		<h4><?php echo JText::_('COM_EASYBLOG_AMP_RELATED_POSTS'); ?></h4>
		<amp-list width="300" height="75" layout="responsive" src="<?php echo $relatedUrl;?>">
			<template type="amp-mustache">
				<div class="related">
					<div class="related__img">
						<a class="related__link" href="{{url}}">
							<amp-img width="101" height="75" src="{{thumbnail}}"></amp-img>
						</a>
					</div>
					<div class="related__body">
						<a class="related__link" href="{{url}}">
							<span class="related__title">{{title}}</span>
						</a>
					</div>
				</div>
			</template>
		</amp-list>
	<?php } ?>


	<?php if ($post->allowComments()) { ?>
		<a class="btn-eb btn-eb-comment" href="<?php echo $post->getCommentsPermalink();?>"><?php echo JText::_('COM_EASYBLOG_AMP_LEAVE_COMMENTS'); ?></a>
	<?php } ?>

	<?php echo $adsense->footer;?>

</body>
</html>
