<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-search" data-eb-search-wrapper>
	<form class="form-horizontal l-stack" action="<?php echo JRoute::_('index.php');?>" method="post" data-eb-search-form>
		<div class="eb-bar eb-bar--search-filter-bar">
			<div class="t-d--flex sm:t-flex-direction--c t-flex-grow--1">
				<div class="sm:t-mb--md lg:t-mr--xs t-min-width--0 ">
					<div class="dropdown_">
						<?php echo $this->fd->html('button.standard', call_user_func(function() use ($activeCategory) {
							ob_start();
							?>
								<b><?php echo JText::_('COM_EB_SEARCH_CATEGORY_LABEL'); ?></b>&nbsp;
								<span data-category-title>
									<?php if ($activeCategory->id) { ?>
										<?php echo $activeCategory->getTitle();?>
									<?php } else { ?>
										<?php echo JText::_('COM_EB_SEARCH_CATEGORY_ALL_CATEGORIES'); ?>
									<?php } ?>
								</span>
								&nbsp;
								<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
							<?php
							$contents = ob_get_contents();
							ob_end_clean();

							return $contents;

						}), 'default', 'default', [
							'class' => 'dropdown-toggle_ t-w--100',
							'attributes' => 'data-bp-toggle="dropdown"'
						]);?>

						<div class="dropdown-menu  sm:t-mb--md t-text--truncate dropdown-menu--eb-search-category" data-eb-category-container >
							<div class="o-loader-wrapper">
								<div class="o-loader o-loader--inline"></div>
							</div>

							<?php echo $this->output('site/search/category', ['rootLevel' => true, 'categories' => $categories, 'activeCategoryId' => $activeCategoryId]); ?>
						</div>
					</div>
				</div>

				<?php if ($tags) { ?>
					<div class="sm:t-mb--md lg:t-mr--sm">
						<div class="dropdown_">
							<?php echo $this->fd->html('button.standard', call_user_func(function() {
								ob_start();
								?>
									<?php echo JText::_('COM_EB_SEARCH_TAG_TAGS_LABEL'); ?> (<span data-eb-tags-count>0</span>)
									<i class="fdi fa fa-plus t-ml--lg t-text--500"></i>
								<?php
								$contents = ob_get_contents();
								ob_end_clean();

								return $contents;

							}), 'default', 'default', [
								'class' => 'dropdown-toggle_ t-w--100',
								'attributes' => 'data-bp-toggle="dropdown"'
							]);?>

							<div class="dropdown-menu t-mt--2xs sm:t-w--100 dropdown-menu--eb-search-tags" data-eb-tags-container>
								<?php echo $this->output('site/search/tags', ['tags' => $tags, 'activeTagIds' => $activeTagIds]); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="t-flex-grow--1">
					<input type="text" class="eb-search-filter-input t-w--100" autocomplete="off" placeholder="<?php echo JText::_('COM_EB_SEARCH_PLACEHOLDER', true); ?>" 
						data-eb-search-input
						name="query" 
						value="<?php echo $this->fd->html('str.escape', $query);?>">
				</div>
			</div>
			<div class="t-d--flex t-align-items--c sm:t-pl--no lg:t-pl--md sm:t-pt--md sm:t-justify-content--c">
				<?php echo $this->fd->html('button.submit', '', 'default', 'md', [
					'icon' => 'fdi fa fa-search',
					'ghost' => true
				]); ?>
			</div>
		</div>

		<div class="eb-bar eb-bar--filter-bar" data-eb-search-result-bar>
			<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
				<div class="t-flex-grow--1 sm:t-mb--md">

					<div class="<?php echo !$posts ? ' eb-empty t-p--no' : '';?>">
						<?php if ($posts) { ?>
							<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_TOTAL_RESULT', $pagination->get('pages.current'), $pagination->get('pages.total'), $pagination->get('total'), $query); ?>
						<?php } elseif (!$posts && $query) { ?>
							<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_EMPTY', $query); ?>
						<?php } else { ?>
							<?php echo JText::_('COM_EB_SEARCH_WITHOUT_KEYWORD'); ?>
						<?php } ?>
					</div>
				</div>

				<?php if ($posts) { ?>
				<div class="t-d--flex sm:t-flex-direction--c sm:t-w--100">
					<div class="dropdown_ t-mr--md sm:t-mb--md sm:t-mr--no">
						<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1  t-d--flex t-align-items--c t-w--100 sm:t-justify-content--c">
							<?php echo JText::_('COM_EB_SEARCH_SORT_BY'); ?> &nbsp;
							<span data-sort-title>
								<?php echo JText::_('COM_EB_SEARCH_SORT_DEFAULT'); ?>
							</span>
							<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
						</a>
						<ul data-sort-container class="dropdown-menu dropdown-menu--filter-menu sm:t-mb--md t-text--truncate has-active-markers" style="width: 220px;overflow: hidden;">
							<li class="">
								<a href="javascript:void(0);" data-sort-filter="created" title="<?php echo JText::_('COM_EB_SEARCH_SORT_CREATION_DATE', true);?>">
									<?php echo JText::_('COM_EB_SEARCH_SORT_CREATION_DATE'); ?>
								</a>
							</li>
							<li class="">
								<a href="javascript:void(0);" data-sort-filter="publish_up" title="<?php echo JText::_('COM_EB_SEARCH_SORT_PUBLISH_DATE', true);?>">
									<?php echo JText::_('COM_EB_SEARCH_SORT_PUBLISH_DATE'); ?>
								</a>
							</li>
							<li class="">
								<a href="javascript:void(0);" data-sort-filter="hits" title="<?php echo JText::_('COM_EB_SEARCH_SORT_MOST_POPULAR', true);?>">
									<?php echo JText::_('COM_EB_SEARCH_SORT_MOST_POPULAR'); ?>
								</a>
							</li>
						</ul>
					</div>

					<div class="dropdown_ sm:t-mr--no">
						<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1  t-d--flex t-align-items--c t-w--100 sm:t-justify-content--c">
							<span data-order-title>
								<?php echo JText::_('COM_EB_SEARCH_SORT_DEFAULT'); ?>
							</span>
							<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
						</a>
						<ul data-order-container class="dropdown-menu dropdown-menu--filter-menu sm:t-mb--md t-text--truncate has-active-markers" style="width: 220px;overflow: hidden;">
							<li class="active">
								<a href="javascript:void(0);" data-order-filter="asc" title="<?php echo JText::_('COM_EB_SEARCH_ORDERING_ASCENDING', true);?>">
									<?php echo JText::_('COM_EB_SEARCH_ORDERING_ASCENDING'); ?>
								</a>
							</li>
							<li class="">
								<a href="javascript:void(0);" data-order-filter="desc" title="<?php echo JText::_('COM_EB_SEARCH_ORDERING_DESCENDING', true);?>">
									<?php echo JText::_('COM_EB_SEARCH_ORDERING_DESCENDING'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

		<input type="hidden" name="category_id" value="<?php echo $activeCategoryId ? $activeCategoryId : ''; ?>" data-category-id />
		<input type="hidden" name="tag_ids" value="" data-tag-ids />
		<input type="hidden" name="sort" value="<?php echo $sort; ?>" data-sort-input />
		<input type="hidden" name="ordering" value="<?php echo $ordering; ?>" data-order-input />

		<?php echo $this->fd->html('form.action', 'search.query'); ?>
	</form>
	<div class="o-loader-wrapper">
		<div class="o-loader o-loader--inline"></div>
	</div>
</div>

<hr class="t-border--0" />

<?php if ($posts) { ?>
<div class="eb-posts eb-posts-search" data-eb-search-content>
	<?php foreach ($posts as $post) { ?>
	<div class="eb-post clearfix">
		<div class="eb-post-content">
			<h2 class="eb-post-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
			</h2>

			<div class="eb-post-article">
				<?php echo $post->content;?>
			</div>

			<div class="eb-post-meta text-muted t-pl--no t-border--0 ">
				<div class="eb-post-date">
					<time><?php echo $this->fd->html('str.date', $post->created, JText::_('DATE_FORMAT_LC'));?></time>
				</div>

				<div class="eb-post-author">
					<a href="<?php echo $post->getAuthor()->getPermalink();?>"><?php echo $post->getAuthor()->getName(); ?></a>
				</div>

				<?php foreach ($post->categories as $category) { ?>
				<div>
					<div class="eb-post-category comma-seperator">
						<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<?php echo $pagination->getPagesLinks();?>

<?php } ?>
