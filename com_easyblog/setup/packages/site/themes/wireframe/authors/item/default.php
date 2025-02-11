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

<?php if ($author->custom_css && $author->getAcl()->get('custom_css')) { ?>
<style type="text/css">
<?php echo $author->custom_css;?>
</style>
<?php } ?>

<?php if ($this->params->get('author_header', true)) { ?>
	<?php echo $this->html('headers.author', $author, [
		'name' => $this->params->get('author_name', true),
		'avatar' => $this->params->get('author_avatar', true),
		'rss' => $author->id != $this->my->id,
		'subscription' => $author->id != $this->my->id,
		'twitter' => $this->params->get('author_twitter', true),
		'website' => $this->params->get('author_website', true),
		'biography' => $this->params->get('author_bio', true),
		'isActivateBioTruncation' => $this->params->get('author_truncate_bio', true),
		'featureAction' => false
	]); ?>
<?php } ?>

<div data-blog-listings>
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
		<?php foreach ($posts as $post) { ?>
			<?php echo $this->html('post.list.item', $post, $postStyles->post, $index, $this->params, $return); ?>
			<?php $index++; ?>
		<?php } ?>
	<?php } ?>
	</div>

	<?php echo EB::renderModule('easyblog-after-entries'); ?>

	<?php if (!$posts) { ?>
		<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
	<?php } ?>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination;?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
