<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!-- TODO Need to update DOM to follow default.main -->
<div class="eb-post" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
	<div class="eb-post-content">
		<div class="eb-post-head">
			<?php echo $this->html('post.admin', $post, $return); ?>

			<!-- Quote type -->
			<?php if ($post->posttype == 'quote') { ?>
			<div class="eb-post-headline">
				<h2 class="eb-post-title reset-heading">
					<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo nl2br($post->title);?></a>
				</h2>

				<div class="eb-post-headline-source">
					<?php echo $post->getContent(); ?>
				</div>
			</div>
			<?php } ?>

			<!-- Link type -->
			<?php if ($post->posttype == 'link') { ?>
			<?php $link = $post->getAsset('link')->getValue(); ?>
			<div class="eb-post-headline">
				<h2 class="eb-placeholder-link-title eb-post-title reset-heading">
					<a href="<?php echo $post->getPermalink();?>"><?php echo nl2br($post->title);?></a>
				</h2>

				<div class="eb-post-headline-source">
					<a href="<?php echo $post->getAsset('link'); ?>" target="_blank">
						<?php echo EB::string()->htmlAnchorLink($link, $link); ?>
					</a>
				</div>
			</div>
			<?php } ?>

			<!-- Twitter type -->
			<?php if ($post->posttype == 'twitter') { ?>
			<?php $screen_name = $post->getAsset('screen_name')->getValue();
				  $created_at = EB::date($post->getAsset('created_at')->getValue(), true)->format(JText::_('DATE_FORMAT_LC'));
			?>
			<div class="eb-post-headline">
				<h2 class="eb-post-title-tweet reset-heading">
					<?php echo $post->content;?>
				</h2>

				<?php if (!empty($screen_name) && !empty($created_at)) { ?>
				<div class="eb-post-headline-source">
						<?php echo '@'.$screen_name.' - '.$created_at; ?>
						&middot;
						<a href="<?php echo $post->getPermalink();?>">
							<?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
						</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<!-- Photo/Video/Standard type -->
			<?php if ((in_array($post->posttype, array('photo', 'standard', 'video', 'email'))) && $this->params->get('post_title', true)) { ?>
			<h2 class="eb-post-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
			</h2>
			<?php } ?>


			<?php if ($this->params->get('post_date', true) || $this->params->get('post_author', true) || $this->params->get('post_category', true)) { ?>
			<div class="eb-post-meta text-muted">
				<?php if ($post->isFeatured) { ?>
				<div class="eb-post-featured">
					<b><?php echo JText::_('COM_EASYBLOG_FEATURED');?></b>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_author', true)) { ?>
				<div class="eb-post-author">
					<i class="fdi fa fa-user"></i>
					<span>
						<a href="<?php echo $post->getAuthorPermalink();?>" rel="author"><?php echo $post->getAuthorName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
				<div class="eb-post-meta-team">
					<?php echo $this->html('avatar.team', $post->getBlogContribution(), 'sm'); ?>

					<span>
						<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="">
							<?php echo $post->getBlogContribution()->getTitle();?>
						</a>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_date', true)) { ?>
				<div class="eb-post-date">
					<i class="fdi far fa-clock"></i>
					<time class="eb-meta-date" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_category', true) && $post->categories) { ?>
				<div class="">
					<div class="eb-post-category comma-seperator">
						<i class="fdi far fa-folder-open"></i>
						<?php foreach ($post->categories as $category) { ?>
						<span>
							<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
						</span>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>

		<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
			<div class="eb-post-protected">
				<?php echo $this->output('site/blogs/tools/protected.form', array('post' => $post)); ?>
			</div>
		<?php } ?>

		<?php if ($post->posttype == 'link') { ?>
		asdadsad
		<?php } ?>

		<?php if ($post->posttype == 'video') { ?>
		<div class="eb-post-video">
			<?php foreach ($post->videos as $video) { ?>
			<div class="eb-responsive-video">
				<?php echo $video->html;?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if ($post->hasReadmore() && $this->params->get('post_readmore', true)) { ?>
			<div class="eb-post-more text-right mt-10">
				<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
			</div>
		<?php } ?>

		<div class="eb-post-foot">
			<div class="row-table">
				<div class="col-cell">
					<?php if ($this->params->get('post_type', false)) { ?>
						<span class="eb-post-type mr-10">
							<?php echo $this->html('post.icon', $post->getType()); ?>
						</span>
					<?php } ?>

					<?php if ($this->params->get('post_hits', true)) { ?>
						<span class="eb-post-hits mr-10">
							<i class="fdi fa fa-eye"></i> <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
						</span>
					<?php } ?>

					<?php if ($this->params->get('post_comment_counter', true)) { ?>
						<span class="eb-post-comments">
							<i class="fdi fa fa-comments"></i>
							<a href="<?php echo $post->getPermalink();?>"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->getTotalComments(), true); ?></a>
						</span>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>

	<?php echo $this->html('post.list.schema', $post); ?>
</div>
