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

if ($firstImage) {
	$content = EB::string()->removeFirstImage($content);
}

$adsContent = 'enable=true ad_density=' . $config->get('fb_ads_density', 'default');
if (EBString::strlen($content) <= 250) {
	$adsContent = 'false';
}

?>
<!DOCTYPE html>
<html lang="en" prefix="op: http://media.facebook.com/op#">
<head>
	<meta charset="utf-8">
	<link rel="canonical" href="<?php echo $post->getPermalink(true, true); ?>">
	<meta property="op:markup_version" content="v1.0">
	
	<?php if ($config->get('facebook_ads_placement_id', false)) { ?>
	<meta property="fb:use_automatic_ad_placement" content="<?php echo $adsContent; ?>">
	<?php } ?>
</head>
<body>
<article>
	<header>
		<?php if ($cover) { ?>
		<figure>
			<img src="<?php echo $cover; ?>" />
		</figure>
		<?php } ?>

		<?php if ($config->get('facebook_ads_placement_id', false)) { ?>
		<!-- Audience Network tag for slot #1 -->
		<figure class="op-ad">
			<iframe width="<?php echo $config->get('facebook_ads_width', 300); ?>" height="<?php echo $config->get('facebook_ads_height', 250); ?>" style="border:0; margin:0;" src="https://www.facebook.com/adnw_request?placement=<?php echo $config->get('facebook_ads_placement_id', false); ?>&adtype=banner<?php echo $config->get('facebook_ads_width', 300); ?>x<?php echo $config->get('facebook_ads_height', 250); ?>"></iframe>
		</figure>
		<?php } ?>

		<h1><?php echo $post->title;?></h1>

		<h3 class="op-kicker"><?php echo JText::sprintf('COM_EASYBLOG_INSTANT_ARTICLE_FEED_CATEGORY', $category->title);?></h3>

		<address><?php echo $author->getName();?></address>

		<!-- The published and last modified time stamps -->
		<time class="op-published" dateTime="<?php echo $post->getCreationDate(true)->toISO8601(true);?>"><?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?></time>
		<time class="op-modified" dateTime="<?php echo $post->getModifiedDate()->toISO8601(true);?>"><?php echo $post->getModifiedDate()->format(JText::_('DATE_FORMAT_LC1'));?></time>
	</header>

	<?php if ($post->hasLocation()) {  ?>
	<figure class="op-map">
		<script type="application/json" class="op-geotag">
		{
			"type": "Feature",
			"geometry": {
				"type": "Point",
				"coordinates": [<?php echo $post->latitude; ?>, <?php echo $post->longitude; ?>]
			},
			"properties": {
				"title": "<?php echo $post->address ?>",
				"pivot": true,
				"style": "satellite",
			}
		}
		</script>
	</figure>
	<?php } ?>
	
	<?php echo $content; ?>

	<?php if ($config->get('facebook_google_analytics', false)) { ?>
	<figure class="op-tracker">
		<iframe>
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', '<?php echo $config->get('main_google_analytics_id', false); ?>', 'auto');
			ga('send', 'pageview');
			</script>
		</iframe>
	</figure>
	<?php } ?>

	<footer>
		<aside><?php echo JText::sprintf('COM_EASYBLOG_INSTANT_ARTICLE_FEED_PUBLISHED_ON', $site);?></aside>
	</footer>
</article>
</body>
</html>
