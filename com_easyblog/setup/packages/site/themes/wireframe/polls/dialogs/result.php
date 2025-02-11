<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>700</width>
	<height>340</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{dialogTitle}": "[data-eb-dialog-title]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo $poll->title; ?></title>
	<content>
		<div class="t-h--100">
			<table class="eb-table table table-hover">
				<thead>
					<tr>
						<td>
							<?php echo JText::_('COM_EB_POLL_DIALOG_ITEMS_COLUMN'); ?>
						</td>
						<td width="15%" class="text-center center narrow-hide">
							<?php echo JText::_('COM_EB_POLL_DIALOG_VOTES_COLUMN'); ?>
						</td>
					</tr>
				</thead>

				<tbody>
					<?php foreach($items as $item) { ?>
						<tr>
							<td class="text-left">
								<span>
									<?php echo $item->value; ?>
								</span>
							</td>

							<td class="text-center">
								<span>
									<?php echo $item->count; ?>
								</span>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
