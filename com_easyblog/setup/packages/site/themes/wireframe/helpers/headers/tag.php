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
<div class="eb-tag">
	<?php if ($showTitle) { ?>
	<div class="eb-tag-head">
		<h2 class="eb-tag-name reset-heading">
			<a href="<?php echo $tag->getPermalink();?>">
				<span class="col-cell">
					<i class="fdi fa fa-tag text-muted"></i>
				</span>
				<span class="col-cell"><?php echo $tag->getTitle();?></span>
			</a>
		</h2>

		<?php if ($tag->getDescription()) { ?>
		<div class="eb-authors-bio">
			<?php echo $this->fd->html('str.truncate', $tag->getDescription(), 350); ?>
		</div>
		<?php } ?>

	</div>
	<?php } ?>

	<?php if ($showRss) { ?>
	<div class="eb-tag-bio">
		<span class="eb-tag-rss">
			<i class="fdi fa fa-rss-square"></i>
			<a href="<?php echo $tag->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS', true); ?>" class="link-rss" target="_blank">
				<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS'); ?>
			</a>
		</span>
	</div>
	<?php } ?>
</div>
