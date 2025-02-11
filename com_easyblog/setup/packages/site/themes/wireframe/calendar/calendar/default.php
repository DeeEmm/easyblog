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
<table class="eb-calendar table table-bordered" width="100%" border="1">
	<tbody>
		<tr class="eb-calendar-control">
			<td class="eb-calendar-previous text-center">
				<a class="btn-previous-month" href="javascript:void(0);" data-calendar-previous data-timestamp="<?php echo $calendar->previous;?>">
					<i class="fdi fa fa-chevron-left"></i>
				</a>
			</td>
			<td class="eb-calendar-month text-center" colspan="5">
				<a class="btn-select-month" href="<?php echo EB::_('index.php?option=com_easyblog&view=calendar&year=' . $date->year . '&month=' . $date->month);?>">
					<?php echo $date->display;?>
				</a>
			</td>
			<td class="eb-calendar-next text-center">
				<a class="btn-next-month" href="javascript:void(0);" data-calendar-next data-timestamp="<?php echo $calendar->next;?>">
					<i class="fdi fa fa-chevron-right"></i>
				</a>
			</td>
		</tr>

		<tr class="eb-calendar-days">
			<?php if ($this->config->get('main_start_of_week') == 'sunday') { ?>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('SUN'); ?></span>
				<span class="hidden" title="<?php echo JText::_('SUN'); ?>"><?php echo JText::_('S'); ?></span>
			</td>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('MON'); ?></span>
				<span class="hidden" title="<?php echo JText::_('MON'); ?>"><?php echo JText::_('M'); ?></span>
			</td>
			<?php } ?>

			<?php if ($this->config->get('main_start_of_week') == 'monday') { ?>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('MON'); ?></span>
				<span class="hidden" title="<?php echo JText::_('MON'); ?>"><?php echo JText::_('M'); ?></span>
			</td>
			<?php } ?>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('TUE'); ?></span>
				<span class="hidden" title="<?php echo JText::_('TUE'); ?>"><?php echo JText::_('T'); ?></span>
			</td>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('WED'); ?></span>
				<span class="hidden" title="<?php echo JText::_('WED'); ?>"><?php echo JText::_('W'); ?></span>
			</td>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('THU'); ?></span>
				<span class="hidden" title="<?php echo JText::_('THU'); ?>"><?php echo JText::_('T'); ?></span>
			</td>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('FRI'); ?></span>
				<span class="hidden" title="<?php echo JText::_('FRI'); ?>"><?php echo JText::_('F'); ?></span>
			</td>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('SAT'); ?></span>
				<span class="hidden" title="<?php echo JText::_('SAT'); ?>"><?php echo JText::_('S'); ?></span>
			</td>
			<?php if ($this->config->get('main_start_of_week') == 'monday') { ?>
			<td class="text-center day-of-week">
				<span><?php echo JText::_('SUN'); ?></span>
				<span class="hidden" title="<?php echo JText::_('SUN'); ?>"><?php echo JText::_('S'); ?></span>
			</td>
			<?php } ?>

		</tr>

		<tr>
			<?php $current = 1; ?>
			<?php while ($calendar->blank) { ?>
				<td class="empty">
					<small class="other-day"></small>
				</td>
				<?php $calendar->blank--;?>
				<?php $current++; ?>
			<?php } ?>

			<?php $dayNumber = 1; ?>

			<?php while ($dayNumber <= $calendar->days_in_month) { ?>

				<?php $blogs = $posts->{$date->year}->{$date->month}->{$dayNumber}; ?>
				<?php $count = is_array($blogs) ? count($blogs) : 0; ?>

				<td class="day<?php echo $count ? ' has-posts' : '';?>">
					<div onclick="void(0)"><!-- Quickfix make calendar tooltips able to show in touch devices -->
						<?php echo $dayNumber;?>

						<?php if ($count) { ?>
						<b><?php echo JText::sprintf('COM_EASYBLOG_CALENDAR_POST_COUNT', $count);?></b>

						<div class="eb-calendar-tooltips">
							<span>
								<i class="fdi fa fa-calendar"></i>
								&nbsp; <?php echo EB::date($dayNumber . '-' . $date->month . '-' . $date->year)->format(JText::_('COM_EASYBLOG_CALENDAR_DATE_FORMAT'));?>
							</span>

							<?php foreach ($blogs as $row) { ?>
							<div>
								<?php echo $this->html('post.icon', $row->getType(), 'text-muted'); ?>
								&nbsp; <a href="<?php echo $row->getPermalink();?>"><?php echo $row->title;?></a>
							</div>
							<?php } ?>
						</div>
						<?php } ?>

					</div>
				</td>
				<?php $dayNumber++; ?>
				<?php $current++; ?>

				<?php if ($current > 7) { ?>
				</tr>
				<tr>
					<?php $current = 1; ?>
				<?php } ?>
			<?php } ?>

			<?php while ($current > 1 && $current <= 7) { ?>
				<td class="empty">
					<small class="other-day"></small>
				</td>
				<?php $current++; ?>
			<?php } ?>
		</tr>
	</tbody>
</table>
