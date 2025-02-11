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
<div class="eb-mag eb-mag-layout">
	<h6 class="eb-mag-header-title">
		<?php echo JText::_('COM_EASYBLOG_RECENT_NEWS'); ?>
	</h6>
	<?php if ($leadingArticle) { ?>
	<div class="eb-mag-post has-border">
		<div class="eb-mag-head">
			<?php echo $this->output('site/magazine/default/cover', ['post' => $leadingArticle, 'imageSize' => 'large']); ?>
		</div>
		<div class="eb-mag-body">
			<h1 class="eb-mag-post-title">
				<a href="<?php echo $leadingArticle->getPermalink(); ?>"><?php echo $leadingArticle->title; ?></a>
			</h1>
			<p><?php echo $this->html('post.list.content', $leadingArticle, '', false); ?></p>

			<?php if ($this->params->get('magazine_leading_article_readmore', true)) { ?>
				<a class="magazine-btn magazine-btn-more" href="<?php echo $leadingArticle->getPermalink();?>"><?php echo JText::_('COM_EB_CONTINUE_READING');?></a>
			<?php } ?>
		</div>
		<?php if ($this->params->get('magazine_lading_show_date', true)) { ?>
		<div class="eb-mag-date">
			<time class="eb-mag-meta-date">
				<?php echo $leadingArticle->getDisplayDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
			</time>
		</div>
		<?php } ?>

		<?php echo $this->html('post.list.schema', $leadingArticle); ?>
	</div>
	<?php } ?>

	<?php if ($posts) { ?>
	<div class="eb-mag-grids">
		<?php foreach ($posts as $post) { ?>
		<div class="eb-mag-grids__item">
			<div>
				<?php if (!$this->params->get('magazine_hide_cover', true)) { ?>
					<div class="eb-mag-thumb">
						<?php echo $this->output('site/magazine/default/cover', ['post' => $post, 'imageSize' => 'medium']); ?>
					</div>
				<?php } ?>

				<div class="eb-mag-title" itemprop="name headline">
					<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>
				</div>
				<div class="eb-mag-body">
					<p><?php echo $this->html('post.list.content', $post, '', false); ?></p>

					<?php if ($this->params->get('list_article_readmore', true)) { ?>
					<div class="">
						<a class="magazine-btn magazine-btn-more" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EB_CONTINUE_READING');?></a>
					</div>
					<?php } ?>
				</div>

				<?php if ($this->params->get('magazine_show_date', true)) { ?>
				<div class="eb-mag-foot">
					<time class="eb-mag-meta-date"]>
						<?php echo $post->getDisplayDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
				<?php } ?>

				<?php echo $this->html('post.list.schema', $leadingArticle); ?>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<div class="eb-more">
		<a href="<?php echo $viewAll;?>" class="eb-more__btn"><?php echo JTexT::_('COM_EASYBLOG_VIEW_ALL_POSTS');?> <i class="fdi fa fa-chevron-right"></i></a>
	</div>
</div>
