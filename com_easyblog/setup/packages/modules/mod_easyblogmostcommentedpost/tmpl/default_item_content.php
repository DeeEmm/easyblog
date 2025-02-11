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
<?php if ($post->posttype == 'quote') { ?>
	<div class="eb-mod-hold-quote eb-mod-quote">
		<a href="<?php echo $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo nl2br($post->title);?></a>
		<div><?php echo  $post->content; ?></div>
	</div>
<?php } ?>

<?php if ($post->posttype == 'video' && !empty($post->videos) && $params->get('video_show', false)) { ?>
	<div class="eb-mod-hold">
	<?php foreach ($post->videos as $video) { ?>
		<div class="eb-mod-hold-video eb-mod-responsive-video">
			<?php echo $video->html; ?>
		</div>
	<?php } ?>
	</div>
<?php } ?>

<?php if (in_array($post->posttype, array('twitter', 'link'))) { ?>
	<div class="eb-mod-title">
		<a href="<?php echo $post->posttype == 'link'? $post->getAsset('link')->getValue() : $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo $post->title;?></a>
	</div>
<?php } else { ?>

	<?php if ($params->get('photo_show', true) && $post->cover) { ?>
		<div class="eb-mod-thumb <?php echo $photoAlignment ? " is-" . $photoAlignment : '';?> <?php echo (isset($photoLayout->full) && $photoLayout->full) ? "is-full" : '';?>">
			<?php if ($post->isCoverImage) { ?>
				<?php if (isset($photoLayout->crop) && $photoLayout->crop) { ?>
					<a href="<?php echo $post->getPermalink();?>" class="eb-mod-image-cover" 
						title="<?php echo EB::themes()->escape($post->getImageTitle());?>"
						style="
							background-image: url('<?php echo $post->cover;?>');
							<?php if (isset($photoLayout->full) && $photoLayout->full) { ?>
							width: 100%;
							<?php } else { ?>
							width: <?php echo $photoLayout->width;?>px;
							<?php } ?>
							height: <?php echo $photoLayout->height;?>px;"
					>
						<img class="hide" src="<?php echo $post->cover;?>" alt="<?php echo $post->title;?>" />
					</a>
				<?php } else { ?>
					<a href="<?php echo $post->getPermalink();?>" class="eb-mod-image" 
						title="<?php echo EB::themes()->escape($post->getImageTitle());?>" 
						style="
							<?php if (isset($photoLayout->full) && $photoLayout->full) { ?>
							width: 100%;
							<?php } else { ?>
							width: <?php echo (isset($photoLayout->width)) ? $photoLayout->width : '260';?>px;
							<?php } ?>"
					>
						<img src="<?php echo $post->cover;?>" alt="<?php echo $post->title;?>" />
					</a>
				<?php } ?>
			<?php } ?>
			<?php if (!$post->isCoverImage) { ?>
				<?php if ($post->isEmbedCover()) { ?>
					<div class="o-aspect-ratio" style="--aspect-ratio: 16/9;">
						<?php echo $post->videoEmbedCover; ?>
					</div>
				<?php } else { ?>
					<?php echo $post->videoCover; ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="eb-mod-title">
		<a href="<?php echo $post->getPermalink(); ?>" class="eb-mod-media-title" title="<?php echo EB::themes()->escape($post->title); ?>"><?php echo $post->title;?></a>
	</div>
<?php } ?>

<?php if ($params->get('showcategory', true)) { ?>
	<?php foreach ($post->getCategories() as $category) { ?>
		<div class="mod-post-type">
			 <a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle(); ?></a>
		</div>
	 <?php } ?>
<?php } ?>

<?php if ($post->posttype != 'quote' && $params->get('showintro', '-1') != '-1') { ?>
	<div class="eb-mod-body">
		<?php if ($post->protect) { ?>
			<?php echo $post->content; ?>
		<?php } else { ?>
			<?php echo $post->summary; ?>
		<?php } ?>
	</div>
<?php } ?>