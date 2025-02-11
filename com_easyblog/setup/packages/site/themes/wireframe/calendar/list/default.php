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
<div class="t-mb--lg">
	<?php echo $this->html('snackbar.standard', $date->format('F') . ', ' . $date->format('Y'), [
		call_user_func(function() use ($date) {

			ob_start();
			?>
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar&layout=calendarView&month=' . $date->format('m') . '&year=' . $date->format('Y'));?>" class="eb-calendar-topbar__toggle"><?php echo JText::_('COM_EASYBLOG_SWITCH_TO_CALENDAR_VIEW');?></a>
			<?php
			$contents = ob_get_contents();
			ob_end_clean();

			return $contents;
		})
	]);?>
</div>

<div class="eb-calendar">
	<div class="eb-simple-posts <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
		<?php foreach ($posts as $post) { ?>
			<?php echo $this->html('post.list.simple', $post, 'created', 'DATE_FORMAT_LC1'); ?>
		<?php } ?>
	</div>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination->getPagesLinks();?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
