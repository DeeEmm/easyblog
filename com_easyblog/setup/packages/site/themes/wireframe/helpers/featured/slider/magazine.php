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
<div class="eb-featured eb-featured--magazine <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div class="eb-gallery-stage" data-eb-slider data-autoplay="<?php echo $slider->autoplay;?>" data-interval="<?php echo $slider->autoplayInterval;?>">
		<div class="eb-gallery-viewport">
			<div class="swiper-container" data-container>
				<div class="swiper-wrapper">
					<?php foreach ($posts as $post) { ?>
						<div class="swiper-slide">
							<div class="eb-gallery-item">
								<div class="eb-gallery-box">
									<?php if ($postOption->image && $post->coverImage) { ?>
										<div class="eb-gallery-thumb eb-mod-thumb">
											<!-- TODO -->
											<a href="<?php echo $post->getPermalink(); ?>" class="eb-gallery-cover__img"
											style="
												background-image: url('<?php echo $post->coverImage;?>') !important;
												background-size: cover;
												background-repeat: no-repeat;
												background-position: 50% 50%;
												padding-bottom: 400px;"
											></a>
										</div>
									<?php } ?>

									<div class="eb-gallery-content <?php echo !$postOption->image ? 'no-cover' : ''; ?>">
										<?php if ($postOption->authorAvatar) { ?>
											<?php echo $this->html('avatar.user', $post->getAuthor()); ?>
										<?php } ?>

										<?php if ($postOption->title) { ?>
										<a href="<?php echo $post->getPermalink();?>">
											<h2 class="eb-gallery-content__title"><?php echo $post->title;?></h2>
										</a>
										<?php } ?>

										<?php if ($postOption->content) { ?>
										<div class="eb-gallery-content__article">
											<span>
												<?php echo $post->displayContent; ?>
											</span>
										</div>
										<?php } ?>

										<div class="eb-gallery-content__meta eb-gallery-content__meta--text">
											<?php if ($postOption->authorTitle) { ?>
												<div class="eb-gallery-author">
													<span>
														<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>"><?php echo $post->getAuthor()->getName(); ?></a>
													</span>
												</div>
											<?php } ?>

											<?php if ($postOption->category) { ?>
											<div class="eb-gallery-category">
												<?php foreach ($post->getCategories() as $category) { ?>
												<span>
													<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
												</span>
												<?php } ?>
											</div>
											<?php } ?>

											<?php if ($postOption->date) { ?>
											<div class="eb-gallery-date">
												<time><?php echo $post->getDisplayDate($postOption->dateSource)->format(JText::_('DATE_FORMAT_LC3'));?></time>
											</div>
											<?php } ?>

											<?php if ($postOption->ratings) { ?>
											<div class="eb-gallery-rating" data-ratings>
												<div class="">
													<?php echo EB::ratings()->html($post, 'featured-' . uniqid() . '-ratings'); ?>
												</div>
											</div>
											<?php } ?>
										</div>

										<div class="eb-gallery-content__more">
											<?php if ($postOption->readmore) { ?>
												<a href="<?php echo $post->getPermalink();?>" class="mod-btn mod-btn-more" aria-label="<?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?>: <?php echo $this->fd->html('str.escape', $post->getTitle());?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php if (count($posts) > 1) { ?>
		<div class="eb-gallery-indicators">
			<ol class="eb-gallery-buttons swiper-pagination-clickable swiper-pagination-bullets" data-eb-slider-pagination>
				<?php $i = 0; ?>
				<?php foreach ($posts as $post) { ?>
					<li class="eb-gallery-menu-item <?php echo $i ==0 ? 'active' : ''; ?>"></li>
					<?php $i++; ?>
				<?php } ?>
			</ol>
		</div>
		<?php } ?>


	</div>
</div>
