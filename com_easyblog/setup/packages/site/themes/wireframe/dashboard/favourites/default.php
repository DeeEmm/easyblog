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
<form method="post" action="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=favourites');?>" class="eb-dashboard-entries <?php echo !$posts ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EB_FAVOURITE_POSTS'),
		$this->fd->html('form.dropdown', 'favoriteActions', '', [
				'' => 'COM_EASYBLOG_BULK_ACTIONS',
				'posts.unfavourite' => 'COM_EB_UNFAVOURITE_POST'
			], ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EB_EMPTY_FAVOURITES_POSTS', 'COM_EB_EMPTY_FAVOURITES_POSTS_HINT', [
		'icon' => 'fdi fa fa-star'
	]); ?>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>" class="<?php echo $post->isPending() ? 'is-pending': ''; ?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id); ?>
				</td>
				<td>
					<a href="<?php echo $post->getPermalink();?>" class="post-title"><?php echo $post->getTitle();?></a>

					<div class="post-meta">
						<span>
							<a href="<?php echo $post->creator->getPermalink();?>"><?php echo $post->getAuthorName();?></a>
						</span>
						<span>
							<?php foreach ($post->categories as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>
					</div>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $post->id;?>">
						<li>
							<a href="<?php echo $post->getPermalink();?>" target="_blank" data-eb-action>
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="posts.unfavourite" data-type="form">
								<?php echo JText::_('COM_EB_UNFAVOURITE_POST');?>
							</a>
						</li>
					</ul>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="sort" value="" />
	<input type="hidden" name="ordering" value="" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="favourites" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
