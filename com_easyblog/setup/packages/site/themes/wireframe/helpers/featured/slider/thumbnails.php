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
<div class="eb-featured eb-featured--thumb <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div class="eb-gallery-stage" data-eb-slider data-autoplay="<?php echo $slider->autoplay;?>" data-interval="<?php echo $slider->autoplayInterval;?>">
		<div class="eb-gallery-viewport">
			<div class="swiper-container gallery-top" data-container>
				<div class="swiper-wrapper">
					<?php foreach ($posts as $post) { ?>
					<div class="eb-gallery-item swiper-slide">
						<div class="eb-gallery-box" style="background-image: url('<?php echo $post->coverImage;?>') !important;">
							<div class="eb-gallery-body">
								<?php if ($postOption->authorAvatar) { ?>
									<?php echo $this->html('avatar.user', $post->getAuthor()); ?>
								<?php } ?>

								<?php if ($postOption->title) { ?>
								<h2 class="eb-gallery-title">
									<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
								</h2>
								<?php } ?>

								<div class="eb-gallery-meta">
									<?php if ($postOption->authorTitle) { ?>
									<span>
										<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->getAuthor()->getName(); ?></a>
									</span>
									<?php } ?>

									<?php if ($postOption->category) { ?>
										<?php foreach ($post->getCategories() as $category) { ?>
										<span>
											<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
										</span>
										<?php } ?>
									<?php } ?>
								</div>

								<?php if ($postOption->content) { ?>
								<div class="eb-gallery-content">
									<span style="color: #fff;">
										<?php echo $post->displayContent; ?>
									</span>
								</div>
								<?php } ?>

								<?php if ($postOption->ratings) { ?>
								<div class="eb-post-rating" data-ratings>
									<div class="eb-rating">
										<?php echo EB::ratings()->html($post, 'featured-' . uniqid() . '-ratings'); ?>
									</div>
								</div>
								<?php } ?>

								<?php if ($postOption->date) { ?>
								<div class="eb-gallery-date">
									<time><?php echo $post->getDisplayDate($postOption->dateSource)->format(JText::_('DATE_FORMAT_LC3'));?></time>
								</div>
								<?php } ?>

								<?php if ($postOption->readmore) { ?>
								<div class="eb-gallery-more">
									<a href="<?php echo $post->getPermalink();?>" aria-label="<?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?>: <?php echo $this->fd->html('str.escape', $post->getTitle());?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>


		<div class="eb-gallery-foot">

			<div class="eb-gallery-foot__content">
				<div class="swiper-container gallery-thumbs" data-thumbs
				data-free-mode="1"
				data-space-between="10"
				data-watch-slides-visibility="1"
				data-watch-slides-progress="1">
					<div class="swiper-wrapper">
					<?php $i = 0; ?>
					<?php foreach ($posts as $post) { ?>
						<div class="swiper-slide">
							<div class="eb-gallery-slide-item ">
								<div class="eb-gallery-slide-item__img">
									<div class="eb-gallery-menu-thumb" style="background-image: url('<?php echo $post->coverImage;?>');"></div>
								</div>

							</div>
						</div>
						<?php $i++; ?>
					<?php } ?>
					</div>
				</div>
			</div>

			<?php if (count($posts) > 1) { ?>
				<div class="eb-gallery-foot__btn-group">
					<div class="eb-gallery-buttons">
						<div class="eb-gallery-button eb-gallery-prev-button eb-gallery-button--disabled" data-featured-previous>
							<i class="fdi fa fa-angle-left"></i>
						</div>
						<div class="eb-gallery-button eb-gallery-prev-button" data-featured-next>
							<i class="fdi fa fa-angle-right"></i>
						</div>
					</div>
				</div>

			<?php } ?>
		</div>
	</div>
</div>
