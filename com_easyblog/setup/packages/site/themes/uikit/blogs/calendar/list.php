<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-calendar-topbar">
	<div class="eb-calendar-topbar__date">
		<?php echo $date->format('F');?>, <?php echo $date->format('Y');?>
	</div>
	<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar&layout=calendarView&month=' . $date->format('m') . '&year=' . $date->format('Y'));?>" class="eb-calendar-topbar__toggle"><?php echo JText::_('COM_EASYBLOG_SWITCH_TO_CALENDAR_VIEW');?></a>
</div>

<div class="xeb-calendar xeb-calendar-list <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<ul class="uk-list uk-list-divider uk-margin-small">
		<?php foreach ($posts as $post) { ?>
		<li>
			<div class="uk-grid-small" uk-grid>
				<div class="uk-width-expand" uk-leader>
					<a href="<?php echo $post->getPermalink(); ?>">
						<span uk-icon="icon: file-text" class="uk-margin-small-right"></span> 
						<span><?php echo $post->title;?></span>
					</a>
				</div>
				<div>
					<time ><?php echo EB::date($post->created)->format(JText::_('DATE_FORMAT_LC1')); ?></time>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination->getPagesLinks();?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
