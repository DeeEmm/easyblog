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
<div class="db-stream-graph">
	<div  data-chart-reactions style="height: 200px; width: 100%;"></div>
	<div data-chart-reactions-legend></div>
</div>

<?php if ($reactions) { ?>
	<div class="divide-y divide-solid divide-gray-200">
		<?php foreach ($reactions as $reaction) { ?>
			<div class="py-sm leading-sm">
				<div class="flex overflow-hidden">
					<div class="dash-stream-headline flex-grow min-w-0 overflow-hidden truncate whitespace-nowrap">
						<i class="eb-emoji-icon eb-emoji-icon--sm eb-emoji-icon--<?php echo $reaction->type;?>"></i>
						<?php echo JText::sprintf('COM_EASYBLOG_REACTIONS_USER_REACTED_ON_THE_POST', $reaction->user->getName(), $reaction->post->title); ?>
					</div>
					<div class="text-gray-500 ml-auto flex-shrink-0 pl-md">
						<span class="ml-sm">
							<i class="fdi far fa-clock"></i>&nbsp; <?php echo $this->fd->html('str.date', $reaction->created, JText::_('Y-m-d H:i'));?>
						</span>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } else { ?>
	<div class="o-empty block">
		<div class="o-empty__content">
			<div class="o-empty__text">
				<?php echo JText::_('COM_EASYBLOG_NO_REACTIONS_CURRENTLY');?>
			</div>
		</div>
	</div>
<?php } ?>
