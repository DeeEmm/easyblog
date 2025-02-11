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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $search, 'search', ['tooltip' => 'COM_EB_SEARCH_TOOLTIP_BLOGGERS']); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE'); ?>
					</th>

					<th class="center" width="5%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($articles) { ?>
					<?php $i = 0; ?>

					<?php foreach ($articles as $article) { ?>
					<tr data-item data-id="<?php echo $article->id;?>" data-title="<?php echo $article->title;?>">
						<td>
							<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $article->id;?>','<?php echo addslashes($this->escape($article->title));?>');"><?php echo $article->title;?></a>
						</td>

						<td class="center">
							<?php echo $article->id; ?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="12" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->fd->html('form.action'); ?>
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browse" value="1" />
	<input type="hidden" name="browsefunction" value="<?php echo $browsefunction;?>" />
	<input type="hidden" name="view" value="articles" />
</form>
