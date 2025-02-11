<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-search">
	<form class="form-horizontal" action="<?php echo JRoute::_('index.php');?>" method="post">
		<div class="uk-grid" uk-grid="">
			<div class="uk-width-expand@m">
				<div class="uk-inline uk-width-1-1">
					<span class="uk-form-icon uk-icon" uk-icon="icon: search"></span>
					<input type="text" class="uk-input" name="query" value="<?php echo $this->fd->html('str.escape', $query);?>" />
				</div>
			</div>
			<div class="uk-width-auto@m">
				<div class="uk-grid uk-grid-small" uk-grid="">
					<div class="uk-width-expand@m uk-first-column">
						<?php echo $categoryDropdown; ?>
					</div>
					<div class="uk-width-auto@m ">
						<button type="submit" class="uk-button uk-button-default uk-height-1-1	">
							<?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<br />

		<?php if ($query) { ?>
		<div class="form-control-static <?php echo !$posts ? ' eb-empty' : '';?>">

			<?php if ($posts) { ?>
				<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_TOTAL_RESULT', $pagination->get('pages.current'), $pagination->get('pages.total'), $pagination->get('total'), $query); ?>
			<?php } else { ?>
				<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_EMPTY', $query); ?>
			<?php } ?>

		</div>
		<?php } ?>

		<?php echo $this->fd->html('form.action', 'search.query'); ?>
	</form>
</div>

<hr />

<?php if ($posts) { ?>
<div class="eb-posts eb-posts-search">
	<?php foreach ($posts as $post) { ?>
	<div class="eb-post clearfix">
		<div class="eb-post-content">
			<h2 class="eb-post-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
			</h2>

			<div class="eb-post-article">
				<?php echo $post->content;?>
			</div>

			<div class="eb-post-meta text-muted">
				<div class="eb-post-date">
					<i class="fdi far fa-clock"></i>
					<time><?php echo $this->fd->html('str.date', $post->created, JText::_('DATE_FORMAT_LC'));?></time>
				</div>

				<div class="eb-post-author">
					<i class="fdi fa fa-user"></i>
					<a href="<?php echo $post->getAuthor()->getPermalink();?>"><?php echo $post->getAuthor()->getName(); ?></a>
				</div>

				<?php foreach ($post->categories as $category) { ?>
				<div>
					<div class="eb-post-category comma-seperator">
						<i class="fdi far fa-folder-open"></i>

						<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<div>
	<?php echo $pagination->getPagesLinks();?>
</div>
<?php } ?>
