<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
$date = EB::date($i . '-' . $m . '-01');
$currentDate = $date->format('F');
?>
<?php if ($i < $currentYear || ($i == $currentYear && $m <= $currentMonth && !$params->get('showfuture')) || $params->get('showfuture')) { ?>
   <?php if (($showEmptyMonth) || (!$showEmptyMonth && !empty($postCounts->$i->$m))) { ?>
		<?php $monthSEF = (strlen($m) < 2) ? '0' . $m : $m; ?>
		<?php if (!isset($postCounts->$i->$m)) { ?>
		<div class="eb-mod-item mod-month empty-month">
			<?php echo $currentDate;?>
		</div>
		<?php } else { ?>
		<div class="eb-mod-item">
			<a class="eb-mod-media-thumb" href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar&year='.$i.'&month='.$m .$catUrl . $filterUrl); ?>"
			<?php if($defaultYear == $i && $defaultMonth == $m) echo 'style="font-weight:700;"'; ?>>
				<?php echo $currentDate;?>
				<span class="mod-post-count">(<?php echo $postCounts->$i->$m; ?>)</span>
			</a>
		</div>
		<?php } ?>
	<?php } ?>
<?php } ?>
