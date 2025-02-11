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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>" class="<?php echo !$tags ? 'is-empty' : '';?>" data-eb-dashboard-tags>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_TAGS',  call_user_func(function() {
				if (!$this->acl->get('create_tag') && !FH::isSiteAdmin()) {
					return [];
				}

				return [
					'icon' => 'fdi fa fa-tag',
					'text' => 'COM_EASYBLOG_ADD_TAG_BUTTON',
					'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=tagForm', false),
					'style' => 'primary'
				];
			})
		),
		$this->fd->html('form.dropdown', 'tagActions', '', [
				'' => 'COM_EASYBLOG_BULK_ACTIONS',
				'tags.delete' => (object) ['title' => 'COM_EASYBLOG_DELETE', 'attr' => 'data-confirmation="site/views/dashboard/confirmDeleteTag"']
			], ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'search', $search)
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE', 'COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE_HINT', [
		'icon' => 'fdi fa fa-tag'
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

				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_LANGUAGE');?>
				</td>

				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS');?>
				</td>
			</tr>
		</thead>

		<?php if ($tags) { ?>
		<tbody>
			<?php foreach ($tags as $tag) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $tag->id); ?>
				</td>
				<td>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tagForm&id=' . $tag->id, true);?>" class="post-title"><?php echo $tag->title;?></a>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $tag->id;?>">
						<li>
							<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id); ?>" target="_blank" data-eb-action>
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="site/views/dashboard/confirmDeleteTag" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
							</a>
						</li>
					</ul>
				</td>

				<td class="text-center" width="15%">
					<?php echo $tag->language == '' ? 'All' : $tag->language;?>
				</td>

				<td class="text-center" width="15%">
					<?php echo $tag->post_count;?>
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

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="id" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="tags" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
