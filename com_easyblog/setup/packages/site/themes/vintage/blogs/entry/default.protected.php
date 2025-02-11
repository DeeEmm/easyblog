<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div itemscope itemtype="http://schema.org/BlogPosting">
	<div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf" data-id="<?php echo $post->id;?>">

		<?php if ($hasAdminTools || $preview || $post->isFeatured()) { ?>
		<div class="eb-entry-tools row-table">
			<?php if ($post->isFeatured()) { ?>
			<div class="col-cell">
				<div class="eb-post-featured">
					<i class="fdi fa fa-star"></i>
				</div>
			</div>
			<?php } ?>

			<?php if (!$preview && $hasAdminTools) { ?>
			<div class="col-cell cell-tight">
				<?php echo $this->html('post.admin', $post, $post->getPermalink(false)); ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<!-- @module: easyblog-before-entry -->
		<?php echo EB::renderModule('easyblog-before-entry'); ?>

		<div class="eb-entry-head">
			<?php if ($this->params->get('show_title', true)) { ?>
			<h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-entry-title reset-heading <?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
			<?php } ?>

			<?php if ($this->entryParams->get('post_author', true)) { ?>
			<div class="eb-horizonline">
				<div class="eb-horizonline-inner">
					<?php echo $this->html('avatar.user', $post->creator, 'sm'); ?>

					<div class="eb-post-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
						<span itemprop="name">
							<a href="<?php echo $post->getAuthorPermalink();?>" itemprop="url" rel="author"><?php echo $post->getAuthorName();?></a>
						</span>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="eb-entry-meta text-muted">
				<?php if ($hasEntryTools) { ?>
				<div class="eb-entry-tools row-table mb-15">
					<?php echo $this->output('site/blogs/entry/tools', array('return' => $post->getPermalink(false))); ?>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_date', true)) { ?>
				<div class="eb-entry-date">
					<i class="fdi far fa-clock"></i>
					<time class="eb-meta-date" itemprop="datePublished" content="<?php echo $post->getCreationDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>

				</div>
				<?php } ?>

				<?php if ($this->params->get('show_author', true)) { ?>
				<div class="eb-meta-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
					<i class="fdi fa fa-pencil-alt"></i>
					<span itemprop="name">
						<a href="<?php echo $post->getAuthorPermalink();?>" itemprop="url" rel="author"><?php echo $post->getAuthorName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_category', true)) { ?>
					<div class="eb-meta-category comma-seperator">
						<i class="fdi far fa-folder-open"></i>
						<?php foreach ($post->categories as $cat) { ?>
						<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ($this->params->get('show_hits', true)) { ?>
				<div class="eb-meta-views">
					<i class="fdi fa fa-eye"></i>
					<?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
				</div>
				<?php } ?>
			</div>
		</div>

		<div class="eb-entry-body clearfix">
			<div class="eb-entry-article clearfix" itemprop="articleBody">
				<?php echo $this->output('site/blogs/tools/protected.form'); ?>
			</div>
		</div>
	</div>
</div>

<?php if ($prevId) { ?>
<hr class="eb-hr" />
<?php } ?>
