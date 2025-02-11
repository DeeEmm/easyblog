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
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_DIALOG_TITLE_RECENT_VOTERS'); ?></title>
	<content>
		<ul class="eb-rating-voters reset-list">
			<?php if ($votes) { ?>
				<?php foreach ($votes as $vote) { ?>
					<?php if ($vote->user) { ?>
					<li>
						<div class="row-table align-top">
							<div class="col-cell cell-tight">
								<?php echo $this->fd->html('avatar.default', $vote->user->getAvatar()); ?>
							</div>
							<div class="col-cell">
								<a href="<?php echo $vote->user->getProfileLink();?>"><?php echo $vote->user->getName();?></a>
								<span><?php echo JText::_('COM_EASYBLOG_VOTED_ON_ENTRY');?> <?php echo $this->formatDate(JText::_('DATE_FORMAT_LC1'), $vote->created);?></span>
							</div>
						</div>
					</li>
					<?php } ?>
				<?php } ?>

				<?php if ($totalGuests > 0) { ?>
					<li>
						<div>
							<span><?php echo $this->getNouns('COM_EASYBLOG_GUEST_VOTES_ENTRY', $totalGuests, true); ?>
						</div>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
