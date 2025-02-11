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
<form action="index.php" method="post" id="adminForm" name="adminForm" data-fd-grid data-override-checkbox="0">

	<div class="panel-table">
		<table class="app-table app-table-middle" data-table-grid>
		<thead>
			<tr>
				<th width="1%" class="center">
					&nbsp;
				</th>
				<th>
					<?php echo JText::_('COM_EASYBLOG_THEME_NAME');?>
				</th>
				<th class="center" width="10%">
					&nbsp;
				</th>
				<th class="center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_THEME_DEFAULT');?>
				</th>
				<th class="center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_THEME_VERSION');?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; ?>
			<?php foreach ($themes as $theme) { ?>
			<tr>
				<td class="center pr-xs">
					<input type="radio" name="cid[]" value="<?php echo $theme->element;?>" data-fd-table-id="easyblog" />
				</td>
				<td>
					<div class="pull-left">
						<?php if ($theme->config) { ?>
						<a href="index.php?option=com_easyblog&view=themes&layout=settings&id=<?php echo $theme->element;?>">
						<?php } ?>

						<?php echo JText::_($this->escape($theme->name));?>

						<?php if ($theme->config) { ?>
						</a>
						<?php } ?>
					</div>
				</td>
				<td class="center">
					<a href="index.php?option=com_easyblog&view=themes&layout=editor&element=<?php echo strtolower($theme->name);?>" class="btn btn-default">
						<i class="fdi far fa-edit"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_THEMES_EDIT_FILES'); ?>
					</a>
				</div>
				<td class="center">
					<?php echo $this->html('grid.featured', $theme, 'themes', 'default', 'themes.setDefault', $theme->element == $this->config->get('layout_theme') ? false : true, array(JText::_('COM_EASYBLOG_THEME_SET_DEFAULT'), '')); ?>
				</td>
				<td class="center">
					<?php echo $theme->version; ?>
				</td>
			</tr>
				<?php $i += 1; ?>
			<?php }?>
		</tbody>
		</table>
	</div>

	<?php echo $this->fd->html('form.action', '', '', ''); ?>
</form>
