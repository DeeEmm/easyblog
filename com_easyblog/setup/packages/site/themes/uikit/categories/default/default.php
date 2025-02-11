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
<div class="eb-categories">
	<?php if ($categories) { ?>
		<?php foreach ($categories as $category) { ?>
		<div class="eb-category" data-category-wrapper>

			<?php echo $this->html('headers.category', $category, [
				'title' => $this->params->get('category_title', true),
				'description' => $this->params->get('category_description', true),
				'avatar' => $this->params->get('category_avatar', true),
				'subcategories' => $this->params->get('subcategories', true),
				'rss' => $this->params->get('category_rss', true),
				'subscription' => $this->params->get('category_subscriptions', true)
			]); ?>

			<?php if ($this->params->get('category_posts', true) || $this->params->get('category_authors', true)) { ?>
			<div class="eb-category-stats">
				<?php if ($totalTabs > 1) { ?>
				<ul class="uk-child-width-expand t-mb--lg" uk-tab>
					<?php if ($this->params->get('category_posts', true)) { ?>
					<li class="active">
						<a href="#posts-<?php echo $category->id;?>" data-bp-toggle="tab">
							<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_TOTAL_POSTS');?>
						</a>
					</li>
					<?php } ?>

					<?php if ($this->params->get('category_authors', true)) { ?>
					<li>
						<a href="#authors-<?php echo $category->id; ?>" data-bp-toggle="tab" data-category-id="<?php echo $category->id; ?>" <?php echo ($category->authorsCount) ? 'data-tab-author' : ''; ?>>
							<?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACTIVE_BLOGGERS');?>
						</a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>

				<div class="eb-stats-content">
					<?php if ($this->params->get('category_posts', true)) { ?>
					<div class="tab-pane eb-simple-posts active <?php echo $this->isMobile() ? 'is-mobile' : '';?>" id="posts-<?php echo $category->id; ?>">
						<?php if ($category->blogs) { ?>
							<ul class="uk-list uk-list-divider uk-margin-small">
							<?php $i = 1; ?>
							<?php foreach ($category->blogs as $post) { ?>
								<?php if ($i <= $limitPreviewPost) { ?>
									<?php echo $this->html('post.list.simple', $post, $post->category->getParam('listing_date_source', 'created')); ?>
								<?php } ?>
								<?php $i++; ?>
							<?php } ?>
							</ul>

							<a href="<?php echo $category->getPermalink();?>" class="uk-button uk-button-link uk-button-small uk-width-1-1">
								<?php echo JText::_('COM_EASYBLOG_CATEGORIES_VIEW_ALL_POSTS');?>
							</a>
						<?php } else { ?>
							<div class="eb-empty">
								<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
							</div>
						<?php } ?>
					</div>
					<?php } ?>

					<?php if ($this->params->get('category_authors', true)) { ?>
					<div class="tab-pane eb-labels eb-stats-authors <?php echo !$this->params->get('category_posts', true) ? 'active' : '';?>" id="authors-<?php echo $category->id; ?>">
						<?php if ($category->authorsCount) { ?>
						<div class="center">
							<i class="eb-loader-o"></i>
						</div>
						<?php } else { ?>
							<div class="eb-empty"><?php echo JText::_('COM_EB_CATEGORIES_VIEW_NOT_ACTIVE_AUTHOR'); ?></div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NO_CATEGORY_AVAILABLE'); ?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
		<?php echo $pagination; ?>
	<?php } ?>
</div>
