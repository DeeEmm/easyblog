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
<?php echo EB::renderModule('easyblog-before-team-header'); ?>

<?php if ($this->my->guest && $team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED) { ?>
	<div class="eb-empty">
		<i class="fdi fa fa-info-circle"></i>
		<?php echo JText::_('COM_EB_TEAMBLOGS_FOR_REGISTERED_USERS_ONLY');?>
	</div>
<?php } elseif ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember) { ?>
	<div class="eb-empty">
		<i class="fdi fa fa-info-circle"></i>
		<?php echo JText::_('COM_EB_TEAMBLOGS_FOR_MEMBERS_ONLY');?>
	</div>
<?php } else { ?>

	<div class="eb-author eb-author-teamblog" data-team-item data-id="<?php echo $team->id;?>">
		<?php echo $this->html('headers.team', $team); ?>
	</div>

	<?php echo EB::renderModule('easyblog-after-team-header'); ?>

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

		<?php if (!$posts) { ?>
			<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_BLOG_ENTRY'); ?>
		<?php } ?>
	</div>

	<?php if ($pagination) {?>
		<?php echo EB::renderModule('easyblog-before-pagination'); ?>

		<?php echo $pagination;?>

		<?php echo EB::renderModule('easyblog-after-pagination'); ?>
	<?php } ?>
<?php } ?>


