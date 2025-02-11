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
<form method="post" action="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>" class="eb-dashboard-entries <?php echo !$posts ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_POSTS'),
		$this->fd->html('form.dropdown', 'trashActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS',
					'posts.restore' => 'COM_EASYBLOG_RESTORE'
				];

				if ($this->acl->get('delete_entry')) {
					$options['posts.deletePermanent'] = (object) [
						'title' => 'COM_EASYBLOG_DELETE_PERMANENT',
						'attr' => 'data-confirmation="site/views/dashboard/confirmPermanentDelete"'
					];
				}

				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search),
		[
			$this->html('dashboard.filters', $state, [
				'all' => 'COM_EASYBLOG_FILTER_SELECT_FILTER',
				'publish' => 'COM_EASYBLOG_FILTER_PUBLISHED',
				'unpublish' => 'COM_EASYBLOG_FILTER_UNPUBLISHED',
				'pending' => 'COM_EASYBLOG_FILTER_UNDER_REVIEW',
				'scheduled' => 'COM_EASYBLOG_FILTER_SCHEDULED',
				'drafts' => 'COM_EASYBLOG_FILTER_DRAFT',
				'trash' => 'COM_EASYBLOG_FILTER_TRASHED'
			]),
			$categoryDropdown
		]
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_TRASH_EMPTY', 'COM_EASYBLOG_DASHBOARD_TRASH_EMPTY_HINT', [
		'icon' => 'fdi fa fa-trash-alt'
	]); ?>

	<table class="eb-table table table-striped table-hover" data-table>
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center narrow-hide">
					<?php echo $this->html('dashboard.sort', 'COM_EASYBLOG_TABLE_COLUMN_HITS', 'hits', $ordering, $sort); ?>
				</td>
				<td width="15%" class="text-center">
					<?php echo $this->html('dashboard.sort', 'COM_EASYBLOG_TABLE_COLUMN_DATE', 'created', $ordering, $sort); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id); ?>
				</td>
				<td>
					<?php if ($post->canEdit() || FH::isSiteAdmin()) { ?>
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>" class="post-title"><?php echo $post->getTitle();?></a>
					<?php } else { ?>
						<label class="post-title"><?php echo $post->getTitle();?></label>
					<?php } ?>


					<div class="post-meta">
						<span>
							<a href="<?php echo $post->getAuthorPermalink();?>"><?php echo $post->getAuthorName();?></a>
						</span>

						<span>
							<?php foreach ($post->getCategories() as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fdi fa fa-language"></i>&nbsp; <?php echo $post->language;?>
						</span>
						<?php } ?>
					</div>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $post->id;?>">

						<li>
							<a href="javascript:void(0);" data-eb-action="posts.restore" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_RESTORE'); ?>
							</a>
						</li>

						<?php if ($this->acl->get('delete_entry')) { ?>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="site/views/dashboard/confirmPermanentDelete" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DELETE_PERMANENT'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>

				<td class="text-center narrow-hide" width="10%">
					<?php echo $post->hits;?>
				</td>

				<td class="text-center" width="15%">
					<?php echo $post->getCreationDate(true)->format(JText::_('Y-m-d H:i'));?>
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
	<input type="hidden" name="id" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="entries" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
