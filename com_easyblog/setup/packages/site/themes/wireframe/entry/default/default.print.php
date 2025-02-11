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
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $post->title; ?></title>
	<link href="<?php echo JURI::root();?>components/com_easyblog/themes/wireframe/styles/style-<?php echo EB::getLocalVersion();?>.min.css" rel="stylesheet" />
	<script type="text/javascript">
	window.print();
	</script>
</head>

<body>
<?php if ($isMobile) { ?>
<style type="text/css">
/* Google mobile-friendly test tool font size minimum requirement */	
body {
	font-size:16px;
}
</style>
<?php } ?>

<div id="eb">
	<h1><?php echo $post->title; ?></h1>

	<div class="title-wrapper no-avatar">
		<div class="meta1">
			<div class="inner">
				<span class="post-date"><?php echo $post->getCreationDate()->format($this->config->get('layout_shortdateformat', JText::_('DATE_FORMAT_LC1'))); ?></span>

				<span class="post-category">
					<?php echo JText::sprintf('COM_EASYBLOG_POSTED_BY_AUTHOR', $post->getAuthorPermalink(), $post->getAuthorName()); ?>
					<?php echo JText::sprintf('COM_EASYBLOG_IN', $post->category->getPermalink(), $this->escape($post->category->getTitle())); ?>
				</span>
			</div>
		</div>
	</div>

	<?php echo $post->event->afterDisplayTitle; ?>

	<?php echo $post->event->beforeDisplayContent; ?>

	<div class="post-content clearfix">
		<?php echo $this->html('post.entry.content', $post, $content, [
					'showCover' => $this->entryParams->get('post_image', false),
					'showCoverPlaceholder' => $this->entryParams->get('post_image_placeholder', false),
					'requireLogin' => $requireLogin
				]); ?>
	</div>
	<?php echo $post->event->afterDisplayContent; ?>

	<?php if ($post->copyrights && $this->entryParams->get('post_copyrights', true)) { ?>
	<div class="post-copyright clearfix">
		<?php echo $this->html('post.copyrights', $post->copyrights); ?>
	</div>
	<?php } ?>

	<?php if ($this->entryParams->get('post_tags', true)) { ?>
	<div class="post-tags">
		<?php echo $this->html('post.tags', $post->tags); ?>
	</div>
	<?php } ?>

	<a rel="nofollow" onclick="window.print();" title="<?php echo JText::_('PRINT'); ?>" href="javascript: void(0)"><?php echo JText::_('PRINT'); ?></a>
</div>
</body>
</html>