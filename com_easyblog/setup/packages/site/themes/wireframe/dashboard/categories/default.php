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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="<?php echo !$categories ? 'is-empty' : ''; ?>" data-eb-dashboard-categories>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_CATEGORIES', [
			'icon' => 'fdi far fa-folder',
			'text' => 'COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE',
			'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm', false),
			'style' => 'primary'
		]),
		$this->fd->html('form.dropdown', 'categoryActions', '', call_user_func(function() {
			$options = [
				'' => 'COM_EASYBLOG_BULK_ACTIONS',
				'categories.publish' => (object) [
					'title' => 'COM_EASYBLOG_PUBLISH',
					'attr' => 'data-confirmation="site/views/categories/confirmPublishCategory"'
				],
				'categories.unpublish' => (object) [
					'title' => 'COM_EASYBLOG_UNPUBLISH',
					'attr' => 'data-confirmation="site/views/categories/confirmUnpublishCategory"'
				]
			];

			if ($this->acl->get('delete_category') || FH::isSiteAdmin()) {
				$options['categories.delete'] = (object) [
					'title' => 'COM_EASYBLOG_DELETE',
					'attr' => 'data-confirmation="site/views/categories/confirmDeleteCategory"'
				];
			}

			return $options;
		}), ['attr' => 'data-eb-table-task']),
		$this->html('snackbar.search', 'post-search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY', 'COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_HINT', [
		'icon' => 'fdi far fa-folder',
		'button' => $this->fd->html('button.link', EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm', false), 'COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE', 'primary')
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
				<td class="text-center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DEFAULT');?>
				</td>
				<td class="text-center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS');?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_SUBCATEGORIES');?>
				</td>
			</tr>
		</thead>

		<?php if ($categories) { ?>
		<tbody>
			<?php foreach ($categories as $category) { ?>
			<tr data-eb-actions data-id="<?php echo $category->id; ?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $category->id); ?>
				</td>
				<td>
					<a href="<?php echo $category->getEditPermalink();?>" class="post-title"><?php echo $category->getTitle();?></a>

					<ul class="post-actions mt-5">
						<li>
							<a href="<?php echo $category->getPermalink();?>" target="_blank" data-eb-action>
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<?php if (!$category->isDefault()) { ?>
						<li>
							<?php if ($category->published) { ?>
							<a href="javascript:void(0)" data-eb-action="site/views/categories/confirmUnpublishCategory" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?></a>
							<?php } else { ?>
							<a href="javascript:void(0)" data-eb-action="site/views/categories/confirmPublishCategory" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?></a>
							<?php } ?>
						</li>
						<?php } ?>
						<?php if ($category->canDelete()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/categories/confirmDeleteCategory" data-type="dialog" class="text-danger">
								<?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center" width="10%">
					<?php if (FH::isSiteAdmin()) { ?>
					<a class="eb-star-<?php echo $category->isDefault() ? 'featured' : 'default'; ?>" href="javascript:void(0)" title="<?php echo JText::_('COM_EASYBLOG_MAKE_DEFAULT_BUTTON'); ?>" data-eb-action="site/views/categories/confirmSetDefault" data-type="dialog">
						<i class="fdi fa fa-star"></i>
					</a>
					<?php } else { ?>
					<span class="eb-star-<?php echo $category->isDefault() ? 'featured' : 'default'; ?>">
						<i class="fdi fa fa-star"></i>
					</span>
					<?php } ?>
				</td>
				<td class="text-center" width="10%">
					<?php if ($category->published) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo $category->getPostCount();?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo $category->getChildCount();?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php } ?>
	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="categories" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
