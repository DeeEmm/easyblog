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

<div class="eb-featured eb-featured--uikit <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div data-eb-slider data-autoplay="<?php echo $slider->autoplay;?>" data-interval="<?php echo $slider->autoplayInterval;?>" data-free-mode="0">
		<div class="eb-gallery-stage">
			<div class="swiper-container" data-container>
				<div class="swiper-wrapper">
					<?php foreach ($posts as $post) { ?><div class="eb-gallery-item swiper-slide"> <!--PLEASE KEEP THIS DOM THIS WAY TO REMOVE WHITESPACING-->
						<div class="eb-gallery-box">
							<?php if ($postOption->image && $post->coverImage) { ?>
							<div class="eb-showcase-thumb eb-post-thumb is-<?php echo $coverOption->alignment;?>">
								<?php if (!$coverOption->crop) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image"
										style="width: <?php echo $coverOption->width;?>px"
									>
										<img src="<?php echo $post->coverImage;?>" alt="<?php echo $this->escape($post->getImageTitle());?>" />
									</a>
								<?php } ?>

								<?php if ($coverOption->crop) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image-cover"
										style="
											background-image: url('<?php echo $post->coverImage;?>');
											width: <?php echo $coverOption->width;?>px;
											height: <?php echo $coverOption->height;?>px;"
									></a>
								<?php } ?>
							</div>
							<?php } ?>

							<div class="eb-gallery-body">
								<?php if ($postOption->authorAvatar) { ?>
								<div class="eb-gallery-avatar">
									<?php echo $this->html('avatar.user', $post->creator, 'sm'); ?>
								</div>
								<?php } ?>

								<?php if ($postOption->title) { ?>
								<h2 class="eb-gallery-title">
									<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
								</h2>
								<?php } ?>

								<div class="eb-gallery-meta">
									<?php if ($postOption->authorAvatar) { ?>
									<span>
										<a href="<?php echo $post->getAuthorPermalink(); ?>">
											<?php echo $post->getAuthorName(); ?>
										</a>
									</span>
									<?php } ?>

									<?php if ($postOption->category) { ?>
										<?php foreach ($post->getCategories() as $category) { ?>
										<span>
											<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
										</span>
										<?php } ?>
									<?php } ?>

									<?php if ($postOption->date) { ?>
									<span>
										<time><?php echo $post->getDisplayDate($postOption->dateSource)->format(JText::_('DATE_FORMAT_LC1'));?></time>
									</span>
									<?php } ?>
								</div>

								<?php if ($postOption->content) { ?>
								<div class="eb-gallery-content">
									<?php echo $post->displayContent;?>
								</div>
								<?php } ?>

								<?php if ($postOption->ratings) { ?>
								<div class="eb-post-rating" data-ratings>
									<div class="eb-rating">
										<?php echo EB::ratings()->html($post, 'featured-' . uniqid() . '-ratings'); ?>
									</div>
								</div>
								<?php } ?>

								<?php if ($postOption->readmore) { ?>
								<div class="eb-showcase-more uk-margin-top">
									<a class="uk-button uk-button-default uk-button-small" href="<?php echo $post->getPermalink();?>" aria-label="<?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?>: <?php echo $this->fd->html('str.escape', $post->getTitle());?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
								</div>
								<?php } ?>
							</div>
						</div>
					</div><?php } ?> <!--PLEASE KEEP THIS DOM THIS WAY TO REMOVE WHITESPACING-->
				</div>
			</div>

			<?php if (count($posts) > 1) { ?>
			<div class="eb-showcase-control uk-button-group" data-featured-navigation-buttons>
				<div class="uk-button uk-button-default" data-featured-previous>
					<span class="fdi fa fa-angle-left"></span>
				</div>
				<div class="uk-button uk-button-default" data-featured-next>
					<span class="fdi fa fa-angle-right"></span>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
