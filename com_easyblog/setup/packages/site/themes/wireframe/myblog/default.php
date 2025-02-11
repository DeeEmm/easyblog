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
<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">

	<div class="eb-authors-head">
		<?php if ($this->config->get('layout_avatar') && $this->params->get('author_avatar', true)) { ?>
			<div class="t-flex-shrink--0 t-pr--md">
				<?php echo $this->html('avatar.user', $author, 'lg'); ?>
			</div>
		<?php } ?>

		<div class="t-flex-grow--1">
			<?php if ($this->params->get('author_name', true)) { ?>
			<h2 class="eb-authors-name reset-heading">
				<a href="<?php echo $author->getPermalink();?>" class="text-inherit"><?php echo $author->getName();?></a>
				<small class="eb-authors-featured eb-star-featured<?php echo !$author->isFeatured() ? ' hide' : '';?>" data-featured-tag data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>">
					<i class="fdi fa fa-star"></i>
				</small>
			</h2>
			<?php } ?>

			<div class="eb-authors-subscribe spans-seperator">
				<?php if ($author->getTwitterLink() && $this->params->get('author_twitter', true)) { ?>
				<span>
					<a href="<?php echo $author->getTwitterLink(); ?>" title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>">
						<i class="fdi fab fa-twitter-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>
					</a>
				</span>
				<?php } ?>

				<?php if (EB::friends()->hasIntegrations($author)) { ?>
				<span id="es">
					<?php echo EB::friends()->html($author);?>
				</span>
				<?php } ?>

				<?php if (EB::messaging()->hasMessaging($author->id)) { ?>
				<span id="es">
					<?php echo EB::messaging()->html($author);?>
				</span>
				<?php } ?>

				<?php if ($author->getWebsite() && $this->params->get('author_website', true)) { ?>
				<span id="es">
					<a href="<?php echo $author->getWebsite();?>" target="_blank">
						<i class="fdi fa fa-globe"></i>&nbsp; <?php echo $author->getWebsite();?>
					</a>
				</span>
				<?php } ?>

			</div>

			<div class="eb-authors-bio">
				<?php if ($this->params->get('author_bio', true)) { ?>
					<?php if ($this->config->get('blogger_author_truncate_bio', true)) { ?>
						<?php echo $this->fd->html('str.truncate', $author->getBiography(), 350); ?>
					<?php } else { ?>
						<?php echo $author->getBiography();?>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>


</div>

<!-- Post listings begins here -->
<div data-blog-listings>
	<div itemscope itemtype="http://schema.org/Blog" class="eb-posts <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-blog-posts>
		<!-- @module: easyblog-before-pagination -->
		<?php echo EB::renderModule('easyblog-before-entries');?>

		<?php if ($posts) { ?>
			<?php foreach ($posts as $blog) { ?>
				<?php if (!FH::isSiteAdmin() && $this->config->get('main_password_protect') && !empty($blog->blogpassword)) { ?>
					<!-- Password protected theme files -->
					<?php echo $this->output('site/blogs/latest/default.protected', array('post' => $blog, 'index' => 1, 'currentPageLink' => '')); ?>
				<?php } else { ?>
					<?php echo $this->output('site/blogs/latest/default.main', array('post' => $blog, 'index' => 1, 'currentPageLink' => '')); ?>
				<?php } ?>
			<?php } ?>
		<?php } else { ?>
			<div class="eb-empty">
				<i class="fdi fa fa-info-circle"></i>
				<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
			</div>
		<?php } ?>

		<!-- @module: easyblog-after-entries -->
		<?php echo EB::renderModule('easyblog-after-entries'); ?>
	</div>
</div>

<?php if($pagination) {?>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<!-- Pagination items -->
	<?php echo $pagination->getPagesLinks();?>

	<!-- @module: easyblog-after-pagination -->
	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
