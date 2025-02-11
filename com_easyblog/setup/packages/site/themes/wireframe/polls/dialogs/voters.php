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
	<width>400</width>
	<height>120</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{loadMore}": "[data-poll-voters-load]",
		"{voterList}": "[data-poll-voters-list]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_POLL_VOTERS_TITLE'); ?></title>
	<content>
		<div class="t-text--center t-d--flex t-align-items--cx t-justify-content--c t-h--100">
			<?php if ($voters) { ?>
				<div class="t-w--100">
					<div data-poll-voters-list class="eb-poll-voter-list l-cluster">
						<div>
							<?php echo $this->output('site/polls/item/voters', ['voters' => $voters]); ?>
						</div>
					</div>
					<?php if ($total && $total > $limit) { ?>
						<div class="t-text--center t-mt--lg">
							<a data-poll-voters-load data-nextlimit="<?php echo $limit; ?>" class="btn btn-default btn-sm t-lg-mt--lg" href="javascript:void(0);">
								<?php echo JText::_('COM_EB_POLL_LOAD_MORE'); ?>
							</a>

							<div class="o-loader"></div>
						</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<span class="t-align-self--c"><?php echo JText::_('COM_EB_POLL_NO_VOTER'); ?></span>
			<?php } ?>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
