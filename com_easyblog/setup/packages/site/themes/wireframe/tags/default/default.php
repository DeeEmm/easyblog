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

<?php if ($this->params->get('tag_search', true) || $this->params->get('tag_sorting', true)) { ?>
<form name="tags" method="post" action="<?php echo JRoute::_('index.php'); ?>" class="eb-tags-filter row-table form-horizontal <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div class="col-cell">
		<?php if ($this->params->get('tag_search', true)) { ?>
		<div class="eb-tags-finder input-group">
			<input type="text" class="form-control" name="filter-tags" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_TAGS', true);?>" />
			<i class="fdi fa fa-tags"></i>
			<div class="input-group-btn">
				<button type="submit" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?></button>
			</div>
		</div>
		<?php } ?>
	</div>

	<?php if ($this->params->get('tag_sorting', true)) { ?>
	<div class="col-cell">
		<?php echo $this->fd->html('form.dropdown', 'tagsOrdering', $ordering, [
			'default' => (object) ['title' => 'COM_EASYBLOG_TAGS_SORT_BY', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=tags') . '"'],
			'title' => (object) ['title' => 'COM_EASYBLOG_TAGS_ORDER_BY_TITLE', 'attr' => 'data-url="' . EBR::_($titleURL) . '"'],
			'postcount' => (object) ['title' => 'COM_EASYBLOG_TAGS_ORDER_BY_POST_COUNT', 'attr' => 'data-url="' . EBR::_($postURL) . '"']
		], ['attr' => 'data-tags-sorting']); ?>
	</div>
	<?php } ?>

	<?php echo $this->fd->html('form.action', 'tags.query'); ?>
</form>
<?php } ?>

<?php if($tags) { ?>
<div class="eb-tags-list fd-cf">
	<?php foreach ($tags as $tag) { ?>
	<div class="eb-tags-grid">
		<div class="eb-tags-item">
			<?php if ($showRss) { ?>
			<a href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id, false, 'tag');?>" class="eb-tags-item__icon">
				<i class="fdi fa fa-rss-square"></i>
			</a>
			<?php } ?>

			<a href="<?php echo $tag->getPermalink();?>" title="<?php echo $this->fd->html('str.escape', $tag->title);?>" class="eb-tags-item__link">
				<b><?php echo JText::_($tag->title);?></b>

				<?php if ($this->params->get('tag_used_counter', true)) { ?>
				<i><?php echo $tag->post_count; ?></i>
				<?php } ?>
			</a>
		</div>
	</div>
	<?php } ?>
</div>


<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination->getPagesLinks();?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>


<?php } else { ?>
	<div class="eb-empty">
		<i class="fdi far fa-paper-plane"></i>
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE');?>
	</div>
<?php } ?>

