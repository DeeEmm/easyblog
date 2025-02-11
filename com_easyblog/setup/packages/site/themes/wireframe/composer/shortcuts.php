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
<?php
$metaKey = '<kbd>' . EB::getMetakey() . '</kbd>';
?>
<div class="eb-shortcuts-modal" data-composer-shortcuts>
	<div class="eb-shortcuts-modal__inner">
		<div class="eb-shortcuts-modal__dialog">
			<div class="eb-shortcuts-modal__content">
				<div class="eb-shortcuts-modal__hd">
					<a href="javascript:void(0);" class="eb-shortcuts-modal__close" data-shortcuts-close>×</a>
				</div>
				<div class="eb-shortcuts-modal__bd">
					<div class="eb-shortcuts-modal__title">
						<?php echo JText::_('COM_EB_EDITOR');?>
					</div>
					<table class="eb-shortcut-table t-lg-mb--lg">
						<tbody>
							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_OPEN_MEDIA_MANAGER');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>m</kbd>
									</div>
								</td>
							</tr>

							<?php if ($this->config->get('layout_editor') != 'composer') { ?>
							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_EMBED_VIDEOS');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>v</kbd>
									</div>
								</td>
							</tr>
							<?php } ?>

							<?php if ($this->config->get('layout_editor') === 'composer') { ?>
							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_INSERT_NEW_BLOCK');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>i</kbd>
									</div>
								</td>
							</tr>
							<?php } ?>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_TOGGLE_POST_COVER');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>c</kbd>
									</div>
								</td>
							</tr>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_INSERT_POST');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>p</kbd>
									</div>
								</td>
							</tr>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_INSERT_LOCATION');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>l</kbd>
									</div>
								</td>
							</tr>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_TOGGLE_SIDEBAR');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>\</kbd>
									</div>
								</td>
							</tr>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_POST_REVISIONS');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>h</kbd>
									</div>
								</td>
							</tr>

							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_TOGGLE_THIS_WINDOW');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>shift</kbd> + <kbd>/</kbd>
									</div>
								</td>
							</tr>
							<tr>
								<td class="eb-shortcut-table__name">
									<?php echo JText::_('COM_EB_HIDE_THIS_WINDOW');?>
								</td>
								<td>
									<div class="eb-shortcut-labels">
										<kbd>esc</kbd>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

					<?php if ($this->config->get('layout_editor') === 'composer') { ?>
					<div class="eb-shortcuts-modal__title">
						<?php echo JText::_('COM_EB_BLOCKS');?> &mdash; When block is active
					</div>
					<table class="eb-shortcut-table t-lg-mb--lg">
						<tbody>
						<tr>
							<td class="eb-shortcut-table__name">
								<?php echo JText::_('COM_EB_DUPLICATE_BLOCK');?>
							</td>
							<td>
								<div class="eb-shortcut-labels">
									<?php echo $metaKey;?> <kbd>shift</kbd> <kbd>d</kbd>
								</div>
							</td>
						</tr>
						<tr>
							<td class="eb-shortcut-table__name">
								<?php echo JText::_('COM_EB_DELETE_BLOCK');?>
							</td>
							<td>
								<div class="eb-shortcut-labels">
									<?php echo $metaKey;?> <kbd>shift</kbd> <kbd>backspace</kbd>
								</div>
							</td>
						</tr>
						<tr>
							<td class="eb-shortcut-table__name">
								<?php echo JText::_('COM_EB_MOVE_BLOCK');?>
							</td>
							<td>
								<div class="eb-shortcut-labels">
									<?php echo $metaKey;?> <kbd>shift</kbd> <kbd>m</kbd>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
