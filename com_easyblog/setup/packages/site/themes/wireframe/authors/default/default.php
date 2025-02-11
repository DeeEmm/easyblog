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
<?php if ($this->params->get('authors_search', true) || $this->params->get('authors_sorting', true)) { ?>
<form name="authors" method="post" action="<?php echo JRoute::_('index.php'); ?>" class="eb-author-filter form-horizontal row-table <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<?php if ($this->params->get('authors_search', true)) { ?>
	<div class="col-cell">
		<div class="eb-authors-finder input-group">
			<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_BLOGGERS', true);?>" value="<?php echo $this->fd->html('str.escape', $search);?>" />
			<i class="fdi fa fa-user"></i>
			<div class="input-group-btn">
				<button type="submit" class="btn btn-default">
					<?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?>
				</button>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($this->params->get('authors_sorting', true)) { ?>
	<div class="col-cell">
		<?php echo $this->fd->html('form.dropdown', 'authorSort', $sort, [
			'default' => (object) ['title' => 'COM_EASYBLOG_BLOGGERS_SORT_BY', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger') . '"'],
			'alphabet' => (object) ['title' => 'COM_EASYBLOG_BLOGGERS_ORDER_BY_NAME', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger&sort=alphabet', false) . '"'],
			'active' => (object) ['title' => 'COM_EASYBLOG_BLOGGERS_ORDER_BY_ACTIVE', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger&sort=active', false) . '"'],
			'latest' => (object) ['title' => 'COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger&sort=latest', false) . '"'],
			'latestpost' => (object) ['title' => 'COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST_POST', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger&sort=latestpost', false) . '"'],
			'ordering' => (object) ['title' => 'COM_EB_BLOGGERS_ORDER_BY_COLUMN_ORDERING', 'attr' => 'data-url="' . EBR::_('index.php?option=com_easyblog&view=blogger&sort=ordering', false) . '"']
		], [
			'attr' => 'data-authors-sorting'
		]); ?>
	</div>
	<?php } ?>

	<?php echo $this->fd->html('form.action', 'search.blogger'); ?>
</form>
<?php } ?>

<div class="eb-authors" data-authors>
	<?php if ($authors) { ?>
		<?php foreach ($authors as $author) { ?>
			<div class="eb-author <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-item data-id="<?php echo $author->id;?>">

				<?php echo $this->html('headers.author', $author, [
					'avatar' => $this->params->get('author_avatar', true),
					'rss' => $this->params->get('author_subscribe_rss', true),
					'subscription' => $this->params->get('author_subscribe_email', true),
					'twitter' => $this->params->get('author_twitter', true),
					'website' => $this->params->get('author_website', true),
					'biography' => $this->params->get('author_bio', true),
					'isActivateBioTruncation' => $this->params->get('author_truncate_bio', true),
					'featureAction' => true
				]); ?>

				<?php if ($this->params->get('author_posts', true)) { ?>
				<div class="eb-authors-stats">

					<?php if ($author->blogs) { ?>
					<div class="eb-authors-stats-heading t-mb--lg">
						<?php echo JText::_('COM_EB_POSTS_BY_AUTHOR');?>
					</div>
					<?php } ?>

					<div class="eb-stats-content">
						<div class="tab-pane eb-simple-posts active <?php echo $this->isMobile() ? 'is-mobile' : '';?>" id="posts-<?php echo $author->id;?>">
							<?php if ($author->blogs) { ?>
								<?php $i = 1; ?>

								<?php foreach ($author->blogs as $post) { ?>
									<?php if ($i <= $limitPreviewPost) { ?>
										<?php echo $this->html('post.list.simple', $post, $this->config->get('blogger_post_date_source', 'created')); ?>
									<?php } ?>
									<?php $i++; ?>
								<?php } ?>

								<a href="<?php echo $author->getPermalink();?>" class="btn btn-default t-mt--md">
									<?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?>
								</a>
							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND');?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fdi fa fa-users"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_AUTHORS_CURRENTLY'); ?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
		<?php echo $pagination; ?>
	<?php } ?>
</div>
