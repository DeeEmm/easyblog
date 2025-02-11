<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
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

<form class="" name="tags" method="post" action="<?php echo JRoute::_('index.php'); ?>">
	<div class=""  uk-grid>
		<div class="uk-width-expand@m">
			<div class="uk-grid uk-grid-small" uk-grid>
				<?php if ($this->params->get('tag_search', true)) { ?>
				<div class="uk-width-expand@m">
					<div class="uk-inline uk-width-1-1">
						<span class="uk-form-icon" uk-icon="icon: tag"></span>
						<input name="filter-tags" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_TAGS', true);?>" class="uk-input">
					</div>
				</div>

				<div class="uk-width-auto@m">
					<button type="submit" class="uk-button uk-button-default"><?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?></button>
				</div>
				<?php } ?>

			</div>
		</div>
		<div class="uk-width-auto@m">
			<?php if ($this->params->get('tag_sorting', true)) { ?>
			<div class="uk-form-controls">
				<select class="uk-select" id="form-horizontal-select" data-tags-sorting>
					<option value="default" data-url="<?php echo EBR::_('index.php?option=com_easyblog&view=tags');?>"><?php echo JText::_('COM_EASYBLOG_TAGS_SORT_BY');?></option>
					<option value="title" <?php echo $ordering == 'title' ? ' selected="selected"' : '';?> data-url="<?php echo EBR::_($titleURL);?>"><?php echo JText::_('COM_EASYBLOG_TAGS_ORDER_BY_TITLE');?></option>
					<option value="postcount" <?php echo $ordering == 'postcount' ? ' selected="selected"' : '';?> data-url="<?php echo EBR::_($postURL);?>"><?php echo JText::_('COM_EASYBLOG_TAGS_ORDER_BY_POST_COUNT');?></option>
				</select>
			</div>
			<?php } ?>

		</div>

	</div>

	<?php echo $this->fd->html('form.action', 'tags.query'); ?>
</form>

<div class="uk-divider uk-margin-medium"></div>

<?php } ?>


<?php if($tags) { ?>
<div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>

	<?php foreach ($tags as $tag) { ?>
	<div class="">
		<div class="uk-card uk-card-default uk-card-small uk-card-body">
			<div class="" uk-grid>
				<div class="uk-width-auto">
					<?php if ($showRss) { ?>
					<a href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id, false, 'tag');?>" class="eb-tags-item__iconx">
						<i class="fdi fa fa-rss-square"></i>
					</a>
					<?php } ?>
				</div>

				<div class="uk-width-expand">
					<a href="<?php echo $tag->getPermalink();?>" title="<?php echo $this->fd->html('str.escape', $tag->title);?>" class="" uk-grid>
						<div class="uk-width-expand">
							<b><?php echo JText::_($tag->title);?></b>
						</div>
						<div class="uk-width-auto">
							<?php if ($this->params->get('tag_used_counter', true)) { ?>
							<i><?php echo $tag->post_count; ?></i>
							<?php } ?>
						</div>
					</a>
				</div>
			</div>
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

