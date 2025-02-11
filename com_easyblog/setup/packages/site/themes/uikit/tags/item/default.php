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
<?php if ($this->params->get('tag_header', true) && ($this->params->get('tag_title', true) || $this->params->get('tag_subscribe_rss', true))) { ?>
	<?php echo $this->html('headers.tag', $tag, [
		'title' => $this->params->get('tag_title', true),
		'rss' => $this->params->get('tag_subscribe_rss', true)
	]); ?>
<?php } ?>

<div data-blog-listings>
	<?php if ($private > 0 || $team > 0) { ?>
		<?php if ($private > 0 && $team > 0) { ?>
			<?php echo $this->html('post.list.emptyList', JText::sprintf('COM_EASYBLOG_TAG_PRIVATE_AND_TEAM_BLOG_INFO', $private, $team)); ?>
		<?php } elseif ($private > 0) { ?>
			<?php echo $this->html('post.list.emptyList', $this->getNouns('COM_EASYBLOG_TAG_PRIVATE_BLOG', $private, true)); ?>
		<?php } elseif ($team > 0) { ?>
			<?php echo $this->html('post.list.emptyList', $this->getNouns('COM_EASYBLOG_TAG_TEAM_BLOG_INFO', $team, true)); ?>
		<?php } ?>
	<?php } ?>

	<?php echo EB::renderModule('easyblog-before-entries');?>

	<div class="eb-post-listing
		<?php echo $postStyles->row === 'row' ? 'is-row' : '';?>
		<?php echo $postStyles->row === 'column' && $postStyles->column === 'column' ? 'is-column ' : '';?>
		<?php echo $postStyles->row === 'column' && $postStyles->column === 'masonry' ? 'is-masonry ' : '';?>
		<?php echo $postStyles->row === 'column' ? 'eb-post-listing--col-' . $postStyles->columns : '';?>
		<?php echo $postStyles->row === 'row' && $this->params->get('row_divider', true) ? 'has-divider' : '';?>
		<?php echo $this->isMobile() ? 'is-mobile' : '';?>
		"
		data-blog-posts
	>
		<?php if ($posts) { ?>
			<?php $index = 0; ?>
			<div class="uk-child-width-1-2@m uk-grid uk-grid-stack">
			<?php foreach ($posts as $post) { ?>
				<!-- Determine if post custom fields should appear or not in tag listings -->
				<?php if (!$this->params->get('tag_post_customfields')) { ?>
					<?php $post->fields = '';?>
				<?php } ?>

				<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return); ?>
				<?php $index++; ?>
			<?php } ?>
			</div>
		<?php } ?>
	</div>

	<?php echo EB::renderModule('easyblog-after-entries'); ?>

	<?php if (!$posts) { ?>
		<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
	<?php } ?>
</div>

<?php if ($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination->getPagesLinks();?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
