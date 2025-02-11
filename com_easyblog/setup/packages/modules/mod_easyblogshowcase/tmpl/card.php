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
<div id="eb" class="eb-mod mod-easyblogshowcase-card mod-easyblogshowcase<?php echo $modules->getWrapperClass(); ?> <?php echo $modules->isMobile() ? 'is-mobile' : '';?>">
	<div class="t-mb--lg">
		<?php echo $modules->html('featured.slider', $posts, [
			'style' => 'card',
			'autoplay' => $autoplay,
			'autoplayInterval' => $autoplayInterval,
			'navigation' => true,
			'image' => $params->get('photo_show', true),
			'pickFirstImage' => $params->get('photo_legacy', true),
			'postTitle' => true,
			'postDate' => $params->get('contentdate', true),
			'postDateSource' => 'created',
			'postCategory' => true,
			'postContent' => true,
			'postContentSource' => $params->get('contentfrom', 'content'),
			'postContentLimit' => $params->get('textlimit', 200),
			'authorAvatar' => $params->get('authoravatar', true),
			'authorTitle' => $params->get('contentauthor', true),
			'readmore' => $params->get('showreadmore', true),
			'ratings' => $params->get('showratings', false),
			'cropCover' => $coverLayout->crop,
			'coverWidth' => $coverLayout->width,
			'coverHeight' => $coverLayout->height,
			'coverAlignment' => $coverLayout->alignment,
			'fromModule' => true,
			'showPlaceholder' => $params->get('show_cover_placeholder', true)
		]); ?>
	</div>

	<?php require(JModuleHelper::getLayoutPath('mod_easyblogshowcase', 'default_viewall')); ?>
</div>