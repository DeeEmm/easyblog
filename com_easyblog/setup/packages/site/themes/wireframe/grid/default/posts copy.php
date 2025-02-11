	<div class="eb-post-listing__item" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?> data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
		<div class="eb-blog-grid__content">
			<?php if ($this->params->get('grid_show_cover', true)) { ?>
				<?php if (EB::image()->isImage($post->getImage())) { ?>
					<div class="eb-blog-grid__thumb">
						<a class="eb-blog-grid-image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $post->getImage(EB::getCoverSize('cover_size'));?>');">
							<?php if ($post->isFeatured()) { ?>
							<span class="eb-blog-grid-label">
								<i class="fdi fa fa-bookmark"></i>
							</span>
							<?php } ?>
						</a>
					</div>
				<?php } else { ?>
					<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
				<?php } ?>
			<?php } ?>

			<div class="eb-blog-grid__title">
				<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>
			</div>

			<!-- Grid meta -->
			<div class="eb-blog-grid__meta eb-blog-grid__meta--text">

				<?php if ($this->params->get('grid_show_author_avatar', false)) { ?>
					<?php echo $this->html('avatar.user', $post->getAuthor(), 'sm'); ?>
				<?php } ?>

				<?php if ($this->params->get('grid_show_author', true)) { ?>
				<div class="eb-blog-grid-author">
					<a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName(); ?></a>
				</div>
				<?php } ?>

				<?php if ($this->params->get('grid_show_category', true)) { ?>
				<div class="eb-blog-grid-category">
					<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>
				</div>
				<?php } ?>
			</div>
			<?php if ($this->params->get('grid_show_intro', true)) { ?>
			<div class="eb-blog-grid__body" data-blog-posts-item-content>
				<?php if ($this->config->get('layout_dropcaps')) { ?>
				<p class="has-drop-cap">
				<?php } ?>
					<?php echo $post->getIntro(true, $gridTruncation, 'intro', null, array('forceTruncateByChars' => true, 'forceCharsLimit' => $this->params->get('grid_content_limit', 350))); ?>
				<?php if ($this->config->get('layout_dropcaps')) { ?>
				</p>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($this->params->get('grid_show_readmore', false)) { ?>
			<div class="eb-post-more mt-20">
				<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
			</div>
			<?php } ?>
			<?php if ($this->params->get('grid_show_date', true)) { ?>
			<div class="eb-blog-grid__foot">
				<time class="eb-blog-grid-meta-date">
					<?php echo $post->getDisplayDate($this->params->get('grid_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
				</time>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php $index++; ?>
	<?php } ?>
