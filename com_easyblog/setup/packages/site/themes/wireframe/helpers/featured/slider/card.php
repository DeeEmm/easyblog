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
<div class="eb-featured <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div class="eb-showcases-card" data-eb-slider="card" data-autoplay="<?php echo $slider->autoplay;?>" data-interval="<?php echo $slider->autoplayInterval;?>" data-free-mode="0">
		<?php if ($slider->navigation && count($posts) > 1) { ?>
		<ol class="eb-showcase-indicators carousel-indicators reset-list text-center">
			<?php for ($i = 0; $i < count($posts); $i++) { ?>
				<li data-eb-slider-custom-pagination data-index="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
			<?php } ?>
		</ol>
		<?php } ?>

		<div class="swiper-container" data-container>
			<div class="swiper-wrapper">
				<?php foreach ($posts as $post) { ?>
					<div class="swiper-slide">
						<div class="eb-card is-featured <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
							<?php if ($postOption->image && $post->coverImage) { ?>
							<div class="eb-card__hd">
								<div class="o-aspect-ratio" style="--aspect-ratio: 16/9;height: 100%;">
									<div class="" style="
										background-image: url('<?php echo $post->coverImage;?>');
										background-position: center;
									 ">
									</div>
								</div>
							</div>
							<?php } ?>

							<div class="eb-card__content">
								<div class="eb-card__bd eb-card--border">
									<?php if ($postOption->title) { ?>
									<a href="<?php echo $post->getPermalink();?>">
										<h2 class="eb-card__title">
											<?php echo $post->title;?>
										</h2>
									</a>
									<?php } ?>

									<?php if ($postOption->content) { ?>
									<div class="eb-card__bd-content">
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
									<div class="eb-post-more mt-20">
										<a class="btn btn-default" href="<?php echo $post->getPermalink();?>" aria-label="<?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?>: <?php echo $this->fd->html('str.escape', $post->getTitle());?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
									</div>
									<?php } ?>
								</div>

								<div class="eb-card__ft">
									<div class="eb-card__ft-content eb-card--border">
										<div class="t-d--flex t-align-items--c">
											<div class="eb-post-meta t-flex-grow--1 t-min-width--0 t-d--flex t-flex-wrap--w t-mb--no">
												<?php if ($postOption->date) { ?>
												<div class="eb-post-date">
													<time><?php echo $post->getDisplayDate($postOption->dateSource)->format(JText::_('DATE_FORMAT_LC1'));?></time>
												</div>
												<?php } ?>

												<?php if ($postOption->category) { ?>
												<div>
													<div class="eb-post-category comma-seperator">
														<?php foreach ($post->getCategories() as $category) { ?>
														<span>
															<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
														</span>
														<?php } ?>
													</div>
												</div>
												<?php } ?>
											</div>
											<div class="t-flex-shrink--0">
												<?php if ($postOption->authorAvatar) { ?>
													<?php echo $this->html('avatar.user', $post->creator); ?>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
